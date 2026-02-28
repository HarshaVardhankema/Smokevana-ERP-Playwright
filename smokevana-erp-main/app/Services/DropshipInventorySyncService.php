<?php

namespace App\Services;

use App\Product;
use App\Variation;
use App\Models\WpVendor;
use App\Models\VariationVendor;
use App\VariationLocationDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * DropshipInventorySyncService
 * 
 * Handles the inventory sync flow for ERP dropshipping:
 * 
 * Vendor Portal → ERP Inventory → eCommerce
 * 
 * Key responsibilities:
 * - Sync vendor stock to ERP inventory (vendor is source of truth)
 * - Update variation_location_details with vendor stock
 * - Maintain stock synchronization across the system
 * - Handle variant-level inventory management
 */
class DropshipInventorySyncService
{
    /**
     * Sync vendor stock to ERP inventory for a product
     * The vendor's stock becomes the source of truth for dropshipped products
     * 
     * @param int $productId
     * @param int $vendorId
     * @param float $stockQty
     * @return bool
     */
    public function syncVendorStockToERP($productId, $vendorId, $stockQty)
    {
        try {
            $product = Product::with('variations')->find($productId);
            
            if (!$product) {
                Log::warning('Product not found for stock sync', ['product_id' => $productId]);
                return false;
            }

            // Only sync dropshipped products
            if ($product->product_source_type !== 'dropshipped') {
                Log::info('Product is not dropshipped, skipping ERP sync', ['product_id' => $productId]);
                return true; // Not an error, just skip
            }

            $businessId = $product->business_id;
            $locationId = $this->getDefaultLocationId($businessId);

            if (!$locationId) {
                Log::warning('No default location found for business', ['business_id' => $businessId]);
                return false;
            }

            DB::beginTransaction();

            // Update stock for all variations of this product
            foreach ($product->variations as $variation) {
                // Update or create variation_location_details
                // Update BOTH qty_available AND in_stock_qty (ERP displays in_stock_qty)
                VariationLocationDetails::updateOrCreate(
                    [
                        'variation_id' => $variation->id,
                        'product_id' => $productId,
                        'location_id' => $locationId,
                    ],
                    [
                        'qty_available' => $stockQty,
                        'in_stock_qty' => $stockQty,
                        'product_variation_id' => $variation->product_variation_id,
                    ]
                );
            }

            DB::commit();

            Log::info('Vendor stock synced to ERP inventory', [
                'product_id' => $productId,
                'vendor_id' => $vendorId,
                'stock_qty' => $stockQty,
                'location_id' => $locationId,
                'variations_updated' => $product->variations->count()
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to sync vendor stock to ERP', [
                'product_id' => $productId,
                'vendor_id' => $vendorId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Sync stock for a specific variation
     * 
     * @param int $variationId
     * @param int $productId
     * @param float $stockQty
     * @return bool
     */
    public function syncVariationStock($variationId, $productId, $stockQty)
    {
        try {
            $variation = Variation::find($variationId);
            $product = Product::find($productId);

            if (!$variation || !$product) {
                return false;
            }

            $locationId = $this->getDefaultLocationId($product->business_id);
            if (!$locationId) {
                return false;
            }

            // Update BOTH qty_available AND in_stock_qty (ERP displays in_stock_qty)
            VariationLocationDetails::updateOrCreate(
                [
                    'variation_id' => $variationId,
                    'product_id' => $productId,
                    'location_id' => $locationId,
                ],
                [
                    'qty_available' => $stockQty,
                    'in_stock_qty' => $stockQty,
                    'product_variation_id' => $variation->product_variation_id,
                ]
            );

            Log::info('Variation stock synced to ERP', [
                'variation_id' => $variationId,
                'stock_qty' => $stockQty
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to sync variation stock', [
                'variation_id' => $variationId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Bulk sync stock for multiple products
     * 
     * @param int $vendorId
     * @param array $stockData Array of [product_id => stock_qty]
     * @return array Results
     */
    public function bulkSyncVendorStock($vendorId, $stockData)
    {
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($stockData as $productId => $stockQty) {
            $success = $this->syncVendorStockToERP($productId, $vendorId, $stockQty);
            
            if ($success) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = "Product {$productId} failed to sync";
            }
        }

        return $results;
    }

    /**
     * Get the default location ID for a business
     * 
     * @param int $businessId
     * @return int|null
     */
    protected function getDefaultLocationId($businessId)
    {
        $location = \App\BusinessLocation::where('business_id', $businessId)
            ->where('is_active', 1)
            ->orderBy('id')
            ->first();

        return $location ? $location->id : null;
    }

    /**
     * Get current ERP stock for a product
     * 
     * @param int $productId
     * @return float
     */
    public function getERPStock($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return 0;
        }

        $locationId = $this->getDefaultLocationId($product->business_id);
        if (!$locationId) {
            return 0;
        }

        $totalStock = VariationLocationDetails::where('product_id', $productId)
            ->where('location_id', $locationId)
            ->sum('qty_available');

        return $totalStock ?? 0;
    }

    /**
     * Verify sync status between vendor stock and ERP
     * 
     * @param int $productId
     * @param int $vendorId
     * @return array
     */
    public function verifySyncStatus($productId, $vendorId)
    {
        $product = Product::find($productId);
        $vendor = WpVendor::find($vendorId);

        if (!$product || !$vendor) {
            return ['in_sync' => false, 'error' => 'Product or vendor not found'];
        }

        // Get vendor stock from pivot
        $vendorStock = DB::table('products_wp_vendors_table_pivot')
            ->where('product_id', $productId)
            ->where('wp_vendor_id', $vendorId)
            ->value('vendor_stock_qty') ?? 0;

        // Get ERP stock
        $erpStock = $this->getERPStock($productId);

        return [
            'in_sync' => abs($vendorStock - $erpStock) < 0.01, // Account for float precision
            'vendor_stock' => $vendorStock,
            'erp_stock' => $erpStock,
            'difference' => $vendorStock - $erpStock,
        ];
    }

    // =========================================================================
    // VARIANT-LEVEL SYNC METHODS
    // =========================================================================

    /**
     * Sync variation stock from vendor to ERP inventory
     * This is called when vendor updates stock for a specific variation
     * 
     * @param int $variationId
     * @param int $vendorId
     * @param float $stockQty
     * @return bool
     */
    public function syncVariationStockToERP($variationId, $vendorId, $stockQty)
    {
        try {
            $variation = Variation::with('product')->find($variationId);
            
            if (!$variation || !$variation->product) {
                Log::warning('Variation not found for ERP sync', ['variation_id' => $variationId]);
                return false;
            }

            $product = $variation->product;
            
            // Only sync dropshipped products
            if ($product->product_source_type !== 'dropshipped') {
                Log::info('Product is not dropshipped, skipping variation ERP sync', [
                    'product_id' => $product->id,
                    'variation_id' => $variationId
                ]);
                return true;
            }

            $businessId = $product->business_id;
            $locationId = $this->getDefaultLocationId($businessId);

            if (!$locationId) {
                Log::warning('No default location found for business', ['business_id' => $businessId]);
                return false;
            }

            // Update variation_location_details for this specific variation
            VariationLocationDetails::updateOrCreate(
                [
                    'variation_id' => $variationId,
                    'product_id' => $product->id,
                    'location_id' => $locationId,
                ],
                [
                    'qty_available' => $stockQty,
                    'in_stock_qty' => $stockQty,
                    'product_variation_id' => $variation->product_variation_id,
                ]
            );

            Log::info('Variation stock synced to ERP inventory', [
                'variation_id' => $variationId,
                'vendor_id' => $vendorId,
                'stock_qty' => $stockQty,
                'location_id' => $locationId,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to sync variation stock to ERP', [
                'variation_id' => $variationId,
                'vendor_id' => $vendorId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Sync all pending variation updates for a vendor
     * 
     * @param int $vendorId
     * @return array Results
     */
    public function processPendingVariationSyncs($vendorId)
    {
        $results = ['erp_synced' => 0, 'ecommerce_synced' => 0, 'failed' => 0];

        try {
            // Get all variations needing ERP sync
            $pendingErp = VariationVendor::where('wp_vendor_id', $vendorId)
                ->where('needs_erp_sync', true)
                ->get();

            foreach ($pendingErp as $mapping) {
                $success = $this->syncVariationStockToERP(
                    $mapping->variation_id,
                    $vendorId,
                    $mapping->vendor_stock_qty
                );

                if ($success) {
                    $mapping->markErpSynced();
                    $results['erp_synced']++;
                } else {
                    $results['failed']++;
                }
            }

            Log::info('Processed pending variation syncs for vendor', [
                'vendor_id' => $vendorId,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process pending variation syncs', [
                'vendor_id' => $vendorId,
                'error' => $e->getMessage()
            ]);
        }

        return $results;
    }

    /**
     * Bulk update variations from vendor portal
     * 
     * @param int $vendorId
     * @param array $data Array of [variation_id => ['cost' => x, 'stock' => y]]
     * @return array Results
     */
    public function bulkUpdateVariations($vendorId, $data)
    {
        $results = ['updated' => 0, 'failed' => 0, 'errors' => []];

        DB::beginTransaction();
        try {
            foreach ($data as $variationId => $values) {
                $mapping = VariationVendor::where('variation_id', $variationId)
                    ->where('wp_vendor_id', $vendorId)
                    ->first();

                if (!$mapping) {
                    $results['failed']++;
                    $results['errors'][] = "Variation {$variationId} not mapped to vendor";
                    continue;
                }

                // Update cost if provided
                if (isset($values['cost']) && $values['cost'] !== null) {
                    $mapping->updateCostPrice($values['cost']);
                }

                // Update stock if provided
                if (isset($values['stock']) && $values['stock'] !== null) {
                    $mapping->updateStock($values['stock']);
                }

                $results['updated']++;
            }

            DB::commit();

            // Process pending syncs
            $this->processPendingVariationSyncs($vendorId);

            return $results;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk variation update failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get variation stock summary for a vendor
     * 
     * @param int $vendorId
     * @return array
     */
    public function getVendorVariationStockSummary($vendorId)
    {
        $mappings = VariationVendor::where('wp_vendor_id', $vendorId)->get();

        $lowStockThreshold = 10;

        return [
            'total' => $mappings->count(),
            'in_stock' => $mappings->filter(fn($m) => $m->vendor_stock_qty > ($m->low_stock_threshold ?? $lowStockThreshold))->count(),
            'low_stock' => $mappings->filter(fn($m) => $m->vendor_stock_qty > 0 && $m->vendor_stock_qty <= ($m->low_stock_threshold ?? $lowStockThreshold))->count(),
            'out_of_stock' => $mappings->filter(fn($m) => $m->vendor_stock_qty <= 0)->count(),
        ];
    }

    // =========================================================================
    // ORDER FULFILLMENT - STOCK DEDUCTION METHODS
    // =========================================================================

    /**
     * Deduct stock when a dropship order is fulfilled (shipped)
     * 
     * @param \App\Models\DropshipOrderTracking $order The dropship order tracking record
     * @param int $vendorId The vendor fulfilling the order
     * @return array Results with success/failure details
     */
    public function deductStockOnOrderFulfillment($order, $vendorId)
    {
        $results = [
            'success' => true,
            'deducted' => [],
            'failed' => [],
            'total_items' => 0,
            'total_quantity' => 0
        ];

        DB::beginTransaction();
        try {
            $transaction = $order->transaction;
            if (!$transaction) {
                throw new \Exception('Transaction not found for order');
            }

            $sellLines = $transaction->sell_lines()
                ->with(['product', 'variations'])
                ->get();

            if ($sellLines->isEmpty()) {
                Log::warning('No sell lines found for order fulfillment stock deduction', [
                    'order_id' => $order->id,
                    'transaction_id' => $transaction->id
                ]);
                DB::commit();
                return $results;
            }

            foreach ($sellLines as $sellLine) {
                $variationId = $sellLine->variation_id;
                $productId = $sellLine->product_id;
                $quantity = $sellLine->quantity;

                $results['total_items']++;
                $results['total_quantity'] += $quantity;

                try {
                    $vendorStockDeducted = $this->deductVendorStock($variationId, $vendorId, $quantity);
                    
                    if (!$vendorStockDeducted) {
                        $actualVendorId = $this->findVariationVendor($variationId, $productId);
                        if ($actualVendorId && $actualVendorId != $vendorId) {
                            $vendorStockDeducted = $this->deductVendorStock($variationId, $actualVendorId, $quantity);
                        }
                    }
                    
                    $erpStockDeducted = $this->deductERPStock($variationId, $productId, $quantity);

                    if ($erpStockDeducted) {
                        $results['deducted'][] = [
                            'variation_id' => $variationId,
                            'product_id' => $productId,
                            'quantity' => $quantity,
                            'product_name' => $sellLine->product->name ?? 'Unknown',
                            'variation_name' => $sellLine->variations->name ?? 'N/A',
                            'vendor_stock_deducted' => $vendorStockDeducted
                        ];
                    } else {
                        $results['failed'][] = [
                            'variation_id' => $variationId,
                            'product_id' => $productId,
                            'quantity' => $quantity,
                            'reason' => 'ERP stock deduction failed'
                        ];
                    }
                } catch (\Exception $e) {
                    $results['failed'][] = [
                        'variation_id' => $variationId,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'reason' => $e->getMessage()
                    ];
                    Log::error('Failed to deduct stock for sell line', [
                        'variation_id' => $variationId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $order->update(['stock_deducted' => true]);

            DB::commit();

            $results['success'] = empty($results['failed']);

            Log::info('Order fulfillment stock deduction completed', [
                'order_id' => $order->id,
                'vendor_id' => $vendorId,
                'items_deducted' => count($results['deducted']),
                'items_failed' => count($results['failed']),
                'total_quantity' => $results['total_quantity']
            ]);

            return $results;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order fulfillment stock deduction failed', [
                'order_id' => $order->id,
                'vendor_id' => $vendorId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'deducted' => [],
                'failed' => []
            ];
        }
    }

    /**
     * Find the vendor ID for a variation/product
     */
    protected function findVariationVendor($variationId, $productId)
    {
        $vendorId = DB::table('variation_vendor_pivot')
            ->where('variation_id', $variationId)
            ->value('wp_vendor_id');

        if ($vendorId) {
            return $vendorId;
        }

        $vendorId = DB::table('products_wp_vendors_table_pivot')
            ->where('product_id', $productId)
            ->value('wp_vendor_id');

        return $vendorId;
    }

    /**
     * Deduct stock from vendor's inventory (variation_vendor_pivot)
     */
    protected function deductVendorStock($variationId, $vendorId, $quantity)
    {
        try {
            $mapping = VariationVendor::where('variation_id', $variationId)
                ->where('wp_vendor_id', $vendorId)
                ->first();

            if ($mapping) {
                $mapping->reduceStock($quantity);
                
                Log::info('Vendor stock deducted', [
                    'variation_id' => $variationId,
                    'vendor_id' => $vendorId,
                    'quantity_deducted' => $quantity,
                    'new_stock' => $mapping->vendor_stock_qty
                ]);
                
                return true;
            }

            // Fallback: Try product-level pivot table
            $productPivot = DB::table('products_wp_vendors_table_pivot')
                ->join('variations', 'products_wp_vendors_table_pivot.product_id', '=', 'variations.product_id')
                ->where('variations.id', $variationId)
                ->where('products_wp_vendors_table_pivot.wp_vendor_id', $vendorId)
                ->first();

            if ($productPivot) {
                $currentStock = $productPivot->vendor_stock_qty ?? 0;
                $newStock = max(0, $currentStock - $quantity);

                DB::table('products_wp_vendors_table_pivot')
                    ->where('product_id', $productPivot->product_id)
                    ->where('wp_vendor_id', $vendorId)
                    ->update([
                        'vendor_stock_qty' => $newStock,
                        'stock_last_updated' => now(),
                        'status' => $newStock > 0 ? 'active' : 'out_of_stock'
                    ]);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to deduct vendor stock', [
                'variation_id' => $variationId,
                'vendor_id' => $vendorId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Deduct stock from ERP inventory (variation_location_details)
     */
    protected function deductERPStock($variationId, $productId, $quantity)
    {
        try {
            $product = Product::find($productId);
            if (!$product) {
                return false;
            }

            $locationId = $this->getDefaultLocationId($product->business_id);
            if (!$locationId) {
                return false;
            }

            $vld = VariationLocationDetails::where('variation_id', $variationId)
                ->where('product_id', $productId)
                ->where('location_id', $locationId)
                ->first();

            if ($vld) {
                $newQtyAvailable = max(0, $vld->qty_available - $quantity);
                $newInStockQty = max(0, $vld->in_stock_qty - $quantity);

                $vld->update([
                    'qty_available' => $newQtyAvailable,
                    'in_stock_qty' => $newInStockQty
                ]);

                Log::info('ERP stock deducted', [
                    'variation_id' => $variationId,
                    'location_id' => $locationId,
                    'quantity_deducted' => $quantity,
                    'new_qty_available' => $newQtyAvailable
                ]);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to deduct ERP stock', [
                'variation_id' => $variationId,
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Check if order has already had stock deducted
     */
    public function isStockAlreadyDeducted($order)
    {
        return (bool) $order->stock_deducted;
    }

    /**
     * Restore stock when an order is cancelled or returned
     */
    public function restoreStockOnOrderCancellation($order, $vendorId)
    {
        $results = [
            'success' => true,
            'restored' => [],
            'failed' => []
        ];

        if (!$order->stock_deducted) {
            return $results;
        }

        DB::beginTransaction();
        try {
            $transaction = $order->transaction;
            if (!$transaction) {
                throw new \Exception('Transaction not found');
            }

            $sellLines = $transaction->sell_lines()
                ->with(['product', 'variations'])
                ->get();

            foreach ($sellLines as $sellLine) {
                $variationId = $sellLine->variation_id;
                $productId = $sellLine->product_id;
                $quantity = $sellLine->quantity;

                try {
                    $mapping = VariationVendor::where('variation_id', $variationId)
                        ->where('wp_vendor_id', $vendorId)
                        ->first();

                    if ($mapping) {
                        $mapping->increaseStock($quantity);
                    }

                    $this->restoreERPStock($variationId, $productId, $quantity);

                    $results['restored'][] = [
                        'variation_id' => $variationId,
                        'quantity' => $quantity
                    ];

                } catch (\Exception $e) {
                    $results['failed'][] = [
                        'variation_id' => $variationId,
                        'error' => $e->getMessage()
                    ];
                }
            }

            $order->update(['stock_deducted' => false]);

            DB::commit();

            return $results;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to restore stock on cancellation', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Restore ERP stock (used during cancellation/return)
     */
    protected function restoreERPStock($variationId, $productId, $quantity)
    {
        $product = Product::find($productId);
        if (!$product) return false;

        $locationId = $this->getDefaultLocationId($product->business_id);
        if (!$locationId) return false;

        $vld = VariationLocationDetails::where('variation_id', $variationId)
            ->where('product_id', $productId)
            ->where('location_id', $locationId)
            ->first();

        if ($vld) {
            $vld->update([
                'qty_available' => $vld->qty_available + $quantity,
                'in_stock_qty' => $vld->in_stock_qty + $quantity
            ]);
            return true;
        }

        return false;
    }
}

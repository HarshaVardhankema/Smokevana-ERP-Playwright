<?php

namespace App\Jobs;

use App\Transaction;
use App\TransactionSellLine;
use App\Utils\TransactionUtil;
use App\Utils\OrderNumberGenerator;
use App\Models\WpVendor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DropshipOrderTracking;
use App\Jobs\WooCommerceWebhookSaleOrder;

class SplitOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderId;

    /**
     * Create a new job instance.
     *
     * @param mixed $order Transaction model or order ID
     * @return void
     */
    public function __construct($order)
    {
        // Accept either a Transaction model or an ID
        if ($order instanceof Transaction) {
            $this->orderId = $order->id;
        } else {
            $this->orderId = $order;
        }
    }

    /**
     * Execute the job.
     * Enhanced with dropshipping preprocessing workflow
     *
     * @return void
     */
    public function handle()
    {
        $order = Transaction::with([
            'sell_lines',
            'sell_lines.variations',
            'sell_lines.product' => function($q) {
                // Include fields needed for routing decisions
                $q->select('id', 'name', 'sku', 'slug', 'image', 'product_source_type', 'woocommerce_product_id', 'woocommerce_disable_sync');
            },
            'sell_lines.product.vendors',
        ])->find($this->orderId);

        if (!$order || $order->type !== 'sales_order') {
            return;
        }

        DB::beginTransaction();
        try {
            // Idempotency: if this SO already has split children recorded, don't split again.
            $existingSalesOrderIds = $order->sales_order_ids;
            if (is_string($existingSalesOrderIds)) {
                $decoded = json_decode($existingSalesOrderIds, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $existingSalesOrderIds = $decoded;
                }
            }
            if (is_array($existingSalesOrderIds) && count($existingSalesOrderIds) > 0) {
                Log::info('SplitOrderJob: Order already has children, skipping (idempotency check)', [
                    'order_id' => $order->id,
                    'existing_children' => $existingSalesOrderIds
                ]);
                DB::commit();
                return;
            }

            // Process ALL orders with dropship products - split regardless of source (ERP or Web)
            // Group sell_lines by vendor type:
            // 1. WooCommerce Vendors (vendor_type = 'woocommerce') - sync to WooCommerce
            // 2. ERP Dropship Vendors (vendor_type = 'erp_dropship') - portal fulfillment only
            // 3. No vendor (in-house products) - ERP fulfillment
            $woocommerceVendorGroups = [];
            $erpDropshipVendorGroups = [];
            $noVendorLines = [];

            foreach ($order->sell_lines as $sellLine) {
                $product = $sellLine->product;
                
                if (!$product) {
                    $noVendorLines[] = $sellLine;
                    continue;
                }
                
                // Check if product is marked as dropshipped
                $isDropshipped = ($product->product_source_type ?? '') === 'dropshipped';
                
                if (!$isDropshipped) {
                    // Not a dropship product - in-house fulfillment
                    $noVendorLines[] = $sellLine;
                    continue;
                }
                
                // Get all dropship-capable vendors for this product
                $vendors = $product->vendors ?? collect();
                if ($vendors instanceof \Illuminate\Support\Collection) {
                    // Filter to only dropship-capable vendors (WooCommerce or ERP Dropship types)
                    $vendors = $vendors->filter(function ($v) {
                        return in_array($v->vendor_type, [WpVendor::TYPE_WOOCOMMERCE, WpVendor::TYPE_ERP_DROPSHIP]);
                    })->values();
                }
                
                if ($vendors->isEmpty()) {
                    // Dropship product but no vendor assigned - treat as in-house
                    $noVendorLines[] = $sellLine;
                    Log::warning('SplitOrderJob: Dropship product has no vendor assigned', [
                        'product_id' => $product->id,
                        'sku' => $product->sku,
                        'order_id' => $order->id
                    ]);
                } else {
                    // Route to the primary vendor (first one)
                    $vendor = $vendors->first();
                    
                    if ($vendor->vendor_type === WpVendor::TYPE_WOOCOMMERCE) {
                        // WooCommerce vendor - needs WooCommerce sync
                        // Check if product is WooCommerce synced and vendor has wp_term_id
                        $isWooSyncedProduct = !empty($product->woocommerce_product_id) && empty($product->woocommerce_disable_sync);
                        
                        if ($isWooSyncedProduct && !empty($vendor->wp_term_id)) {
                            if (!isset($woocommerceVendorGroups[$vendor->id])) {
                                $woocommerceVendorGroups[$vendor->id] = ['vendor' => $vendor, 'lines' => []];
                            }
                            $woocommerceVendorGroups[$vendor->id]['lines'][] = $sellLine;
                        } else {
                            // WooCommerce vendor but product not synced - still create dropship order but won't sync
                            if (!isset($woocommerceVendorGroups[$vendor->id])) {
                                $woocommerceVendorGroups[$vendor->id] = ['vendor' => $vendor, 'lines' => []];
                            }
                            $woocommerceVendorGroups[$vendor->id]['lines'][] = $sellLine;
                            Log::info('SplitOrderJob: WooCommerce vendor product not synced to WC', [
                                'product_id' => $product->id,
                                'vendor_id' => $vendor->id
                            ]);
                        }
                    } elseif ($vendor->vendor_type === WpVendor::TYPE_ERP_DROPSHIP) {
                        // ERP Dropship vendor - portal fulfillment (no WooCommerce sync)
                        if (!isset($erpDropshipVendorGroups[$vendor->id])) {
                            $erpDropshipVendorGroups[$vendor->id] = ['vendor' => $vendor, 'lines' => []];
                        }
                        $erpDropshipVendorGroups[$vendor->id]['lines'][] = $sellLine;
                    }
                }
            }

            // If there are no vendor groups but we have lines, treat all as no-vendor to force ERP split
            $hasVendorGroups = !empty($woocommerceVendorGroups) || !empty($erpDropshipVendorGroups);
            if (!$hasVendorGroups && empty($noVendorLines) && $order->sell_lines->count() > 0) {
                $noVendorLines = $order->sell_lines->all();
            }

            $childOrderIds = [];
            $transactionUtil = new TransactionUtil();

            // ALWAYS create child orders - even for in-house only orders
            // This ensures consistent parent-child structure for all orders
            // Parent stays as 'sales_order', child becomes 'erp_sales_order'

            $index = 1;

            // Create child orders for WooCommerce vendors (wp_sales_order) - syncs to WooCommerce
            foreach ($woocommerceVendorGroups as $vendorId => $group) {
                $childOrder = $this->createChildOrder($order, $group['lines'], $transactionUtil, 'wp_sales_order', $index);
                $childOrderIds[] = $childOrder->id;

                // Create dropship tracking record
                $this->createDropshipTracking($childOrder, $order, $vendorId, 'pending');

                // Schedule to WooCommerce sync order 
                try {
                    WooCommerceWebhookSaleOrder::dispatch($childOrder->id);
                } catch (\Exception $e) {
                    Log::warning('SplitOrderJob: Failed to dispatch WooCommerce sync', [
                        'child_order_id' => $childOrder->id,
                        'error' => $e->getMessage()
                    ]);
                }

                Log::info('SplitOrderJob: Created WooCommerce vendor child order', [
                    'child_order_id' => $childOrder->id,
                    'vendor_id' => $vendorId,
                    'vendor_name' => $group['vendor']->name ?? 'N/A',
                    'vendor_type' => 'woocommerce',
                    'parent_order_id' => $order->id,
                    'woocommerce_sync' => 'scheduled'
                ]);
                
                $index++;
            }

            // Create child orders for ERP Dropship vendors (erp_dropship_order) - NO WooCommerce sync
            foreach ($erpDropshipVendorGroups as $vendorId => $group) {
                $childOrder = $this->createChildOrder($order, $group['lines'], $transactionUtil, 'erp_dropship_order', $index);
                $childOrderIds[] = $childOrder->id;

                // Create dropship tracking record - sync_status is 'not_applicable' for ERP dropship
                $this->createDropshipTracking($childOrder, $order, $vendorId, 'not_applicable');

                // No WooCommerce sync - vendor fulfills via portal
                Log::info('SplitOrderJob: Created ERP Dropship vendor child order', [
                    'child_order_id' => $childOrder->id,
                    'vendor_id' => $vendorId,
                    'vendor_name' => $group['vendor']->name ?? 'N/A',
                    'vendor_type' => 'erp_dropship',
                    'parent_order_id' => $order->id,
                    'woocommerce_sync' => 'not_applicable'
                ]);
                
                $index++;
            }

            // Create child order for no-vendor items (erp_sales_order) - in-house fulfillment
            // This now runs for ALL in-house products, creating parent-child structure even for in-house only orders
            if (!empty($noVendorLines)) {
                $childOrder = $this->createChildOrder($order, $noVendorLines, $transactionUtil, 'erp_sales_order', $index);
                $childOrderIds[] = $childOrder->id;
                
                $isInHouseOnly = empty($woocommerceVendorGroups) && empty($erpDropshipVendorGroups);
                Log::info('SplitOrderJob: Created ERP in-house child order', [
                    'child_order_id' => $childOrder->id,
                    'child_invoice_no' => $childOrder->invoice_no,
                    'parent_order_id' => $order->id,
                    'parent_invoice_no' => $order->invoice_no,
                    'lines_count' => count($noVendorLines),
                    'is_inhouse_only_order' => $isInHouseOnly
                ]);
            }

            // Store child order IDs in main order's sales_order_ids and mark as preprocessed
            if (!empty($childOrderIds)) {
                // Get existing sales_order_ids if any, and merge with new child order IDs
                $existingIds = $order->sales_order_ids ?? [];
                if (is_string($existingIds)) {
                    $existingIds = json_decode($existingIds, true) ?: [];
                }
                $allChildIds = array_unique(array_merge($existingIds, $childOrderIds));
                $order->sales_order_ids = $allChildIds;
                // Mark parent as preprocessed so it appears in Pending instead of Preprocessing
                $order->is_preprocessed = true;
                $order->save();
                
                Log::info('SplitOrderJob: Child order IDs stored in main order', [
                    'main_order_id' => $order->id,
                    'child_order_ids' => $allChildIds,
                    'total_children' => count($allChildIds)
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SplitOrderJob failed: ' . $e->getMessage(), ['order_id' => $this->orderId, 'trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    /**
     * Create dropship tracking record
     */
    private function createDropshipTracking($childOrder, $parentOrder, $vendorId, $syncStatus)
    {
        try {
            if (class_exists(DropshipOrderTracking::class)) {
                DropshipOrderTracking::create([
                    'transaction_id' => $childOrder->id,
                    'parent_transaction_id' => $parentOrder->id,
                    'wp_vendor_id' => $vendorId,
                    'business_id' => $parentOrder->business_id,
                    'fulfillment_status' => 'pending',
                    'sync_status' => $syncStatus,
                    'order_placed_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('SplitOrderJob: Failed to create dropship tracking', [
                'child_order_id' => $childOrder->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create a child order for a vendor
     * 
     * Order Number Format (using OrderNumberGenerator):
     * - wp_sales_order     → SOWDS25010001 (WooCommerce DropShip)
     * - erp_dropship_order → SOVDS25010001 (Vendor Portal DropShip)
     * - erp_sales_order    → SOERP25010001 (ERP In-House)
     */
    private function createChildOrder($parentOrder, $sellLines, $transactionUtil, $type, $index)
    {
        // Calculate totals
        $totalBeforeTax = 0;
        $taxAmount = 0;
        $finalTotal = 0;

        foreach ($sellLines as $line) {
            $lineTotal = ($line->unit_price ?? 0) * ($line->quantity ?? 0);
            $lineTax = ($line->item_tax ?? 0) * ($line->quantity ?? 0);
            $totalBeforeTax += $lineTotal;
            $taxAmount += $lineTax;
        }
        $finalTotal = $totalBeforeTax + $taxAmount;

        // Generate order number using new standardized format
        $invoiceNo = $this->generateInvoiceNumber($parentOrder, $type, $index);

        $childOrder = Transaction::create([
            'business_id' => $parentOrder->business_id,
            'location_id' => $parentOrder->location_id,
            'contact_id' => $parentOrder->contact_id,
            'type' => $type,
            'status' => $parentOrder->status,
            'payment_status' => $parentOrder->payment_status,
            'customer_group_id' => $parentOrder->customer_group_id,
            'invoice_no' => $invoiceNo,
            'total_before_tax' => $totalBeforeTax,
            'tax_amount' => $taxAmount,
            'discount_type' => $parentOrder->discount_type,
            'discount_amount' => 0,
            'transaction_date' => $parentOrder->transaction_date,
            'final_total' => $finalTotal,
            'shipping_address' => $parentOrder->shipping_address,
            'is_direct_sale' => $parentOrder->is_direct_sale,
            'selling_price_group_id' => $parentOrder->selling_price_group_id,
            'shipping_charges' => 0,
            'additional_notes' => $parentOrder->additional_notes,
            'created_by' => $parentOrder->created_by,
            'transfer_parent_id' => $parentOrder->id,
            'shipping_first_name' => $parentOrder->shipping_first_name,
            'shipping_last_name' => $parentOrder->shipping_last_name,
            'shipping_company' => $parentOrder->shipping_company,
            'shipping_address1' => $parentOrder->shipping_address1,
            'shipping_address2' => $parentOrder->shipping_address2,
            'shipping_city' => $parentOrder->shipping_city,
            'shipping_state' => $parentOrder->shipping_state,
            'shipping_zip' => $parentOrder->shipping_zip,
            'shipping_country' => $parentOrder->shipping_country,
        ]);

        // Create sell_lines for child order
        foreach ($sellLines as $line) {
            TransactionSellLine::create([
                'transaction_id' => $childOrder->id,
                'product_id' => $line->product_id,
                'variation_id' => $line->variation_id,
                'quantity' => $line->quantity,
                'ordered_quantity' => $line->quantity, // Important: Set ordered_quantity for order fulfillment display
                'unit_price' => $line->unit_price,
                'unit_price_inc_tax' => $line->unit_price_inc_tax,
                'unit_price_before_discount' => $line->unit_price_before_discount ?? $line->unit_price,
                'item_tax' => $line->item_tax,
                'tax_id' => $line->tax_id,
                'line_discount_type' => $line->line_discount_type,
                'line_discount_amount' => $line->line_discount_amount,
                'sell_line_note' => $line->sell_line_note,
                'sub_unit_id' => $line->sub_unit_id,
            ]);
        }

        return $childOrder;
    }

    /**
     * Generate invoice number for child order
     */
    private function generateInvoiceNumber($parentOrder, $type, $index)
    {
        try {
            $orderNumberGenerator = new OrderNumberGenerator();
            
            switch ($type) {
                case 'wp_sales_order':
                    // WooCommerce Dropship: SOWDS25010001
                    return $orderNumberGenerator->generateWooDropshipOrder($parentOrder->business_id);
                case 'erp_dropship_order':
                    // Vendor Portal Dropship: SOVDS25010001
                    return $orderNumberGenerator->generateVendorDropshipOrder($parentOrder->business_id);
                case 'erp_sales_order':
                default:
                    // ERP In-House: SOERP25010001
                    return $orderNumberGenerator->generateErpOrder($parentOrder->business_id);
            }
        } catch (\Exception $e) {
            // Fallback: append child index to parent invoice number
            Log::warning('SplitOrderJob: OrderNumberGenerator failed, using fallback', [
                'error' => $e->getMessage()
            ]);
            return $parentOrder->invoice_no . '-C' . $index;
        }
    }
}

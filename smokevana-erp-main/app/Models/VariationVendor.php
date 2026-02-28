<?php

namespace App\Models;

use App\Product;
use App\Variation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * VariationVendor - Manages vendor-level data for product variations
 * 
 * This model enables:
 * - Per-variation cost price management by vendors
 * - Per-variation stock tracking
 * - Auto-calculated selling prices with markup
 * - Sync tracking for ERP and eCommerce
 */
class VariationVendor extends Model
{
    protected $table = 'variation_vendor_pivot';

    protected $guarded = ['id'];

    protected $casts = [
        'vendor_cost_price' => 'decimal:4',
        'markup_percentage' => 'decimal:2',
        'markup_amount' => 'decimal:4',
        'selling_price' => 'decimal:4',
        'vendor_stock_qty' => 'decimal:4',
        'track_stock' => 'boolean',
        'needs_erp_sync' => 'boolean',
        'needs_ecommerce_sync' => 'boolean',
        'cost_last_updated' => 'datetime',
        'price_last_updated' => 'datetime',
        'stock_last_updated' => 'datetime',
        'erp_synced_at' => 'datetime',
        'ecommerce_synced_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_OUT_OF_STOCK = 'out_of_stock';
    const STATUS_DISCONTINUED = 'discontinued';

    /**
     * Get the variation
     */
    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }

    /**
     * Get the vendor
     */
    public function vendor()
    {
        return $this->belongsTo(WpVendor::class, 'wp_vendor_id');
    }

    /**
     * Get the product (denormalized)
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for a specific vendor
     */
    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('wp_vendor_id', $vendorId);
    }

    /**
     * Scope for a specific product
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for items needing ERP sync
     */
    public function scopeNeedsErpSync($query)
    {
        return $query->where('needs_erp_sync', true);
    }

    /**
     * Scope for items needing eCommerce sync
     */
    public function scopeNeedsEcommerceSync($query)
    {
        return $query->where('needs_ecommerce_sync', true);
    }

    /**
     * Scope for low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->where('track_stock', true)
            ->whereRaw('vendor_stock_qty <= low_stock_threshold')
            ->where('vendor_stock_qty', '>', 0);
    }

    /**
     * Scope for out of stock items
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('track_stock', true)
            ->where('vendor_stock_qty', '<=', 0);
    }

    /**
     * Update vendor cost price and recalculate selling price
     * 
     * @param float $costPrice
     * @param float|null $markupPercentage Override the default markup
     * @return bool
     */
    public function updateCostPrice($costPrice, $markupPercentage = null)
    {
        $this->vendor_cost_price = $costPrice;
        $this->cost_last_updated = now();
        
        // Use provided markup or fall back to existing or vendor default
        if ($markupPercentage !== null) {
            $this->markup_percentage = $markupPercentage;
        } elseif ($this->markup_percentage === null) {
            // Get default from vendor
            $this->markup_percentage = $this->vendor->default_markup_percentage ?? 0;
        }
        
        // Recalculate selling price
        $this->recalculateSellingPrice();
        
        // Flag for sync
        $this->needs_erp_sync = true;
        $this->needs_ecommerce_sync = true;
        
        $result = $this->save();
        
        Log::info('Vendor cost price updated', [
            'variation_id' => $this->variation_id,
            'vendor_id' => $this->wp_vendor_id,
            'cost_price' => $costPrice,
            'markup' => $this->markup_percentage,
            'selling_price' => $this->selling_price
        ]);
        
        return $result;
    }

    /**
     * Recalculate selling price based on cost and markup
     */
    public function recalculateSellingPrice()
    {
        if ($this->vendor_cost_price === null) {
            return;
        }
        
        $cost = floatval($this->vendor_cost_price);
        
        if ($this->markup_percentage !== null && $this->markup_percentage > 0) {
            // Percentage markup
            $this->selling_price = $cost * (1 + ($this->markup_percentage / 100));
        } elseif ($this->markup_amount !== null && $this->markup_amount > 0) {
            // Fixed amount markup
            $this->selling_price = $cost + $this->markup_amount;
        } else {
            // No markup - selling price equals cost
            $this->selling_price = $cost;
        }
        
        $this->price_last_updated = now();
    }

    /**
     * Update stock quantity
     * 
     * @param float $quantity
     * @return bool
     */
    public function updateStock($quantity)
    {
        $oldQty = $this->vendor_stock_qty;
        $this->vendor_stock_qty = max(0, $quantity);
        $this->stock_last_updated = now();
        
        // Update status based on stock
        if ($this->track_stock) {
            if ($this->vendor_stock_qty <= 0) {
                $this->status = self::STATUS_OUT_OF_STOCK;
            } elseif ($this->status === self::STATUS_OUT_OF_STOCK) {
                $this->status = self::STATUS_ACTIVE;
            }
        }
        
        // Flag for sync
        $this->needs_erp_sync = true;
        $this->needs_ecommerce_sync = true;
        
        $result = $this->save();
        
        Log::info('Vendor stock updated', [
            'variation_id' => $this->variation_id,
            'vendor_id' => $this->wp_vendor_id,
            'old_qty' => $oldQty,
            'new_qty' => $this->vendor_stock_qty
        ]);
        
        return $result;
    }

    /**
     * Reduce stock (e.g., when order is placed)
     * 
     * @param float $quantity
     * @return bool
     */
    public function reduceStock($quantity)
    {
        return $this->updateStock($this->vendor_stock_qty - $quantity);
    }

    /**
     * Increase stock (e.g., when order is cancelled)
     * 
     * @param float $quantity
     * @return bool
     */
    public function increaseStock($quantity)
    {
        return $this->updateStock($this->vendor_stock_qty + $quantity);
    }

    /**
     * Mark as synced to ERP
     */
    public function markErpSynced()
    {
        $this->needs_erp_sync = false;
        $this->erp_synced_at = now();
        return $this->save();
    }

    /**
     * Mark as synced to eCommerce
     */
    public function markEcommerceSynced()
    {
        $this->needs_ecommerce_sync = false;
        $this->ecommerce_synced_at = now();
        return $this->save();
    }

    /**
     * Check if stock is low
     */
    public function isLowStock()
    {
        if (!$this->track_stock) {
            return false;
        }
        return $this->vendor_stock_qty <= $this->low_stock_threshold && $this->vendor_stock_qty > 0;
    }

    /**
     * Check if out of stock
     */
    public function isOutOfStock()
    {
        if (!$this->track_stock) {
            return false;
        }
        return $this->vendor_stock_qty <= 0;
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => '<span class="badge bg-success">Active</span>',
            'inactive' => '<span class="badge bg-secondary">Inactive</span>',
            'out_of_stock' => '<span class="badge bg-danger">Out of Stock</span>',
            'discontinued' => '<span class="badge bg-dark">Discontinued</span>',
        ];
        
        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Get stock status badge
     */
    public function getStockBadgeAttribute()
    {
        if (!$this->track_stock) {
            return '<span class="badge bg-secondary">Not Tracked</span>';
        }
        
        if ($this->vendor_stock_qty <= 0) {
            return '<span class="badge bg-danger">0</span>';
        }
        
        if ($this->vendor_stock_qty <= $this->low_stock_threshold) {
            return '<span class="badge bg-warning">' . intval($this->vendor_stock_qty) . '</span>';
        }
        
        return '<span class="badge bg-success">' . intval($this->vendor_stock_qty) . '</span>';
    }

    /**
     * Create or update variation-vendor mapping for a product
     * 
     * @param int $productId
     * @param int $vendorId
     * @param array $defaults Default values for new mappings
     * @return array Created/updated mappings
     */
    public static function syncProductVariationsToVendor($productId, $vendorId, $defaults = [])
    {
        $product = Product::with('variations')->find($productId);
        $vendor = WpVendor::find($vendorId);
        
        if (!$product || !$vendor) {
            return [];
        }
        
        $mappings = [];
        $defaultMarkup = $vendor->default_markup_percentage ?? 0;
        
        foreach ($product->variations as $variation) {
            $mapping = self::updateOrCreate(
                [
                    'variation_id' => $variation->id,
                    'wp_vendor_id' => $vendorId,
                ],
                array_merge([
                    'product_id' => $productId,
                    'markup_percentage' => $defaultMarkup,
                    'status' => self::STATUS_ACTIVE,
                ], $defaults)
            );
            
            $mappings[] = $mapping;
        }
        
        Log::info('Product variations synced to vendor', [
            'product_id' => $productId,
            'vendor_id' => $vendorId,
            'variations_count' => count($mappings)
        ]);
        
        return $mappings;
    }

    /**
     * Bulk update cost prices for a vendor
     * 
     * @param int $vendorId
     * @param array $data Array of ['variation_id' => cost_price]
     * @param float|null $markupPercentage
     * @return int Count of updated records
     */
    public static function bulkUpdateCostPrices($vendorId, $data, $markupPercentage = null)
    {
        $count = 0;
        
        foreach ($data as $variationId => $costPrice) {
            $mapping = self::where('variation_id', $variationId)
                ->where('wp_vendor_id', $vendorId)
                ->first();
            
            if ($mapping) {
                $mapping->updateCostPrice($costPrice, $markupPercentage);
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Bulk update stock quantities for a vendor
     * 
     * @param int $vendorId
     * @param array $data Array of ['variation_id' => quantity]
     * @return int Count of updated records
     */
    public static function bulkUpdateStock($vendorId, $data)
    {
        $count = 0;
        
        foreach ($data as $variationId => $quantity) {
            $mapping = self::where('variation_id', $variationId)
                ->where('wp_vendor_id', $vendorId)
                ->first();
            
            if ($mapping) {
                $mapping->updateStock($quantity);
                $count++;
            }
        }
        
        return $count;
    }
}

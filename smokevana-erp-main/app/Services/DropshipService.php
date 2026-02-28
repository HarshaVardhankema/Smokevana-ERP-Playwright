<?php

namespace App\Services;

use App\Models\DropshipOrderTracking;
use App\Models\WpVendor;
use App\Product;
use App\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Woocommerce\Utils\WoocommerceUtil;

class DropshipService
{
    protected $woocommerceUtil;

    public function __construct()
    {
        // WoocommerceUtil will be instantiated when needed
    }

    /**
     * Get dashboard statistics for dropshipping
     */
    public function getDashboardStats($business_id)
    {
        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();

        return [
            // Order counts
            'orders_today' => DropshipOrderTracking::forBusiness($business_id)
                ->whereDate('created_at', $today)
                ->count(),
            
            'orders_this_week' => DropshipOrderTracking::forBusiness($business_id)
                ->where('created_at', '>=', $weekStart)
                ->count(),
            
            'orders_this_month' => DropshipOrderTracking::forBusiness($business_id)
                ->where('created_at', '>=', $monthStart)
                ->count(),

            // Status counts
            'pending_orders' => DropshipOrderTracking::forBusiness($business_id)
                ->pending()
                ->count(),
            
            'shipped_orders' => DropshipOrderTracking::forBusiness($business_id)
                ->shipped()
                ->count(),
            
            'completed_orders' => DropshipOrderTracking::forBusiness($business_id)
                ->completed()
                ->count(),
            
            'failed_syncs' => DropshipOrderTracking::forBusiness($business_id)
                ->where('sync_status', 'failed')
                ->count(),

            // Vendor stats
            'active_vendors' => WpVendor::forBusiness($business_id)
                ->active()
                ->count(),
            
            'total_dropship_products' => Product::where('business_id', $business_id)
                ->dropshipped()
                ->count(),

            // Financial (this month)
            'revenue_this_month' => DropshipOrderTracking::forBusiness($business_id)
                ->where('created_at', '>=', $monthStart)
                ->where('fulfillment_status', 'completed')
                ->sum('vendor_payout_amount'),

            // Average fulfillment time (hours)
            'avg_fulfillment_time' => $this->calculateAverageFulfillmentTime($business_id),
        ];
    }

    /**
     * Calculate average fulfillment time in hours
     */
    public function calculateAverageFulfillmentTime($business_id)
    {
        $completed = DropshipOrderTracking::forBusiness($business_id)
            ->where('fulfillment_status', 'completed')
            ->whereNotNull('order_placed_at')
            ->whereNotNull('completed_at')
            ->get();

        if ($completed->isEmpty()) {
            return 0;
        }

        $totalHours = 0;
        foreach ($completed as $order) {
            $totalHours += $order->order_placed_at->diffInHours($order->completed_at);
        }

        return round($totalHours / $completed->count(), 1);
    }

    /**
     * Get orders grouped by vendor
     */
    public function getOrdersByVendor($business_id, $status = null)
    {
        $query = DropshipOrderTracking::forBusiness($business_id)
            ->with(['vendor', 'transaction', 'parentTransaction']);

        if ($status) {
            $query->where('fulfillment_status', $status);
        }

        return $query->get()->groupBy('wp_vendor_id');
    }

    /**
     * Get top performing vendors
     */
    public function getTopVendors($business_id, $limit = 5)
    {
        return WpVendor::forBusiness($business_id)
            ->active()
            ->withCount(['orders as completed_orders_count' => function ($query) {
                $query->where('fulfillment_status', 'completed');
            }])
            ->orderBy('completed_orders_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent activity for dashboard
     */
    public function getRecentActivity($business_id, $limit = 10)
    {
        return DropshipOrderTracking::forBusiness($business_id)
            ->with(['vendor', 'transaction'])
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Create dropship tracking record when order is split
     */
    public function createTrackingRecord($transaction, $parentTransaction, $vendor)
    {
        return DropshipOrderTracking::create([
            'transaction_id' => $transaction->id,
            'parent_transaction_id' => $parentTransaction->id,
            'wp_vendor_id' => $vendor->id,
            'business_id' => $transaction->business_id,
            'fulfillment_status' => 'pending',
            'sync_status' => 'pending',
            'order_placed_at' => now(),
        ]);
    }

    /**
     * Map product to vendor with pricing
     */
    public function mapProductToVendor($product_id, $vendor_id, $pricing_data = [])
    {
        $product = Product::findOrFail($product_id);
        $vendor = WpVendor::findOrFail($vendor_id);

        $pivotData = [
            'vendor_cost_price' => $pricing_data['vendor_cost_price'] ?? null,
            'vendor_markup_percentage' => $pricing_data['vendor_markup_percentage'] ?? $vendor->default_markup_percentage,
            'vendor_markup_amount' => $pricing_data['vendor_markup_amount'] ?? null,
            'vendor_sku' => $pricing_data['vendor_sku'] ?? null,
            'is_primary_vendor' => $pricing_data['is_primary_vendor'] ?? false,
            'lead_time_days' => $pricing_data['lead_time_days'] ?? 0,
            'min_order_qty' => $pricing_data['min_order_qty'] ?? 1,
            'status' => $pricing_data['status'] ?? 'active',
        ];

        // Calculate selling price if cost and markup are provided
        if ($pivotData['vendor_cost_price'] && $pivotData['vendor_markup_percentage']) {
            $pivotData['dropship_selling_price'] = $pivotData['vendor_cost_price'] * 
                (1 + ($pivotData['vendor_markup_percentage'] / 100));
        }

        // Sync the relationship
        $product->vendors()->syncWithoutDetaching([
            $vendor_id => $pivotData
        ]);

        // Update product source type if not already dropshipped
        if ($product->product_source_type !== 'dropshipped') {
            $product->update(['product_source_type' => 'dropshipped']);
        }

        return true;
    }

    /**
     * Remove product-vendor mapping
     */
    public function unmapProductFromVendor($product_id, $vendor_id)
    {
        $product = Product::findOrFail($product_id);
        $product->vendors()->detach($vendor_id);

        // If no more vendors, revert to in-house
        if ($product->vendors()->count() === 0) {
            $product->update(['product_source_type' => 'in_house']);
        }

        return true;
    }

    /**
     * Sync product to WooCommerce (ERP → WooCommerce)
     */
    public function syncProductToWooCommerce($product_id, $business_id)
    {
        $product = Product::with('vendors', 'variations', 'category')
            ->findOrFail($product_id);

        try {
            $wooUtil = app(WoocommerceUtil::class);
            
            // Format product data for WooCommerce
            $productData = $this->formatProductForWooCommerce($product);

            if ($product->woocommerce_product_id) {
                // Update existing product
                $result = $wooUtil->updateProductInWooCommerce($business_id, $product, $productData);
            } else {
                // Create new product
                $result = $wooUtil->createProductInWooCommerce($business_id, $product, $productData);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to sync product to WooCommerce', [
                'product_id' => $product_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Format product data for WooCommerce API
     */
    protected function formatProductForWooCommerce($product)
    {
        $data = [
            'name' => $product->name,
            'sku' => $product->sku,
            'description' => $product->product_description,
            'regular_price' => (string) $product->variations->first()?->sell_price_inc_tax ?? '0',
            'manage_stock' => $product->enable_stock ? true : false,
            'stock_quantity' => $product->enable_stock ? $product->variations->first()?->qty_available ?? 0 : null,
            'status' => $product->is_inactive ? 'draft' : 'publish',
        ];

        // Add category if exists
        if ($product->category && $product->category->woocommerce_cat_id) {
            $data['categories'] = [
                ['id' => $product->category->woocommerce_cat_id]
            ];
        }

        // Add image if exists
        if ($product->image) {
            $data['images'] = [
                ['src' => asset('uploads/img/' . $product->image)]
            ];
        }

        return $data;
    }

    /**
     * Update order status from WooCommerce webhook
     */
    public function handleWooCommerceStatusUpdate($woocommerce_order_id, $status, $tracking_info = null)
    {
        $tracking = DropshipOrderTracking::where('woocommerce_order_id', $woocommerce_order_id)->first();

        if (!$tracking) {
            Log::warning('No tracking record found for WooCommerce order', [
                'woocommerce_order_id' => $woocommerce_order_id
            ]);
            return false;
        }

        // Map WooCommerce status to internal status
        $statusMap = [
            'pending' => 'pending',
            'processing' => 'processing',
            'on-hold' => 'vendor_accepted',
            'completed' => 'delivered',
            'shipped' => 'shipped',
            'cancelled' => 'cancelled',
            'refunded' => 'returned',
            'failed' => 'cancelled',
        ];

        $internalStatus = $statusMap[$status] ?? $status;

        // Update tracking record
        $tracking->updateStatus($internalStatus);
        $tracking->update([
            'woocommerce_status' => $status,
            'sync_status' => 'synced',
            'woocommerce_last_sync' => now(),
        ]);

        // Add tracking info if provided
        if ($tracking_info && isset($tracking_info['tracking_number'])) {
            $tracking->addTracking(
                $tracking_info['tracking_number'],
                $tracking_info['carrier'] ?? null,
                $tracking_info['tracking_url'] ?? null
            );
        }

        // Update parent transaction if all children are complete
        $this->checkParentOrderCompletion($tracking->parent_transaction_id);

        return true;
    }

    /**
     * Check if parent order should be marked as complete
     */
    public function checkParentOrderCompletion($parent_transaction_id)
    {
        $parentOrder = Transaction::find($parent_transaction_id);
        if (!$parentOrder) {
            return false;
        }

        // Get all child tracking records
        $childTrackings = DropshipOrderTracking::where('parent_transaction_id', $parent_transaction_id)->get();

        // Check if all children are complete
        $allComplete = $childTrackings->every(function ($tracking) {
            return in_array($tracking->fulfillment_status, ['delivered', 'completed']);
        });

        if ($allComplete && $childTrackings->count() > 0) {
            $parentOrder->update([
                'shipping_status' => 'delivered',
                'status' => 'final',
            ]);

            Log::info('Parent order marked as complete', [
                'parent_transaction_id' => $parent_transaction_id
            ]);

            return true;
        }

        return false;
    }

    /**
     * Get order split hierarchy
     */
    public function getOrderHierarchy($parent_transaction_id)
    {
        $parent = Transaction::with('contact')
            ->find($parent_transaction_id);

        if (!$parent) {
            return null;
        }

        $children = Transaction::where('transfer_parent_id', $parent_transaction_id)
            ->with(['sell_lines.product', 'contact'])
            ->get();

        $trackings = DropshipOrderTracking::where('parent_transaction_id', $parent_transaction_id)
            ->with('vendor')
            ->get()
            ->keyBy('transaction_id');

        return [
            'parent' => $parent,
            'children' => $children->map(function ($child) use ($trackings) {
                $child->tracking = $trackings->get($child->id);
                return $child;
            }),
            'summary' => [
                'total_children' => $children->count(),
                'completed' => $trackings->where('fulfillment_status', 'completed')->count(),
                'pending' => $trackings->whereIn('fulfillment_status', ['pending', 'processing'])->count(),
                'shipped' => $trackings->whereIn('fulfillment_status', ['shipped', 'in_transit'])->count(),
            ]
        ];
    }

    /**
     * Get vendor performance metrics
     */
    public function getVendorPerformance($vendor_id, $period = 'month')
    {
        $startDate = match ($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $orders = DropshipOrderTracking::forVendor($vendor_id)
            ->where('created_at', '>=', $startDate)
            ->get();

        $completed = $orders->where('fulfillment_status', 'completed');

        return [
            'total_orders' => $orders->count(),
            'completed_orders' => $completed->count(),
            'pending_orders' => $orders->whereIn('fulfillment_status', ['pending', 'processing'])->count(),
            'cancelled_orders' => $orders->where('fulfillment_status', 'cancelled')->count(),
            'completion_rate' => $orders->count() > 0 
                ? round(($completed->count() / $orders->count()) * 100, 1) 
                : 0,
            'total_revenue' => $completed->sum('vendor_payout_amount'),
            'avg_fulfillment_hours' => $this->calculateVendorAvgFulfillmentTime($completed),
        ];
    }

    /**
     * Calculate vendor's average fulfillment time
     */
    protected function calculateVendorAvgFulfillmentTime($completedOrders)
    {
        $ordersWithTime = $completedOrders->filter(function ($order) {
            return $order->order_placed_at && $order->completed_at;
        });

        if ($ordersWithTime->isEmpty()) {
            return 0;
        }

        $totalHours = $ordersWithTime->sum(function ($order) {
            return $order->order_placed_at->diffInHours($order->completed_at);
        });

        return round($totalHours / $ordersWithTime->count(), 1);
    }
}













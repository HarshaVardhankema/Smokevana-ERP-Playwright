<?php

namespace App\Http\Controllers;

use App\Models\DropshipOrderTracking;
use App\Models\WpVendor;
use App\Models\WoocommerceSyncHistory;
use Modules\Woocommerce\Entities\WoocommerceSyncLog;
use App\Transaction;
use App\Product;
use App\Services\DropshipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Yajra\DataTables\Facades\DataTables;
use Modules\Woocommerce\Utils\WoocommerceUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;

class DropshipOrderController extends Controller
{
    protected $dropshipService;

    public function __construct(DropshipService $dropshipService)
    {
        $this->dropshipService = $dropshipService;
    }

    /**
     * Display listing of dropship orders (renamed from WooCommerce Orders)
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('dropship.view_all_orders') && !auth()->user()->can('dropship.admin_access') && !auth()->user()->can('so.view_all')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Get vendors for filter dropdown
        $vendors = WpVendor::forBusiness($business_id)
            ->active()
            ->pluck('name', 'id');

        // Status options for filter
        $statuses = [
            'pending' => 'Pending',
            'vendor_notified' => 'Vendor Notified',
            'vendor_accepted' => 'Vendor Accepted',
            'processing' => 'Processing',
            'ready_to_ship' => 'Ready to Ship',
            'shipped' => 'Shipped',
            'in_transit' => 'In Transit',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'returned' => 'Returned'
        ];

        return view('dropship.orders.index', compact('vendors', 'statuses'));
    }

    /**
     * Get dropship orders data for DataTable
     */
    public function data(Request $request)
    {
        if (!auth()->user()->can('dropship.view_all_orders') && !auth()->user()->can('dropship.admin_access') && !auth()->user()->can('so.view_all')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $query = DropshipOrderTracking::forBusiness($business_id)
            ->with(['transaction', 'parentTransaction.contact', 'vendor'])
            ->select('dropship_order_tracking.*');

        // Apply filters
        if ($request->has('vendor_id') && !empty($request->vendor_id)) {
            $query->where('wp_vendor_id', $request->vendor_id);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('fulfillment_status', $request->status);
        }

        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return DataTables::of($query)
            ->addColumn('date', function ($row) {
                return $row->created_at->format('Y-m-d H:i');
            })
            ->addColumn('order_no', function ($row) {
                $orderNo = $row->transaction->invoice_no ?? '-';
                return "<a href='" . action([self::class, 'show'], $row->id) . "'>{$orderNo}</a>";
            })
            ->addColumn('parent_order', function ($row) {
                $parentNo = $row->parentTransaction->invoice_no ?? '-';
                $url = action([\App\Http\Controllers\SellController::class, 'show'], $row->parent_transaction_id);
                return "<a href='{$url}' target='_blank'>{$parentNo}</a>";
            })
            ->addColumn('vendor_name', function ($row) {
                return $row->vendor->display_name ?? '-';
            })
            ->addColumn('customer', function ($row) {
                return $row->parentTransaction->contact->name ?? '-';
            })
            ->addColumn('total', function ($row) {
                return '<span class="display_currency" data-currency_symbol="true">' . 
                    ($row->transaction->final_total ?? 0) . '</span>';
            })
            ->addColumn('status_badge', function ($row) {
                return $row->status_badge;
            })
            ->addColumn('sync_status', function ($row) {
                return $row->sync_status_badge;
            })
            ->addColumn('tracking', function ($row) {
                if ($row->tracking_number) {
                    $url = $row->tracking_url;
                    if ($url) {
                        return "<a href='{$url}' target='_blank'>{$row->tracking_number}</a>";
                    }
                    return $row->tracking_number;
                }
                return '-';
            })
            ->addColumn('action', function ($row) {
                $html = '<div class="btn-group">';
                $html .= '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary dropdown-toggle" data-toggle="dropdown">' . __("messages.actions") . '</button>';
                $html .= '<ul class="dropdown-menu dropdown-menu-left">';
                
                $html .= '<li><a href="' . action([self::class, 'show'], $row->id) . '"><i class="fas fa-eye"></i> View Details</a></li>';
                
                // Sync to WooCommerce option
                $html .= '<li><a href="#" class="sync-order-woo" data-id="' . $row->id . '" data-invoice="' . ($row->transaction->invoice_no ?? 'Order') . '"><i class="fab fa-wordpress"></i> Sync to WooCommerce</a></li>';
                
                if ($row->canAddTracking()) {
                    $html .= '<li><a href="#" class="add-tracking" data-id="' . $row->id . '"><i class="fas fa-truck"></i> Add Tracking</a></li>';
                }
                
                if ($row->canEdit()) {
                    $html .= '<li><a href="#" class="update-status" data-id="' . $row->id . '"><i class="fas fa-sync"></i> Update Status</a></li>';
                }
                
                if ($row->sync_status === 'failed') {
                    $html .= '<li><a href="#" class="retry-sync" data-id="' . $row->id . '"><i class="fas fa-redo"></i> Retry Sync</a></li>';
                }
                
                $html .= '</ul></div>';
                return $html;
            })
            ->rawColumns(['order_no', 'parent_order', 'total', 'status_badge', 'sync_status', 'tracking', 'action'])
            ->make(true);
    }

    /**
     * Show the specified dropship order
     */
    public function show(Request $request, $id)
    {
        if (!auth()->user()->can('dropship.view_all_orders') && !auth()->user()->can('dropship.admin_access') && !auth()->user()->can('so.view_all')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $tracking = DropshipOrderTracking::forBusiness($business_id)
            ->with([
                'transaction.sell_lines.product',
                'transaction.contact',
                'parentTransaction.sell_lines.product',
                'parentTransaction.contact',
                'vendor'
            ])
            ->findOrFail($id);

        // Get order hierarchy
        $hierarchy = $this->dropshipService->getOrderHierarchy($tracking->parent_transaction_id);

        return view('dropship.orders.show', compact('tracking', 'hierarchy'));
    }

    /**
     * Add tracking information
     */
    public function addTracking(Request $request, $id)
    {
        if (!auth()->user()->can('dropship.manage_orders') && !auth()->user()->can('dropship.view_all_orders') && !auth()->user()->can('dropship.admin_access')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'tracking_number' => 'required|string|max:255',
            'carrier' => 'nullable|string|max:100',
            'carrier_tracking_url' => 'nullable|url|max:500',
            'shipping_cost' => 'nullable|numeric|min:0',
        ]);

        try {
            $tracking = DropshipOrderTracking::findOrFail($id);
            
            $tracking->addTracking(
                $validated['tracking_number'],
                $validated['carrier'] ?? null,
                $validated['carrier_tracking_url'] ?? null
            );

            if (isset($validated['shipping_cost'])) {
                $tracking->update(['shipping_cost' => $validated['shipping_cost']]);
            }

            return [
                'success' => true,
                'msg' => 'Tracking information added successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to add tracking', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'msg' => 'Failed to add tracking: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        if (!auth()->user()->can('dropship.manage_orders') && !auth()->user()->can('dropship.view_all_orders') && !auth()->user()->can('dropship.admin_access')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'fulfillment_status' => 'required|in:pending,vendor_notified,vendor_accepted,processing,ready_to_ship,shipped,in_transit,out_for_delivery,delivered,completed,cancelled,returned',
            'notes' => 'nullable|string',
        ]);

        try {
            $tracking = DropshipOrderTracking::findOrFail($id);
            $tracking->updateStatus($validated['fulfillment_status']);

            if (!empty($validated['notes'])) {
                $tracking->update(['internal_notes' => $validated['notes']]);
            }

            // Check if parent order should be completed
            $this->dropshipService->checkParentOrderCompletion($tracking->parent_transaction_id);

            return [
                'success' => true,
                'msg' => 'Order status updated successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to update status', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'msg' => 'Failed to update status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Retry sync for failed orders
     */
    public function retrySync(Request $request, $id)
    {
        if (!auth()->user()->can('dropship.admin_access') && !auth()->user()->can('dropship.view_all_orders')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $tracking = DropshipOrderTracking::findOrFail($id);
            
            // Dispatch sync job
            \App\Jobs\WooCommerceWebhookSaleOrder::dispatch($tracking->transaction_id);

            $tracking->update([
                'sync_status' => 'retrying',
                'sync_attempts' => $tracking->sync_attempts + 1,
            ]);

            return [
                'success' => true,
                'msg' => 'Sync retry initiated'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to retry sync', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'msg' => 'Failed to retry sync: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sync single order to WooCommerce
     */
    public function syncOrderToWoo(Request $request, $id)
    {
        if (!auth()->user()->can('dropship.admin_access') && !auth()->user()->can('dropship.view_all_orders')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $tracking = DropshipOrderTracking::with('transaction')->findOrFail($id);
            
            if (!$tracking->transaction) {
                return [
                    'success' => false,
                    'msg' => 'Order transaction not found'
                ];
            }

            // Mark as syncing
            $tracking->update([
                'sync_status' => 'syncing',
                'sync_attempts' => ($tracking->sync_attempts ?? 0) + 1,
            ]);

            // Dispatch sync job
            \App\Jobs\WooCommerceWebhookSaleOrder::dispatch($tracking->transaction_id);

            return [
                'success' => true,
                'msg' => 'Order sync initiated for ' . ($tracking->transaction->invoice_no ?? 'Order #' . $id)
            ];
        } catch (\Exception $e) {
            Log::error('Failed to sync order to WooCommerce', ['error' => $e->getMessage(), 'id' => $id]);
            
            return [
                'success' => false,
                'msg' => 'Failed to sync order: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Bulk sync all orders to WooCommerce with progress tracking
     */
    public function bulkSyncOrdersToWoo(Request $request)
    {
        if (!auth()->user()->can('dropship.admin_access') && !auth()->user()->can('dropship.view_all_orders')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        
        // Get filter parameters
        $vendorId = $request->input('vendor_id');
        $status = $request->input('status');
        $syncOnlyPending = $request->input('sync_only_pending', true);

        // Check if sync is already running
        if (WoocommerceSyncHistory::isSyncRunning($business_id, WoocommerceSyncHistory::TYPE_ORDERS)) {
            return [
                'success' => false,
                'msg' => 'An order sync is already running. Please wait for it to complete.'
            ];
        }

        try {
            // Get orders that need syncing
            $query = DropshipOrderTracking::forBusiness($business_id)
                ->with(['transaction', 'vendor']);
            
            if ($vendorId) {
                $query->where('wp_vendor_id', $vendorId);
            }
            
            if ($status) {
                $query->where('fulfillment_status', $status);
            }
            
            if ($syncOnlyPending) {
                $query->where(function($q) {
                    $q->whereIn('sync_status', ['pending', 'failed', ''])
                      ->orWhereNull('sync_status');
                });
            }
            
            $orders = $query->get();
            $totalOrders = $orders->count();

            if ($totalOrders === 0) {
                return [
                    'success' => false,
                    'msg' => 'No orders found to sync.'
                ];
            }

            // Create sync history record
            $syncHistory = WoocommerceSyncHistory::startSync(
                $business_id,
                WoocommerceSyncHistory::TYPE_ORDERS,
                WoocommerceSyncHistory::TRIGGER_MANUAL,
                auth()->user()->id,
                $totalOrders
            );

            // Initialize details array
            $syncDetails = [];
            $syncedCount = 0;
            $failedCount = 0;

            // Process each order
            foreach ($orders as $order) {
                try {
                    if (!$order->transaction) {
                        $syncDetails[] = [
                            'order_id' => $order->id,
                            'invoice_no' => 'N/A',
                            'status' => 'failed',
                            'message' => 'Transaction not found',
                            'timestamp' => now()->toIso8601String()
                        ];
                        $failedCount++;
                        continue;
                    }

                    // Mark as syncing
                    $order->update([
                        'sync_status' => 'syncing',
                        'sync_attempts' => ($order->sync_attempts ?? 0) + 1,
                    ]);

                    // Dispatch sync job
                    \App\Jobs\WooCommerceWebhookSaleOrder::dispatch($order->transaction_id);

                    $syncDetails[] = [
                        'order_id' => $order->id,
                        'invoice_no' => $order->transaction->invoice_no ?? 'N/A',
                        'vendor' => $order->vendor->display_name ?? 'Unknown',
                        'status' => 'queued',
                        'message' => 'Sync job dispatched',
                        'timestamp' => now()->toIso8601String()
                    ];
                    $syncedCount++;

                    // Update progress
                    $syncHistory->updateProgress($syncedCount, $failedCount);

                } catch (\Exception $e) {
                    $syncDetails[] = [
                        'order_id' => $order->id,
                        'invoice_no' => $order->transaction->invoice_no ?? 'N/A',
                        'status' => 'failed',
                        'message' => $e->getMessage(),
                        'timestamp' => now()->toIso8601String()
                    ];
                    $failedCount++;
                }
            }

            // Mark sync as completed
            $syncHistory->markCompleted($syncedCount, $failedCount, 0, $syncDetails);

            return [
                'success' => true,
                'msg' => "Order sync completed. {$syncedCount} orders queued, {$failedCount} failed.",
                'sync_id' => $syncHistory->id,
                'total' => $totalOrders,
                'synced' => $syncedCount,
                'failed' => $failedCount,
                'details' => $syncDetails
            ];

        } catch (\Exception $e) {
            Log::error('Failed to bulk sync orders', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'msg' => 'Failed to sync orders: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get orders grouped by vendor (for dashboard widget)
     */
    public function byVendor(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $status = $request->input('status');

        $ordersByVendor = $this->dropshipService->getOrdersByVendor($business_id, $status);

        return view('dropship.partials.orders_by_vendor', compact('ordersByVendor'));
    }

    /**
     * Bulk update order statuses
     */
    public function bulkUpdateStatus(Request $request)
    {
        if (!auth()->user()->can('dropship.admin_access') && !auth()->user()->can('dropship.view_all_orders')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:dropship_order_tracking,id',
            'fulfillment_status' => 'required|string',
        ]);

        try {
            DropshipOrderTracking::whereIn('id', $validated['order_ids'])
                ->update(['fulfillment_status' => $validated['fulfillment_status']]);

            return [
                'success' => true,
                'msg' => 'Orders updated successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to bulk update orders', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'msg' => 'Failed to update orders: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Manual sync products to WooCommerce
     */
    public function syncProducts(Request $request)
    {
        if (!auth()->user()->can('dropship.admin_access') && !auth()->user()->can('dropship.view_all_orders')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Check if sync is already running
        if (WoocommerceSyncHistory::isSyncRunning($business_id, WoocommerceSyncHistory::TYPE_PRODUCTS)) {
            return [
                'success' => false,
                'msg' => 'A product sync is already running. Please wait for it to complete.'
            ];
        }

        try {
            // Get products count that need syncing
            $products = Product::where('business_id', $business_id)
                ->where(function ($q) {
                    $q->whereNull('woocommerce_disable_sync')
                        ->orWhere('woocommerce_disable_sync', 0);
                })
                ->get();

            $totalProducts = $products->count();

            if ($totalProducts === 0) {
                return [
                    'success' => false,
                    'msg' => 'No products found to sync.'
                ];
            }

            // Create sync history record
            $syncHistory = WoocommerceSyncHistory::startSync(
                $business_id,
                WoocommerceSyncHistory::TYPE_PRODUCTS,
                WoocommerceSyncHistory::TRIGGER_MANUAL,
                auth()->user()->id,
                $totalProducts
            );

            // Dispatch sync job in background
            \App\Jobs\SyncProductsToWooCommerceJob::dispatch($syncHistory->id, $business_id);

            return [
                'success' => true,
                'msg' => "Product sync started for {$totalProducts} products. You can check the progress in Sync History.",
                'sync_id' => $syncHistory->id
            ];

        } catch (\Exception $e) {
            Log::error('Failed to start product sync', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'msg' => 'Failed to start sync: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get sync history
     */
    public function syncHistory(Request $request)
    {
        if (!auth()->user()->can('dropship.admin_access') && !auth()->user()->can('dropship.view_all_orders')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $history = WoocommerceSyncHistory::forBusiness($business_id)
            ->with('triggeredBy')
            ->recent(50)
            ->get();

        return view('dropship.partials.sync_history_modal', compact('history'));
    }

    /**
     * Get sync history data for DataTable (AJAX)
     */
    public function syncHistoryData(Request $request)
    {
        if (!auth()->user()->can('dropship.admin_access') && !auth()->user()->can('dropship.view_all_orders')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $query = WoocommerceSyncHistory::forBusiness($business_id)
            ->with('triggeredBy')
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addColumn('started', function ($row) {
                return $row->started_at ? $row->started_at->format('Y-m-d H:i:s') : '-';
            })
            ->addColumn('sync_type_label', function ($row) {
                return $row->sync_type_label;
            })
            ->addColumn('trigger_label', function ($row) {
                $icon = $row->trigger_type === 'manual' ? 'fa-hand-pointer' : 
                       ($row->trigger_type === 'cron' ? 'fa-clock' : 'fa-bolt');
                return "<i class='fas {$icon}'></i> " . $row->trigger_type_label;
            })
            ->addColumn('status_html', function ($row) {
                return $row->status_badge;
            })
            ->addColumn('progress', function ($row) {
                $total = $row->total_items ?: 1;
                $syncedPercent = round(($row->synced_items / $total) * 100);
                $failedPercent = round(($row->failed_items / $total) * 100);
                
                $html = "<div class='progress' style='height: 20px; min-width: 120px;'>";
                $html .= "<div class='progress-bar bg-success' style='width: {$syncedPercent}%' title='{$row->synced_items} synced'></div>";
                if ($row->failed_items > 0) {
                    $html .= "<div class='progress-bar bg-danger' style='width: {$failedPercent}%' title='{$row->failed_items} failed'></div>";
                }
                $html .= "</div>";
                
                $html .= "<small style='display: flex; justify-content: space-between; margin-top: 2px;'>";
                $html .= "<span style='color: #10b981;'><i class='fas fa-check'></i> {$row->synced_items}</span>";
                if ($row->failed_items > 0) {
                    $html .= "<span style='color: #ef4444; font-weight: 600;'><i class='fas fa-times'></i> {$row->failed_items}</span>";
                } else {
                    $html .= "<span style='color: #9ca3af;'>0 failed</span>";
                }
                $html .= "</small>";
                return $html;
            })
            ->addColumn('duration_text', function ($row) {
                return $row->duration;
            })
            ->addColumn('triggered_by_name', function ($row) {
                if ($row->triggeredBy) {
                    return $row->triggeredBy->first_name . ' ' . $row->triggeredBy->last_name;
                }
                return $row->trigger_type === 'cron' ? 'System (Cron)' : '-';
            })
            ->addColumn('action', function ($row) {
                if ($row->failed_items > 0) {
                    $html = '<button type="button" class="btn btn-xs btn-danger btn-view-errors view-sync-details" data-id="' . $row->id . '">';
                    $html .= '<i class="fas fa-exclamation-triangle"></i> View Errors (' . $row->failed_items . ')</button>';
                } else {
                    $html = '<button type="button" class="btn btn-xs btn-info view-sync-details" data-id="' . $row->id . '">';
                    $html .= '<i class="fas fa-eye"></i> Details</button>';
                }
                return $html;
            })
            ->rawColumns(['trigger_label', 'status_html', 'progress', 'action'])
            ->make(true);
    }

    /**
     * Get sync details
     */
    public function syncDetails(Request $request, $id)
    {
        if (!auth()->user()->can('dropship.admin_access') && !auth()->user()->can('dropship.view_all_orders')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $sync = WoocommerceSyncHistory::forBusiness($business_id)
            ->with('triggeredBy')
            ->findOrFail($id);

        return view('dropship.partials.sync_details_modal', compact('sync'));
    }

    /**
     * Get current sync status (for polling)
     */
    public function syncStatus(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');

        $sync = WoocommerceSyncHistory::forBusiness($business_id)->find($id);

        if (!$sync) {
            return response()->json(['error' => 'Sync not found'], 404);
        }

        return response()->json([
            'id' => $sync->id,
            'status' => $sync->status,
            'total_items' => $sync->total_items,
            'synced_items' => $sync->synced_items,
            'failed_items' => $sync->failed_items,
            'skipped_items' => $sync->skipped_items,
            'progress_percent' => $sync->total_items > 0 
                ? round((($sync->synced_items + $sync->failed_items + $sync->skipped_items) / $sync->total_items) * 100)
                : 0,
            'duration' => $sync->duration,
            'error_message' => $sync->error_message,
        ]);
    }
}











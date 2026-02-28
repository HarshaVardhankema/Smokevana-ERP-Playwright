<?php

namespace App\Http\Controllers\VendorPortal;

use App\Http\Controllers\Controller;
use App\Models\DropshipOrderTracking;
use App\Models\WpVendor;
use App\Product;
use App\Services\DropshipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class VendorPortalController extends Controller
{
    protected $dropshipService;

    public function __construct(DropshipService $dropshipService)
    {
        $this->dropshipService = $dropshipService;
    }

    /**
     * Get the current vendor from session
     */
    protected function getVendor()
    {
        $vendorId = session('vendor_portal.vendor_id');
        return WpVendor::find($vendorId);
    }

    protected function vendorPurchaseOrderQuery($vendor)
    {
        $query = DB::table('transactions as t')
            ->whereIn('t.type', ['purchase', 'purchase_order']);
        
        // Build OR conditions for matching vendor's purchase orders
        $hasCondition = false;
        
        $query->where(function ($q) use ($vendor, &$hasCondition) {
            // 1. Match by contact_id (supplier contact linked to vendor)
            if (!empty($vendor->contact_id)) {
                $q->where('t.contact_id', $vendor->contact_id);
                $hasCondition = true;
            }
            
            // 2. Match by created_by (user_id) for POs created by this vendor's user
            if (!empty($vendor->user_id)) {
                if ($hasCondition) {
                    $q->orWhere('t.created_by', $vendor->user_id);
                } else {
                    $q->where('t.created_by', $vendor->user_id);
                    $hasCondition = true;
                }
            }
            
            // 3. Match POs that contain products from this vendor's inventory
            if ($hasCondition) {
                $q->orWhereExists(function ($subQuery) use ($vendor) {
                    $subQuery->select(DB::raw(1))
                        ->from('purchase_lines as pl')
                        ->join('products_wp_vendors_table_pivot as pv', function ($join) use ($vendor) {
                            $join->on('pv.product_id', '=', 'pl.product_id')
                                ->where('pv.wp_vendor_id', '=', $vendor->id);
                        })
                        ->whereRaw('pl.transaction_id = t.id');
                });
            } else {
                $q->whereExists(function ($subQuery) use ($vendor) {
                    $subQuery->select(DB::raw(1))
                        ->from('purchase_lines as pl')
                        ->join('products_wp_vendors_table_pivot as pv', function ($join) use ($vendor) {
                            $join->on('pv.product_id', '=', 'pl.product_id')
                                ->where('pv.wp_vendor_id', '=', $vendor->id);
                        })
                        ->whereRaw('pl.transaction_id = t.id');
                });
            }
        });
        
        return $query;
    }

    /**
     * Dashboard view
     */
    public function dashboard()
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->route('vendor.login')->with('error', 'Session expired. Please login again.');
        }

        // Get statistics
        $stats = [
            'total_products' => $vendor->products()->count(),
            'active_products' => $vendor->activeProducts()->count(),
            'pending_orders' => $vendor->pendingOrders()->count(),
            'completed_orders' => $vendor->completedOrders()->count(),
            'orders_this_month' => DropshipOrderTracking::forVendor($vendor->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'revenue_this_month' => DropshipOrderTracking::forVendor($vendor->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->whereIn('fulfillment_status', ['delivered', 'completed'])
                ->sum('vendor_payout_amount'),
        ];

        // Get performance metrics with safe defaults
        try {
            $performance = $this->dropshipService->getVendorPerformance($vendor->id, 'month');
        } catch (\Exception $e) {
            $performance = [];
        }
        
        // Ensure all performance keys have defaults
        $performance = array_merge([
            'total_orders' => 0,
            'completed_orders' => 0,
            'pending_orders' => 0,
            'cancelled_orders' => 0,
            'completion_rate' => 0,
            'total_revenue' => 0,
            'avg_fulfillment_hours' => 0,
        ], $performance ?? []);

        // Get orders needing action (pending, vendor_notified, vendor_accepted without tracking)
        $ordersNeedingAction = DropshipOrderTracking::forVendor($vendor->id)
            ->with(['transaction', 'parentTransaction.contact'])
            ->where(function ($q) {
                $q->whereIn('fulfillment_status', ['pending', 'vendor_notified'])
                    ->orWhere(function ($q2) {
                        $q2->whereIn('fulfillment_status', ['vendor_accepted', 'processing', 'ready_to_ship'])
                            ->whereNull('tracking_number');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get recent orders
        $recentOrders = DropshipOrderTracking::forVendor($vendor->id)
            ->with(['transaction', 'parentTransaction.contact'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Refresh session to update pending count
        VendorAuthController::refreshVendorSession();

        return view('vendor_portal.dashboard', compact(
            'vendor',
            'stats',
            'performance',
            'ordersNeedingAction',
            'recentOrders'
        ));
    }

    /**
     * Orders listing
     */
    public function orders(Request $request)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->route('vendor.login')->with('error', 'Session expired.');
        }

        if ($request->ajax()) {
            $query = DropshipOrderTracking::forVendor($vendor->id)
                ->with(['transaction', 'parentTransaction.contact']);

            // Apply status filter
            if ($request->has('status') && !empty($request->status)) {
                $query->where('fulfillment_status', $request->status);
            }

            return DataTables::of($query)
                ->addColumn('date', function ($row) {
                    return $row->created_at->format('M d, Y');
                })
                ->addColumn('order_no', function ($row) {
                    return $row->transaction->invoice_no ?? 'N/A';
                })
                ->addColumn('customer', function ($row) {
                    $name = $row->parentTransaction->contact->name ?? '-';
                    $city = $row->parentTransaction->shipping_city ?? '';
                    return "<strong>{$name}</strong><br><small class='text-muted'>{$city}</small>";
                })
                ->addColumn('items', function ($row) {
                    $count = $row->transaction->sell_lines()->count();
                    return $count . ' item(s)';
                })
                ->addColumn('total', function ($row) {
                    return '$' . number_format($row->transaction->final_total ?? 0, 2);
                })
                ->addColumn('status_badge', function ($row) {
                    return $row->status_badge;
                })
                ->addColumn('tracking', function ($row) {
                    if ($row->tracking_number) {
                        return "<code style='font-size: 12px;'>{$row->tracking_number}</code>";
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="action-buttons">';
                    
                    // View button - always visible
                    $html .= '<a href="' . route('vendor.orders.show', $row->id) . '" class="action-btn view-btn" title="View Order"><i class="bi bi-eye"></i></a>';

                    // Accept button - for pending orders
                    if (in_array($row->fulfillment_status, ['pending', 'vendor_notified'])) {
                        $html .= '<button class="action-btn accept-btn accept-order" data-id="' . $row->id . '" title="Accept Order"><i class="bi bi-check"></i></button>';
                    }

                    // Add Tracking button - for accepted/processing orders without tracking
                    if (in_array($row->fulfillment_status, ['vendor_accepted', 'processing', 'ready_to_ship']) && !$row->tracking_number) {
                        $html .= '<button class="action-btn tracking-btn add-tracking" data-id="' . $row->id . '" title="Add Tracking"><i class="bi bi-truck"></i></button>';
                    }

                    // Packing Slip button - for shipped orders
                    if (in_array($row->fulfillment_status, ['shipped', 'in_transit', 'out_for_delivery', 'delivered', 'completed'])) {
                        $html .= '<a href="' . route('vendor.orders.packing-slip', $row->id) . '" class="action-btn slip-btn" title="Packing Slip" target="_blank"><i class="bi bi-file-text"></i></a>';
                    }

                    // Complete button - for shipped orders
                    if (in_array($row->fulfillment_status, ['shipped', 'in_transit', 'out_for_delivery', 'delivered'])) {
                        $orderRef = $row->ref_no ?? $row->invoice_no ?? $row->id;
                        $html .= '<button class="action-btn complete-btn complete-order" data-id="' . $row->id . '" data-ref="' . $orderRef . '" title="Mark Complete"><i class="bi bi-clipboard-check"></i></button>';
                    }

                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['customer', 'status_badge', 'tracking', 'action'])
                ->make(true);
        }

        // Status options for filter
        $statuses = [
            'pending' => 'Pending',
            'vendor_notified' => 'Notified',
            'vendor_accepted' => 'Accepted',
            'processing' => 'Processing',
            'ready_to_ship' => 'Ready to Ship',
            'shipped' => 'Shipped',
            'in_transit' => 'In Transit',
            'delivered' => 'Delivered',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        return view('vendor_portal.orders.index', compact('vendor', 'statuses'));
    }

    /**
     * Show single order details
     */
    public function showOrder($id)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->route('vendor.login')->with('error', 'Session expired.');
        }

        $order = DropshipOrderTracking::forVendor($vendor->id)
            ->with([
                'transaction.sell_lines.product',
                'transaction.contact',
                'parentTransaction.sell_lines.product',
                'parentTransaction.contact',
            ])
            ->findOrFail($id);

        return view('vendor_portal.orders.show', compact('vendor', 'order'));
    }

    /**
     * Accept an order
     */
    public function acceptOrder(Request $request, $id)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        try {
            $order = DropshipOrderTracking::forVendor($vendor->id)->findOrFail($id);

            if (!in_array($order->fulfillment_status, ['pending', 'vendor_notified'])) {
                return response()->json(['success' => false, 'msg' => 'Order cannot be accepted in its current status.']);
            }

            $order->updateStatus(DropshipOrderTracking::STATUS_VENDOR_ACCEPTED);

            Log::info('Vendor accepted order', [
                'vendor_id' => $vendor->id,
                'order_id' => $id
            ]);

            VendorAuthController::refreshVendorSession();

            return response()->json([
                'success' => true,
                'msg' => 'Order accepted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to accept order', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Failed to accept order.']);
        }
    }

    /**
     * Mark order as processing
     */
    public function markProcessing(Request $request, $id)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        try {
            $order = DropshipOrderTracking::forVendor($vendor->id)->findOrFail($id);
            $order->updateStatus(DropshipOrderTracking::STATUS_PROCESSING);

            return response()->json([
                'success' => true,
                'msg' => 'Order marked as processing!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => 'Failed to update order.']);
        }
    }

    /**
     * Mark order as shipped with tracking
     * IMPORTANT: This also deducts stock from vendor and ERP inventory
     */
    public function shipOrder(Request $request, $id)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        $request->validate([
            'tracking_number' => 'required|string|max:255',
            'carrier' => 'nullable|string|max:100',
            'carrier_tracking_url' => 'nullable|url|max:500',
        ]);

        try {
            $order = DropshipOrderTracking::forVendor($vendor->id)
                ->with(['transaction.sell_lines.product', 'transaction.sell_lines.variations'])
                ->findOrFail($id);

            // Add tracking and update status to shipped
            $order->addTracking(
                $request->tracking_number,
                $request->carrier,
                $request->carrier_tracking_url
            );

            // ============================================================
            // CRITICAL: Deduct stock on order shipment
            // This ensures inventory is reduced when order is fulfilled
            // ============================================================
            $syncService = app(\App\Services\DropshipInventorySyncService::class);
            
            // Check if stock was already deducted (prevent double deduction)
            if (!$syncService->isStockAlreadyDeducted($order)) {
                $stockResult = $syncService->deductStockOnOrderFulfillment($order, $vendor->id);
                
                if (!$stockResult['success']) {
                    Log::warning('Stock deduction had some failures', [
                        'order_id' => $id,
                        'vendor_id' => $vendor->id,
                        'failed_items' => $stockResult['failed']
                    ]);
                }

                Log::info('Stock deducted on order shipment', [
                    'order_id' => $id,
                    'vendor_id' => $vendor->id,
                    'items_deducted' => count($stockResult['deducted']),
                    'total_quantity' => $stockResult['total_quantity']
                ]);
            } else {
                Log::info('Stock already deducted for this order, skipping', [
                    'order_id' => $id
                ]);
            }

            Log::info('Vendor shipped order', [
                'vendor_id' => $vendor->id,
                'order_id' => $id,
                'tracking' => $request->tracking_number
            ]);

            VendorAuthController::refreshVendorSession();

            // Check if parent order should be completed
            $this->dropshipService->checkParentOrderCompletion($order->parent_transaction_id);

            return response()->json([
                'success' => true,
                'msg' => 'Order shipped and stock updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to ship order', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Failed to update order: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark order as completed
     */
    public function completeOrder(Request $request, $id)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        try {
            $order = DropshipOrderTracking::forVendor($vendor->id)
                ->with(['transaction.sell_lines'])
                ->findOrFail($id);

            // Only allow completing shipped or delivered orders
            if (!in_array($order->fulfillment_status, ['shipped', 'in_transit', 'out_for_delivery', 'delivered'])) {
                return response()->json([
                    'success' => false, 
                    'msg' => 'Only shipped or delivered orders can be marked as completed.'
                ]);
            }

            // Calculate vendor payout amount from order total
            $orderTotal = 0;
            if ($order->transaction && $order->transaction->sell_lines) {
                foreach ($order->transaction->sell_lines as $line) {
                    $orderTotal += ($line->unit_price_inc_tax * $line->quantity);
                }
            }
            
            // Apply commission if vendor has commission settings
            $payoutAmount = $orderTotal;
            if ($vendor->commission_type && $vendor->commission_value) {
                if ($vendor->commission_type === 'percentage') {
                    // Vendor keeps (100 - commission)% of the order
                    $commissionAmount = ($orderTotal * $vendor->commission_value) / 100;
                    $payoutAmount = $orderTotal - $commissionAmount;
                } elseif ($vendor->commission_type === 'fixed') {
                    // Fixed commission per order
                    $payoutAmount = $orderTotal - $vendor->commission_value;
                }
            }
            
            // Ensure payout is not negative
            $payoutAmount = max(0, $payoutAmount);

            // Update the fulfillment status to completed with payout info
            $order->update([
                'fulfillment_status' => DropshipOrderTracking::STATUS_COMPLETED,
                'completed_at' => now(),
                'vendor_payout_amount' => $payoutAmount,
                'vendor_payout_status' => 'pending',
            ]);

            // Also update the child transaction's status in ERP
            if ($order->transaction) {
                $order->transaction->update([
                    'status' => 'completed',
                    'shipping_status' => 'delivered',
                ]);
            }

            Log::info('Vendor completed order', [
                'vendor_id' => $vendor->id,
                'order_id' => $id,
                'tracking_id' => $order->id,
                'order_total' => $orderTotal,
                'payout_amount' => $payoutAmount
            ]);

            VendorAuthController::refreshVendorSession();

            // Check if parent order should be completed
            $this->dropshipService->checkParentOrderCompletion($order->parent_transaction_id);

            return response()->json([
                'success' => true,
                'msg' => 'Order marked as completed successfully! Earnings: $' . number_format($payoutAmount, 2)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to complete order', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Failed to complete order: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate Packing Slip for vendor order
     */
    public function packingSlip($id)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->route('vendor.login');
        }

        try {
            $order = DropshipOrderTracking::forVendor($vendor->id)
                ->with(['transaction.sell_lines.product', 'transaction.sell_lines.variations', 'transaction.contact'])
                ->findOrFail($id);

            // Get the transaction (child order) associated with this dropship tracking
            $transaction = $order->transaction;
            
            if (!$transaction) {
                return back()->with('error', 'Order not found.');
            }

            // Prepare packing slip data
            $packingData = new \stdClass();
            $packingData->invoice_no = $transaction->invoice_no;
            $packingData->invoice_date = \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y');
            $packingData->invoice_time = \Carbon\Carbon::parse($transaction->transaction_date)->format('h:i A');
            
            // Customer info
            $contact = $transaction->contact;
            $packingData->customer_name = $contact ? $contact->name : 'N/A';
            $packingData->customer_mobile = $contact ? $contact->mobile : '';
            
            // Shipping address - check multiple sources
            $shippingAddress = $transaction->shipping_address;
            if (empty($shippingAddress) && $contact) {
                // Build from contact details
                $addressParts = array_filter([
                    $contact->shipping_address ?? $contact->address_line_1,
                    $contact->city,
                    $contact->state,
                    $contact->zip_code,
                    $contact->country
                ]);
                $shippingAddress = implode(', ', $addressParts);
            }
            $packingData->shipping_address = $shippingAddress ?: 'N/A';
            
            // Tracking info
            $packingData->tracking_number = $order->tracking_number;
            $packingData->carrier = $order->carrier;
            
            // Vendor info
            $packingData->vendor_name = $vendor->company_name ?? $vendor->name ?? 'Vendor';
            
            // Build product lines
            $lines = [];
            foreach ($transaction->sell_lines as $line) {
                $product = $line->product;
                $variation = $line->variations;
                
                $lineData = [
                    'name' => $product ? $product->name : 'Product',
                    'product_variation' => ($variation && $variation->product_variation) ? $variation->product_variation->name : '',
                    'variation' => ($variation && $variation->name !== 'DUMMY') ? $variation->name : '',
                    'sub_sku' => $variation ? $variation->sub_sku : '',
                    'quantity' => $line->quantity,
                    'units' => $product && $product->unit ? $product->unit->short_name : 'Pc(s)',
                ];
                $lines[] = $lineData;
            }
            $packingData->lines = $lines;
            $packingData->show_barcode = true;
            
            return view('vendor_portal.orders.packing_slip', compact('packingData', 'vendor', 'order'));
            
        } catch (\Exception $e) {
            Log::error('Failed to generate packing slip', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to generate packing slip: ' . $e->getMessage());
        }
    }

    /**
     * Products listing - Enhanced to show variants with expandable UI
     * Shows parent products with expandable variants (ERP-like behavior)
     */
    public function products(Request $request)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->route('vendor.login')->with('error', 'Session expired.');
        }

        // Get stock summary
        $stockSummary = $this->getVendorStockSummary($vendor->id);

        // AJAX: Get parent products for DataTable
        if ($request->ajax() && $request->has('draw')) {
            $response = $this->getParentProductsData($vendor);
            
            // Add stock summary to the DataTables response
            $data = json_decode($response->getContent(), true);
            $data['stockSummary'] = $stockSummary;
            
            return response()->json($data);
        }
        
        // AJAX: Get variants for a specific product (expandable row)
        if ($request->ajax() && $request->has('product_id')) {
            return $this->getProductVariants($vendor, $request->product_id);
        }

        return view('vendor_portal.products', compact('vendor', 'stockSummary'));
    }
    
    /**
     * Get parent products data for DataTable (expandable rows)
     */
    protected function getParentProductsData($vendor)
    {
        // Check if vendor has mapped products
        $mappedProductsCount = DB::table('products_wp_vendors_table_pivot')
            ->where('wp_vendor_id', $vendor->id)
            ->count();
        
        if ($mappedProductsCount > 0) {
            // Use mapped products from pivot table
            $products = DB::table('products as p')
                ->join('products_wp_vendors_table_pivot as pivot', 'p.id', '=', 'pivot.product_id')
                ->where('pivot.wp_vendor_id', $vendor->id)
                ->where('p.business_id', $vendor->business_id)
                ->select([
                    'p.id',
                    'p.name',
                    'p.sku',
                    'p.image',
                    'p.type',
                    'pivot.vendor_cost_price',
                    'pivot.dropship_selling_price as selling_price',
                    'pivot.vendor_stock_qty',
                    'pivot.status',
                ]);
        } else {
            // Fallback: Show products by brand name or SKU prefix matching vendor name
            $vendorName = strtoupper($vendor->name);
            
            // Try to find brand by vendor name
            $brand = DB::table('brands')
                ->where('business_id', $vendor->business_id)
                ->where(function($q) use ($vendorName, $vendor) {
                    $q->where('name', 'LIKE', "%{$vendorName}%")
                      ->orWhere('name', 'LIKE', "%{$vendor->name}%");
                })
                ->first();
            
            // Build base query - match products by SKU prefix or brand
            $products = DB::table('products as p')
                ->leftJoin('products_wp_vendors_table_pivot as pivot', function($join) use ($vendor) {
                    $join->on('p.id', '=', 'pivot.product_id')
                         ->where('pivot.wp_vendor_id', $vendor->id);
                })
                ->where('p.business_id', $vendor->business_id)
                ->where(function($q) use ($vendorName, $brand) {
                    // Match by SKU prefix
                    $q->where('p.sku', 'LIKE', "{$vendorName}%");
                    
                    // Or match by brand if found
                    if ($brand) {
                        $q->orWhere('p.brand_id', $brand->id);
                    }
                })
                ->select([
                    'p.id',
                    'p.name',
                    'p.sku',
                    'p.image',
                    'p.type',
                    DB::raw('COALESCE(pivot.vendor_cost_price, 0) as vendor_cost_price'),
                    DB::raw('COALESCE(pivot.dropship_selling_price, 0) as selling_price'),
                    DB::raw('COALESCE(pivot.vendor_stock_qty, 0) as vendor_stock_qty'),
                    DB::raw('COALESCE(pivot.status, "active") as status'),
                ]);
        }
        
        return DataTables::of($products)
            ->addColumn('image_display', function ($row) {
                $img = $row->image ? asset('uploads/img/' . $row->image) : 'https://via.placeholder.com/50';
                return $img;
            })
            ->addColumn('vendor_cost', function ($row) {
                return $row->vendor_cost_price ?? 0;
            })
            ->addColumn('stock', function ($row) {
                return $row->vendor_stock_qty ?? 0;
            })
            ->addColumn('status_display', function ($row) {
                return $row->status ?? 'active';
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn-action btn-action-stock update-stock" data-id="' . $row->id . '" data-current="' . ($row->vendor_stock_qty ?? 0) . '"><i class="bi bi-pencil"></i> Update</button>';
            })
            ->filterColumn('p.name', function($query, $keyword) {
                $query->where('p.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('p.sku', function($query, $keyword) {
                $query->where('p.sku', 'like', "%{$keyword}%");
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    
    /**
     * Get variants for a specific product (AJAX for expandable row)
     */
    protected function getProductVariants($vendor, $productId)
    {
        $variants = DB::table('variation_vendor_pivot as vvp')
            ->join('variations as v', 'vvp.variation_id', '=', 'v.id')
            ->leftJoin('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
            ->where('vvp.product_id', $productId)
            ->where('vvp.wp_vendor_id', $vendor->id)
            ->whereNull('v.deleted_at')
            ->where('v.name', '!=', 'DUMMY')
            ->select([
                'vvp.id as mapping_id',
                'vvp.variation_id',
                'v.name as variant_name',
                'v.sub_sku',
                'pv.name as variant_type',
                'vvp.vendor_cost_price',
                'vvp.selling_price',
                'vvp.vendor_stock_qty',
                'vvp.vendor_sku',
                'vvp.status',
                'vvp.markup_percentage',
            ])
            ->orderBy('v.name')
            ->get();
        
        return response()->json([
            'success' => true,
            'variants' => $variants,
            'allow_product_edit' => (bool) $vendor->allow_product_edit
        ]);
    }

    /**
     * Get stock summary for vendor
     */
    protected function getVendorStockSummary($vendorId)
    {
        // Try variation-level first
        $variationCount = 0;
        try {
            $variationCount = DB::table('variation_vendor_pivot')->where('wp_vendor_id', $vendorId)->count();
        } catch (\Exception $e) {
            // Table might not exist
        }
        
        if ($variationCount > 0) {
            $mappings = DB::table('variation_vendor_pivot')
                ->where('wp_vendor_id', $vendorId)
                ->select('vendor_stock_qty', 'low_stock_threshold')
                ->get();
            
            return [
                'total' => $mappings->count(),
                'in_stock' => $mappings->filter(fn($m) => $m->vendor_stock_qty > ($m->low_stock_threshold ?? 10))->count(),
                'low_stock' => $mappings->filter(fn($m) => $m->vendor_stock_qty > 0 && $m->vendor_stock_qty <= ($m->low_stock_threshold ?? 10))->count(),
                'out_of_stock' => $mappings->filter(fn($m) => $m->vendor_stock_qty <= 0)->count(),
            ];
        }
        
        // Fallback to product-level
        $products = DB::table('products_wp_vendors_table_pivot')
            ->where('wp_vendor_id', $vendorId)
            ->select('vendor_stock_qty')
            ->get();
        
        return [
            'total' => $products->count(),
            'in_stock' => $products->filter(fn($p) => $p->vendor_stock_qty > 10)->count(),
            'low_stock' => $products->filter(fn($p) => $p->vendor_stock_qty > 0 && $p->vendor_stock_qty <= 10)->count(),
            'out_of_stock' => $products->filter(fn($p) => $p->vendor_stock_qty <= 0)->count(),
        ];
    }

    /**
     * Update product stock - Syncs vendor stock to ERP inventory
     * Vendor is the source of truth for dropshipped product inventory
     */
    public function updateStock(Request $request, $productId)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        $request->validate([
            'stock_qty' => 'required|integer|min:0',
        ]);

        try {
            $stockQty = (int) $request->stock_qty;

            // 1. Update vendor pivot table (vendor's record of their stock)
            $vendor->products()->updateExistingPivot($productId, [
                'vendor_stock_qty' => $stockQty,
                'stock_last_updated' => now(),
                'status' => $stockQty > 0 ? 'active' : 'out_of_stock',
            ]);

            // 2. Sync to ERP inventory (variation_location_details)
            // This makes the vendor stock the source of truth for dropshipped products
            $syncService = app(\App\Services\DropshipInventorySyncService::class);
            $syncResult = $syncService->syncVendorStockToERP($productId, $vendor->id, $stockQty);

            if ($syncResult) {
                Log::info('Vendor stock synced to ERP successfully', [
                    'vendor_id' => $vendor->id,
                    'product_id' => $productId,
                    'stock_qty' => $stockQty
                ]);

                return response()->json([
                    'success' => true,
                    'msg' => 'Stock updated and synced to ERP successfully!',
                    'stock' => $stockQty,
                    'synced' => true
                ]);
            } else {
                // Pivot updated but ERP sync failed
                Log::warning('Vendor stock updated but ERP sync failed', [
                    'vendor_id' => $vendor->id,
                    'product_id' => $productId
                ]);

                return response()->json([
                    'success' => true,
                    'msg' => 'Stock updated. ERP sync pending.',
                    'stock' => $stockQty,
                    'synced' => false
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update vendor stock', [
                'vendor_id' => $vendor->id,
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'msg' => 'Failed to update stock: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk update stock for multiple products
     */
    public function bulkUpdateStock(Request $request)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|integer',
            'products.*.stock' => 'required|integer|min:0',
        ]);

        try {
            $syncService = app(\App\Services\DropshipInventorySyncService::class);
            $stockData = [];
            $updated = 0;

            foreach ($request->products as $item) {
                $productId = $item['id'];
                $stockQty = (int) $item['stock'];

                // Update pivot
                $vendor->products()->updateExistingPivot($productId, [
                    'vendor_stock_qty' => $stockQty,
                    'stock_last_updated' => now(),
                    'status' => $stockQty > 0 ? 'active' : 'out_of_stock',
                ]);

                $stockData[$productId] = $stockQty;
                $updated++;
            }

            // Bulk sync to ERP
            $syncResults = $syncService->bulkSyncVendorStock($vendor->id, $stockData);

            return response()->json([
                'success' => true,
                'msg' => "Updated {$updated} products. ERP synced: {$syncResults['success']}, Failed: {$syncResults['failed']}",
                'details' => $syncResults
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk stock update failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Bulk update failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Update variation cost and stock (variant-level endpoint)
     */
    public function updateVariation(Request $request, $mappingId)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        $request->validate([
            'vendor_cost_price' => 'nullable|numeric|min:0',
            'vendor_stock_qty' => 'nullable|integer|min:0',
            'vendor_sku' => 'nullable|string|max:100',
        ]);

        try {
            $mapping = \App\Models\VariationVendor::where('id', $mappingId)
                ->where('wp_vendor_id', $vendor->id)
                ->first();

            if (!$mapping) {
                return response()->json(['success' => false, 'msg' => 'Variation not found.']);
            }

            $syncService = app(\App\Services\DropshipInventorySyncService::class);

            // Update cost price if provided
            if ($request->has('vendor_cost_price') && $request->vendor_cost_price !== null) {
                $mapping->updateCostPrice($request->vendor_cost_price);
            }

            // Update stock if provided
            if ($request->has('vendor_stock_qty') && $request->vendor_stock_qty !== null) {
                $mapping->updateStock($request->vendor_stock_qty);
                
                // Sync to ERP inventory
                $syncService->syncVariationStockToERP(
                    $mapping->variation_id,
                    $vendor->id,
                    $request->vendor_stock_qty
                );
            }

            // Update vendor SKU if provided
            if ($request->has('vendor_sku')) {
                $mapping->vendor_sku = $request->vendor_sku;
                $mapping->save();
            }

            VendorAuthController::refreshVendorSession();

            return response()->json([
                'success' => true,
                'msg' => 'Variation updated and synced to ERP!',
                'data' => [
                    'cost' => $mapping->vendor_cost_price,
                    'selling_price' => $mapping->selling_price,
                    'stock' => $mapping->vendor_stock_qty,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update variation', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Failed to update: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk update variations (cost and stock)
     */
    public function bulkUpdateVariations(Request $request)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        $request->validate([
            'variations' => 'required|array',
            'variations.*.id' => 'required|integer',
            'variations.*.cost' => 'nullable|numeric|min:0',
            'variations.*.stock' => 'nullable|integer|min:0',
        ]);

        try {
            $syncService = app(\App\Services\DropshipInventorySyncService::class);
            $data = [];

            foreach ($request->variations as $item) {
                $mapping = \App\Models\VariationVendor::where('id', $item['id'])
                    ->where('wp_vendor_id', $vendor->id)
                    ->first();

                if ($mapping) {
                    $data[$mapping->variation_id] = [
                        'cost' => $item['cost'] ?? null,
                        'stock' => $item['stock'] ?? null,
                    ];
                }
            }

            $results = $syncService->bulkUpdateVariations($vendor->id, $data);

            return response()->json([
                'success' => true,
                'msg' => "Updated {$results['updated']} variations. {$results['failed']} failed.",
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk variation update failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Bulk update failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Earnings view
     */
    public function earnings(Request $request)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->route('vendor.login')->with('error', 'Session expired.');
        }

        // Get earnings summary
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        $earnings = [
            'this_month' => DropshipOrderTracking::forVendor($vendor->id)
                ->where('created_at', '>=', $currentMonth)
                ->whereIn('fulfillment_status', ['delivered', 'completed'])
                ->sum('vendor_payout_amount'),
            'last_month' => DropshipOrderTracking::forVendor($vendor->id)
                ->whereBetween('created_at', [$lastMonth, $currentMonth])
                ->whereIn('fulfillment_status', ['delivered', 'completed'])
                ->sum('vendor_payout_amount'),
            'pending_payout' => DropshipOrderTracking::forVendor($vendor->id)
                ->where('vendor_payout_status', 'pending')
                ->whereIn('fulfillment_status', ['delivered', 'completed'])
                ->sum('vendor_payout_amount'),
            'total_earned' => DropshipOrderTracking::forVendor($vendor->id)
                ->whereIn('fulfillment_status', ['delivered', 'completed'])
                ->sum('vendor_payout_amount'),
        ];

        // Get monthly breakdown (last 6 months)
        $monthlyEarnings = DropshipOrderTracking::forVendor($vendor->id)
            ->whereIn('fulfillment_status', ['delivered', 'completed'])
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(vendor_payout_amount) as total'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Recent payouts
        $recentPayouts = DropshipOrderTracking::forVendor($vendor->id)
            ->with('transaction')
            ->whereIn('fulfillment_status', ['delivered', 'completed'])
            ->whereNotNull('vendor_payout_amount')
            ->orderBy('completed_at', 'desc')
            ->limit(20)
            ->get();

        return view('vendor_portal.earnings', compact('vendor', 'earnings', 'monthlyEarnings', 'recentPayouts'));
    }

    /**
     * Profile settings
     */
    public function profile()
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->route('vendor.login')->with('error', 'Session expired.');
        }

        $vendor->load('user', 'contact');

        return view('vendor_portal.profile', compact('vendor'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        $request->validate([
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        try {
            $vendor->update([
                'company_name' => $request->company_name,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            VendorAuthController::refreshVendorSession();

            return response()->json([
                'success' => true,
                'msg' => 'Profile updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => 'Failed to update profile.']);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $vendor = $this->getVendor();

        if (!$vendor || !$vendor->user) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $vendor->user;

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'msg' => 'Current password is incorrect.']);
        }

        try {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Password changed successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => 'Failed to change password.']);
        }
    }

    /**
     * Product Requests listing
     * Shows product requests from the vendor
     */
    public function productRequests(Request $request)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->route('vendor.login')->with('error', 'Session expired.');
        }

        if ($request->ajax()) {
            try {
                // Query product requests for this vendor (exclude soft-deleted)
                $query = DB::table('vendor_product_requests')
                    ->leftJoin('products', 'vendor_product_requests.product_id', '=', 'products.id')
                    ->leftJoin('users as reviewer', 'vendor_product_requests.reviewed_by', '=', 'reviewer.id')
                    ->where('vendor_product_requests.wp_vendor_id', $vendor->id)
                    ->whereNull('vendor_product_requests.deleted_at')
                    ->select([
                        'vendor_product_requests.id',
                        'vendor_product_requests.request_type',
                        'vendor_product_requests.proposed_name',
                        'vendor_product_requests.status',
                        'vendor_product_requests.notes',
                        'vendor_product_requests.admin_notes',
                        'vendor_product_requests.reviewed_at',
                        'vendor_product_requests.created_at',
                        'products.name as product_name',
                        'products.sku as product_sku',
                        'reviewer.first_name as reviewer_first_name',
                        'reviewer.last_name as reviewer_last_name',
                        'reviewer.username as reviewer_username',
                    ]);

            return DataTables::of($query)
                ->addColumn('date', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('M d, Y');
                })
                ->addColumn('product_display', function ($row) {
                    if ($row->request_type === 'new') {
                        return '<span class="badge badge-info">NEW</span> ' . ($row->proposed_name ?? 'New Product');
                    }
                    return $row->product_name ?? 'N/A';
                })
                ->addColumn('sku_display', function ($row) {
                    if ($row->request_type === 'new') {
                        return '<span class="text-muted">-</span>';
                    }
                    return '<code>' . ($row->product_sku ?? 'N/A') . '</code>';
                })
                ->addColumn('type_badge', function ($row) {
                    if ($row->request_type === 'new') {
                        return '<span class="badge badge-info">New Product</span>';
                    }
                    return '<span class="badge badge-secondary">Existing</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    $statusClasses = [
                        'pending' => 'badge-warning',
                        'approved' => 'badge-success',
                        'rejected' => 'badge-danger',
                    ];
                    $class = $statusClasses[$row->status] ?? 'badge-secondary';
                    return '<span class="badge ' . $class . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('reviewed_by_display', function ($row) {
                    if ($row->status === 'pending') {
                        return '<span style="color:#9ca3af;">-</span>';
                    }
                    $name = trim(($row->reviewer_first_name ?? '') . ' ' . ($row->reviewer_last_name ?? ''));
                    if (empty($name)) {
                        $name = $row->reviewer_username ?? 'Admin';
                    }
                    $date = $row->reviewed_at ? \Carbon\Carbon::parse($row->reviewed_at)->format('M d, Y') : '';
                    return '<span style="font-size:12px;">' . htmlspecialchars($name) . ($date ? '<br><span style="color:#9ca3af;font-size:11px;">' . $date . '</span>' : '') . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<button type="button" class="btn btn-sm btn-outline-primary view-request" data-id="' . $row->id . '" title="View"><i class="bi bi-eye"></i></button>';
                    
                    // Only show edit/delete for pending requests
                    if ($row->status === 'pending') {
                        $actions .= '<button type="button" class="btn btn-sm btn-outline-warning edit-request" data-id="' . $row->id . '" title="Edit"><i class="bi bi-pencil"></i></button>';
                        $actions .= '<button type="button" class="btn btn-sm btn-outline-danger delete-request" data-id="' . $row->id . '" title="Delete"><i class="bi bi-trash"></i></button>';
                    }
                    
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['product_display', 'sku_display', 'type_badge', 'status_badge', 'reviewed_by_display', 'action'])
                ->make(true);
            } catch (\Exception $e) {
                Log::error('Vendor product requests query error', [
                    'vendor_id' => $vendor->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'draw' => intval($request->input('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Error loading product requests. Please try again.'
                ], 500);
            }
        }

        // Get counts for stats (exclude soft-deleted)
        $stats = [
            'pending' => DB::table('vendor_product_requests')->where('wp_vendor_id', $vendor->id)->whereNull('deleted_at')->where('status', 'pending')->count(),
            'approved' => DB::table('vendor_product_requests')->where('wp_vendor_id', $vendor->id)->whereNull('deleted_at')->where('status', 'approved')->count(),
            'rejected' => DB::table('vendor_product_requests')->where('wp_vendor_id', $vendor->id)->whereNull('deleted_at')->where('status', 'rejected')->count(),
        ];

        // Get categories and brands for edit modal
        $categories = DB::table('categories')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $brands = DB::table('brands')
            ->whereNull('deleted_at')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('vendor_portal.product_requests', compact('vendor', 'stats', 'categories', 'brands'));
    }

    /**
     * Create Product Request page
     */
    public function createProductRequest()
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->route('vendor.login')->with('error', 'Session expired.');
        }

        // Get categories for new product form
        $categories = DB::table('categories')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Get brands
        $brands = DB::table('brands')
            ->whereNull('deleted_at')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Get units
        $units = DB::table('units')
            ->whereNull('deleted_at')
            ->select('id', 'actual_name', 'short_name')
            ->orderBy('actual_name')
            ->get();

        // Get variation templates with their values
        $variation_templates = DB::table('variation_templates')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Get variation values for each template
        $variation_values = DB::table('variation_value_templates')
            ->select('id', 'variation_template_id', 'name')
            ->orderBy('name')
            ->get()
            ->groupBy('variation_template_id');

        return view('vendor_portal.product_requests_create', compact('vendor', 'categories', 'brands', 'units', 'variation_templates', 'variation_values'));
    }

    /**
     * Get product catalog for existing product selection
     */
    public function getProductCatalog(Request $request)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        try {
            $search = $request->get('search', '');
            $page = (int) $request->get('page', 1);
            $perPage = 20;

            // Build base query for counting
            $countQuery = DB::table('products')
                ->where('is_inactive', 0)
                ->whereNotIn('products.id', function ($sub) use ($vendor) {
                    $sub->select('product_id')
                        ->from('products_wp_vendors_table_pivot')
                        ->where('wp_vendor_id', $vendor->id);
                });

            if ($search) {
                $countQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            }

            $total = $countQuery->count();

            // Get products with variant counts
            $productsQuery = DB::table('products')
                ->where('products.is_inactive', 0)
                ->whereNotIn('products.id', function ($sub) use ($vendor) {
                    $sub->select('product_id')
                        ->from('products_wp_vendors_table_pivot')
                        ->where('wp_vendor_id', $vendor->id);
                });

            if ($search) {
                $productsQuery->where(function ($q) use ($search) {
                    $q->where('products.name', 'like', "%{$search}%")
                        ->orWhere('products.sku', 'like', "%{$search}%");
                });
            }

            $products = $productsQuery
                ->select('products.id', 'products.name', 'products.sku', 'products.image', 'products.type')
                ->orderBy('products.name')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            // Get variant counts for these products
            $productIds = $products->pluck('id')->toArray();
            
            $variantCounts = [];
            $inventoryProductIds = [];
            if (!empty($productIds)) {
                $variants = DB::table('variations')
                    ->whereIn('product_id', $productIds)
                    ->where('name', '!=', 'DUMMY')
                    ->select('product_id', DB::raw('COUNT(*) as count'))
                    ->groupBy('product_id')
                    ->get();
                
                foreach ($variants as $v) {
                    $variantCounts[$v->product_id] = $v->count;
                }

                $inventoryProductIds = DB::table('products_wp_vendors_table_pivot')
                    ->where('wp_vendor_id', $vendor->id)
                    ->whereIn('product_id', $productIds)
                    ->pluck('product_id')
                    ->toArray();
            }

            // Format products
            $products = $products->map(function ($product) use ($variantCounts, $inventoryProductIds) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'image' => $product->image ? asset('uploads/img/' . $product->image) : null,
                    'type' => $product->type,
                    'variant_count' => $variantCounts[$product->id] ?? 0,
                    'in_inventory' => in_array($product->id, $inventoryProductIds, true),
                ];
            });

            return response()->json([
                'success' => true,
                'products' => $products,
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'has_more' => ($page * $perPage) < $total,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load product catalog', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'msg' => 'Failed to load products: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit request for existing products
     */
    public function submitExistingProductRequest(Request $request)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|integer|exists:products,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->product_ids as $productId) {
                // Check if already requested
                $existing = DB::table('vendor_product_requests')
                    ->where('wp_vendor_id', $vendor->id)
                    ->where('product_id', $productId)
                    ->where('request_type', 'existing')
                    ->whereIn('status', ['pending', 'approved'])
                    ->first();

                if ($existing) {
                    continue; // Skip if already requested
                }

                DB::table('vendor_product_requests')->insert([
                    'wp_vendor_id' => $vendor->id,
                    'request_type' => 'existing',
                    'product_id' => $productId,
                    'status' => 'pending',
                    'notes' => $request->notes,
                    'requested_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Product request submitted successfully! It will be reviewed by admin.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit product request', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Failed to submit request.']);
        }
    }

    /**
     * Submit request for new product
     */
    public function submitNewProductRequest(Request $request)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        $request->validate([
            'proposed_name' => 'required|string|max:255',
            'proposed_sku' => 'nullable|string|max:100',
            'proposed_description' => 'nullable|string',
            'proposed_category_id' => 'nullable|integer',
            'proposed_brand_id' => 'nullable|integer',
            'proposed_cost_price' => 'nullable|numeric|min:0',
            'proposed_selling_price' => 'nullable|numeric|min:0',
            'proposed_type' => 'nullable|string|in:single,variable',
            'proposed_image' => 'nullable|image|max:5120',
            'proposed_variation_template' => 'nullable|integer',
            'proposed_variations' => 'nullable|array',
            'proposed_variations.*.value' => 'required_with:proposed_variations|string|max:255',
            'proposed_variations.*.cost_price' => 'nullable|numeric|min:0',
            'proposed_variations.*.selling_price' => 'nullable|numeric|min:0',
            'proposed_variations.*.sku' => 'nullable|string|max:100',
            'proposed_variations.*.barcode' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $imagePath = null;
            if ($request->hasFile('proposed_image')) {
                $imagePath = $request->file('proposed_image')->store('vendor_requests', 'public');
            }

            // Prepare variations JSON if product is variable
            $variationsJson = null;
            if ($request->proposed_type === 'variable' && $request->proposed_variations) {
                $variationsJson = json_encode($request->proposed_variations);
            }

            DB::table('vendor_product_requests')->insert([
                'wp_vendor_id' => $vendor->id,
                'request_type' => 'new',
                'proposed_name' => $request->proposed_name,
                'proposed_sku' => $request->proposed_sku,
                'proposed_description' => $request->proposed_description,
                'proposed_category_id' => $request->proposed_category_id,
                'proposed_brand_id' => $request->proposed_brand_id,
                'proposed_cost_price' => $request->proposed_cost_price,
                'proposed_selling_price' => $request->proposed_selling_price,
                'proposed_type' => $request->proposed_type ?? 'single',
                'proposed_image' => $imagePath,
                'proposed_variation_template' => $request->proposed_variation_template,
                'proposed_variations' => $variationsJson,
                'status' => 'pending',
                'notes' => $request->notes,
                'requested_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'New product request submitted successfully! It will be reviewed by admin.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to submit new product request', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Failed to submit request.']);
        }
    }

    /**
     * Show a specific product request details
     */
    public function showProductRequest($id)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        try {
            $request = DB::table('vendor_product_requests')
                ->leftJoin('products', 'vendor_product_requests.product_id', '=', 'products.id')
                ->leftJoin('categories', 'vendor_product_requests.proposed_category_id', '=', 'categories.id')
                ->leftJoin('brands', 'vendor_product_requests.proposed_brand_id', '=', 'brands.id')
                ->where('vendor_product_requests.id', $id)
                ->where('vendor_product_requests.wp_vendor_id', $vendor->id)
                ->select([
                    'vendor_product_requests.*',
                    'products.name as product_name',
                    'products.sku as product_sku',
                    'categories.name as category_name',
                    'brands.name as brand_name',
                ])
                ->first();

            if (!$request) {
                return response()->json(['success' => false, 'msg' => 'Request not found.'], 404);
            }

            // Decode variations if exists
            if ($request->proposed_variations) {
                $request->proposed_variations = json_decode($request->proposed_variations, true);
            }

            return response()->json([
                'success' => true,
                'request' => $request,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch product request', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Failed to fetch request details.']);
        }
    }

    /**
     * Update a product request
     */
    public function updateProductRequest(Request $request, $id)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        try {
            // Check if the request exists and belongs to this vendor
            $existingRequest = DB::table('vendor_product_requests')
                ->where('id', $id)
                ->where('wp_vendor_id', $vendor->id)
                ->first();

            if (!$existingRequest) {
                return response()->json(['success' => false, 'msg' => 'Request not found.'], 404);
            }

            // Only allow updates for pending requests
            if ($existingRequest->status !== 'pending') {
                return response()->json(['success' => false, 'msg' => 'Only pending requests can be updated.'], 400);
            }

            // Build update data based on request type
            $updateData = [
                'notes' => $request->notes,
                'updated_at' => now(),
            ];

            if ($existingRequest->request_type === 'new') {
                // Validate for new product requests
                $request->validate([
                    'proposed_name' => 'required|string|max:255',
                    'proposed_sku' => 'nullable|string|max:100',
                    'proposed_barcode' => 'nullable|string|max:100',
                    'proposed_description' => 'nullable|string',
                    'proposed_category_id' => 'nullable|integer',
                    'proposed_brand_id' => 'nullable|integer',
                    'proposed_cost_price' => 'nullable|numeric|min:0',
                    'proposed_selling_price' => 'nullable|numeric|min:0',
                    'proposed_type' => 'nullable|string|in:single,variable',
                    'proposed_variations' => 'nullable|array',
                    'proposed_variations.*.value' => 'nullable|string|max:255',
                    'proposed_variations.*.sku' => 'nullable|string|max:100',
                    'proposed_variations.*.cost_price' => 'nullable|numeric|min:0',
                    'proposed_variations.*.selling_price' => 'nullable|numeric|min:0',
                    'notes' => 'nullable|string|max:1000',
                ]);

                $updateData['proposed_name'] = $request->proposed_name;
                $updateData['proposed_sku'] = $request->proposed_sku;
                $updateData['proposed_barcode'] = $request->proposed_barcode;
                $updateData['proposed_description'] = $request->proposed_description;
                $updateData['proposed_category_id'] = $request->proposed_category_id ?: null;
                $updateData['proposed_brand_id'] = $request->proposed_brand_id ?: null;
                $updateData['proposed_cost_price'] = $request->proposed_cost_price;
                $updateData['proposed_selling_price'] = $request->proposed_selling_price;
                $updateData['proposed_type'] = $request->proposed_type ?? 'single';

                // Handle variations
                if ($request->proposed_variations && is_array($request->proposed_variations)) {
                    // Filter out empty variations
                    $validVariations = array_filter($request->proposed_variations, function($v) {
                        return !empty($v['value']);
                    });
                    
                    if (!empty($validVariations)) {
                        $updateData['proposed_variations'] = json_encode(array_values($validVariations));
                    } else {
                        $updateData['proposed_variations'] = null;
                    }
                }
                
                if ($request->proposed_variation_template) {
                    $updateData['proposed_variation_template'] = $request->proposed_variation_template;
                }
            }

            DB::table('vendor_product_requests')
                ->where('id', $id)
                ->update($updateData);

            return response()->json([
                'success' => true,
                'msg' => 'Request updated successfully!',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update product request', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Failed to update request.']);
        }
    }

    /**
     * Delete a product request
     */
    public function deleteProductRequest($id)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        try {
            // Check if the request exists and belongs to this vendor
            $existingRequest = DB::table('vendor_product_requests')
                ->where('id', $id)
                ->where('wp_vendor_id', $vendor->id)
                ->first();

            if (!$existingRequest) {
                return response()->json(['success' => false, 'msg' => 'Request not found.'], 404);
            }

            // Only allow deletion for pending requests
            if ($existingRequest->status !== 'pending') {
                return response()->json(['success' => false, 'msg' => 'Only pending requests can be deleted.'], 400);
            }

            // Soft delete (set deleted_at)
            DB::table('vendor_product_requests')
                ->where('id', $id)
                ->update(['deleted_at' => now()]);

            return response()->json([
                'success' => true,
                'msg' => 'Request deleted successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete product request', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Failed to delete request.']);
        }
    }

    /**
     * Purchase Orders listing
     * Shows purchase orders sent to the vendor
     */
    public function purchaseOrders(Request $request)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->route('vendor.login')->with('error', 'Session expired.');
        }

        if ($request->ajax() || $request->has('draw')) {
            // Debug: Log vendor info to help troubleshoot
            Log::info('Vendor Portal - Purchase Orders Query', [
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->name,
                'vendor_contact_id' => $vendor->contact_id,
                'vendor_user_id' => $vendor->user_id,
            ]);
            
            // Query purchase orders for this vendor (from transactions table)
            $query = $this->vendorPurchaseOrderQuery($vendor)
                ->select([
                    't.id',
                    't.ref_no',
                    't.invoice_no',
                    't.transaction_date',
                    't.final_total',
                    't.status',
                    't.payment_status',
                    't.created_at',
                ]);

            return DataTables::of($query)
                ->addColumn('date', function ($row) {
                    return \Carbon\Carbon::parse($row->transaction_date)->format('M d, Y');
                })
                ->addColumn('total', function ($row) {
                    return '$' . number_format($row->final_total ?? 0, 2);
                })
                ->addColumn('status_badge', function ($row) {
                    $statusClasses = [
                        'draft' => 'badge-secondary',
                        'ordered' => 'badge-info',
                        'partial' => 'badge-warning',
                        'received' => 'badge-success',
                    ];
                    $class = $statusClasses[$row->status] ?? 'badge-secondary';
                    return '<span class="badge ' . $class . '">' . ucfirst($row->status ?? 'N/A') . '</span>';
                })
                ->addColumn('payment_badge', function ($row) {
                    $paymentClasses = [
                        'paid' => 'badge-success',
                        'partial' => 'badge-warning',
                        'due' => 'badge-danger',
                    ];
                    $class = $paymentClasses[$row->payment_status] ?? 'badge-secondary';
                    return '<span class="badge ' . $class . '">' . ucfirst($row->payment_status ?? 'N/A') . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('vendor.purchase-orders.show', $row->id) . '" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</a>';
                })
                ->rawColumns(['status_badge', 'payment_badge', 'action'])
                ->make(true);
        }

        // Get linked contact info for debugging
        $linkedContact = null;
        if ($vendor->contact_id) {
            $linkedContact = DB::table('contacts')->where('id', $vendor->contact_id)->first();
        }
        
        // Get linked user info for debugging
        $linkedUser = null;
        if ($vendor->user_id) {
            $linkedUser = DB::table('users')->where('id', $vendor->user_id)->first();
        }
        
        // Get stats for purchase orders
        $statsQuery = $this->vendorPurchaseOrderQuery($vendor);
        
        $stats = [
            'draft' => (clone $statsQuery)->where('t.status', 'draft')->count(),
            'quotation' => (clone $statsQuery)->where('t.status', 'quotation')->count(),
            'ordered' => (clone $statsQuery)->where('t.status', 'ordered')->count(),
            'partial' => (clone $statsQuery)->where('t.status', 'partial')->count(),
            'received' => (clone $statsQuery)->where('t.status', 'received')->count(),
        ];
        
        // Combine draft and quotation
        $stats['draft'] = $stats['draft'] + $stats['quotation'];
        
        return view('vendor_portal.purchase_orders', compact('vendor', 'linkedContact', 'linkedUser', 'stats'));
    }

    /**
     * Show single purchase order details
     */
    public function showPurchaseOrder($id)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->route('vendor.login')->with('error', 'Session expired.');
        }

        $purchaseOrder = $this->vendorPurchaseOrderQuery($vendor)
            ->where('t.id', $id)
            ->first();

        if (!$purchaseOrder) {
            return redirect()->route('vendor.purchase-orders')->with('error', 'Purchase order not found.');
        }

        // Get purchase lines
        $purchaseLines = DB::table('purchase_lines')
            ->join('products', 'purchase_lines.product_id', '=', 'products.id')
            ->leftJoin('variations', 'purchase_lines.variation_id', '=', 'variations.id')
            ->where('purchase_lines.transaction_id', $id)
            ->select([
                'purchase_lines.*',
                'products.name as product_name',
                'products.sku as product_sku',
                'variations.name as variation_name',
                'variations.sub_sku',
            ])
            ->get();

        return view('vendor_portal.purchase_orders_show', compact('vendor', 'purchaseOrder', 'purchaseLines'));
    }

    /**
     * Create Vendor Purchase Order page
     * Shows form to create a new purchase order from vendor's inventory products
     */
    public function createVendorPurchaseOrder()
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->route('vendor.login')->with('error', 'Session expired.');
        }

        // Get vendor's inventory products count
        $productCount = DB::table('products_wp_vendors_table_pivot')
            ->where('wp_vendor_id', $vendor->id)
            ->count();

        return view('vendor_portal.purchase_orders_create', compact('vendor', 'productCount'));
    }

    /**
     * Get vendor inventory products for autocomplete/search
     * Only shows products that belong to vendor's inventory
     */
    public function getVendorInventoryProducts(Request $request)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        try {
            $search = $request->get('term', '');
            $page = (int) $request->get('page', 1);
            $perPage = 100; // Show more products at once

            // Get products from vendor's inventory (products_wp_vendors_table_pivot)
            $query = DB::table('products as p')
                ->join('products_wp_vendors_table_pivot as pivot', 'p.id', '=', 'pivot.product_id')
                ->leftJoin('variations as v', function($join) {
                    $join->on('p.id', '=', 'v.product_id')
                         ->whereNull('v.deleted_at');
                })
                ->where('pivot.wp_vendor_id', $vendor->id)
                ->where('p.is_inactive', 0);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('p.name', 'like', "%{$search}%")
                        ->orWhere('p.sku', 'like', "%{$search}%")
                        ->orWhere('v.sub_sku', 'like', "%{$search}%")
                        ->orWhere('v.name', 'like', "%{$search}%");
                });
            }

            // Get total count
            $total = $query->distinct('p.id')->count('p.id');

            // Get products with variations
            $products = DB::table('products as p')
                ->join('products_wp_vendors_table_pivot as pivot', 'p.id', '=', 'pivot.product_id')
                ->leftJoin('variations as v', function($join) {
                    $join->on('p.id', '=', 'v.product_id')
                         ->whereNull('v.deleted_at');
                })
                ->leftJoin('variation_vendor_pivot as vvp', function($join) use ($vendor) {
                    $join->on('v.id', '=', 'vvp.variation_id')
                         ->where('vvp.wp_vendor_id', '=', $vendor->id);
                })
                ->where('pivot.wp_vendor_id', $vendor->id)
                ->where('p.is_inactive', 0);

            if ($search) {
                $products->where(function ($q) use ($search) {
                    $q->where('p.name', 'like', "%{$search}%")
                        ->orWhere('p.sku', 'like', "%{$search}%")
                        ->orWhere('v.sub_sku', 'like', "%{$search}%")
                        ->orWhere('v.name', 'like', "%{$search}%");
                });
            }

            $products = $products
                ->select([
                    'p.id as product_id',
                    'p.name as product_name',
                    'p.sku as product_sku',
                    'p.image',
                    'p.type',
                    'v.id as variation_id',
                    'v.name as variation_name',
                    'v.sub_sku',
                    'v.default_purchase_price',
                    'v.default_sell_price',
                    'pivot.vendor_cost_price',
                    'pivot.dropship_selling_price',
                    'pivot.vendor_stock_qty as product_stock',
                    'vvp.vendor_cost_price as variation_cost_price',
                    'vvp.selling_price as variation_sell_price',
                    'vvp.vendor_stock_qty as variation_stock',
                ])
                ->orderBy('p.name')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            // Format products for autocomplete
            $formattedProducts = [];
            foreach ($products as $product) {
                // Skip DUMMY variations but include single products
                if ($product->variation_name === 'DUMMY' && $product->type !== 'single') {
                    continue;
                }

                $displayName = $product->product_name;
                $sku = $product->product_sku;
                $stock = $product->product_stock ?? 0;
                $costPrice = $product->vendor_cost_price ?? $product->default_purchase_price ?? 0;

                // For variable products, show variation info
                if ($product->variation_id && $product->variation_name !== 'DUMMY') {
                    $displayName .= ' - ' . $product->variation_name;
                    $sku = $product->sub_sku ?? $product->product_sku;
                    $stock = $product->variation_stock ?? $product->product_stock ?? 0;
                    $costPrice = $product->variation_cost_price ?? $product->vendor_cost_price ?? $product->default_purchase_price ?? 0;
                }

                $formattedProducts[] = [
                    'id' => $product->product_id,
                    'product_id' => $product->product_id,
                    'variation_id' => $product->variation_id,
                    'name' => $displayName,
                    'text' => $displayName . ' (' . $sku . ')',
                    'sku' => $sku,
                    'sub_sku' => $product->sub_sku,
                    'image' => $product->image ? asset('uploads/img/' . $product->image) : null,
                    'type' => $product->type,
                    'stock' => $stock,
                    'cost_price' => $costPrice,
                    'sell_price' => $product->variation_sell_price ?? $product->dropship_selling_price ?? $product->default_sell_price ?? 0,
                ];
            }

            return response()->json([
                'success' => true,
                'products' => $formattedProducts,
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'has_more' => ($page * $perPage) < $total,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load vendor inventory products', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'msg' => 'Failed to load products: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new vendor purchase order
     * Creates a purchase order from vendor's inventory products
     */
    public function storeVendorPurchaseOrder(Request $request)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|integer',
            'products.*.variation_id' => 'nullable|integer',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
            'delivery_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            // Calculate totals
            $totalBeforeTax = 0;
            foreach ($request->products as $item) {
                $totalBeforeTax += $item['quantity'] * $item['unit_cost'];
            }

            // Get business_id from the vendor's contact
            $contact = DB::table('contacts')->where('id', $vendor->contact_id)->first();
            $businessId = $contact ? $contact->business_id : 1;

            // Get default location
            $location = DB::table('business_locations')
                ->where('business_id', $businessId)
                ->first();

            // Generate sequential reference number (VPO00001 format)
            $lastPO = DB::table('transactions')
                ->where('type', 'purchase_order')
                ->where('sub_type', 'vendor_po')
                ->whereRaw("ref_no REGEXP '^VPO[0-9]+$'")
                ->orderByRaw('CAST(SUBSTRING(ref_no, 4) AS UNSIGNED) DESC')
                ->first();
            
            if ($lastPO && preg_match('/^VPO(\d+)$/i', $lastPO->ref_no, $matches)) {
                $nextNum = intval($matches[1]) + 1;
            } else {
                $nextNum = 1;
            }
            
            $refNo = 'VPO' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);

            // Create the purchase order transaction
            $transactionId = DB::table('transactions')->insertGetId([
                'business_id' => $businessId,
                'location_id' => $location ? $location->id : 1,
                'type' => 'purchase_order',
                'sub_type' => 'vendor_po',
                'status' => 'ordered',
                'contact_id' => $vendor->contact_id,
                'ref_no' => $refNo,
                'transaction_date' => now(),
                'total_before_tax' => $totalBeforeTax,
                'final_total' => $totalBeforeTax,
                'additional_notes' => $request->notes,
                'delivery_date' => $request->delivery_date,
                'created_by' => $vendor->user_id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create purchase lines
            foreach ($request->products as $item) {
                // Get variation details
                $variationId = $item['variation_id'];
                
                if (!$variationId) {
                    // For single products, get the DUMMY variation
                    $variation = DB::table('variations')
                        ->where('product_id', $item['product_id'])
                        ->whereNull('deleted_at')
                        ->first();
                    $variationId = $variation ? $variation->id : null;
                }

                $lineTotal = $item['quantity'] * $item['unit_cost'];

                DB::table('purchase_lines')->insert([
                    'transaction_id' => $transactionId,
                    'product_id' => $item['product_id'],
                    'variation_id' => $variationId,
                    'quantity' => $item['quantity'],
                    'pp_without_discount' => $item['unit_cost'],
                    'purchase_price' => $item['unit_cost'],
                    'purchase_price_inc_tax' => $item['unit_cost'],
                    'item_tax' => 0,
                    'tax_id' => null,
                    'quantity_returned' => 0,
                    'quantity_sold' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            Log::info('Vendor created purchase order', [
                'vendor_id' => $vendor->id,
                'transaction_id' => $transactionId,
                'ref_no' => $refNo,
                'total' => $totalBeforeTax,
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Purchase order created successfully!',
                'ref_no' => $refNo,
                'transaction_id' => $transactionId,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create vendor purchase order', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'msg' => 'Failed to create purchase order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Purchase Receipts listing
     * Shows purchase receipts/invoices for the vendor
     */
    public function purchaseReceipts(Request $request)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->route('vendor.login')->with('error', 'Session expired.');
        }

        // Build query for vendor's purchase receipts
        $baseQuery = function() use ($vendor) {
            return DB::table('transactions')
                ->whereIn('type', ['purchase_return', 'purchase'])
                ->where('status', 'received')
                ->where(function ($q) use ($vendor) {
                    if (!empty($vendor->contact_id)) {
                        $q->where('contact_id', $vendor->contact_id);
                    }
                    if (!empty($vendor->user_id)) {
                        $q->orWhere('created_by', $vendor->user_id);
                    }
                });
        };

        if ($request->ajax()) {
            $query = $baseQuery()->select([
                'id',
                'ref_no',
                'invoice_no',
                'type',
                'status',
                'transaction_date',
                'final_total',
                'payment_status',
                'created_at',
            ]);

            return DataTables::of($query)
                ->addColumn('date', function ($row) {
                    return \Carbon\Carbon::parse($row->transaction_date)->format('M d, Y');
                })
                ->addColumn('type_badge', function ($row) {
                    if ($row->type === 'purchase_return') {
                        return '<span class="badge badge-warning">Return</span>';
                    }
                    return '<span class="badge badge-success">Received</span>';
                })
                ->addColumn('total', function ($row) {
                    return '$' . number_format($row->final_total ?? 0, 2);
                })
                ->addColumn('payment_badge', function ($row) {
                    $statusClasses = [
                        'received' => 'badge-success',
                        'pending' => 'badge-warning',
                        'ordered' => 'badge-info',
                    ];
                    $class = $statusClasses[$row->status] ?? 'badge-secondary';
                    return '<span class="badge ' . $class . '">' . ucfirst($row->status ?? 'N/A') . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('vendor.purchase-receipts.show', $row->id) . '" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</a>';
                })
                ->rawColumns(['type_badge', 'payment_badge', 'action'])
                ->make(true);
        }

        // Calculate stats
        $stats = [
            'total' => $baseQuery()->count(),
            'received' => $baseQuery()->where('status', 'received')->count(),
            'pending' => $baseQuery()->where('status', 'pending')->count(),
        ];

        return view('vendor_portal.purchase_receipts', compact('vendor', 'stats'));
    }

    /**
     * Show single purchase receipt details
     */
    public function showPurchaseReceipt($id)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->route('vendor.login')->with('error', 'Session expired.');
        }

        $purchaseReceipt = DB::table('transactions')
            ->where('id', $id)
            ->whereIn('type', ['purchase_return', 'purchase'])
            ->where('contact_id', $vendor->contact_id)
            ->where('status', 'received')
            ->first();

        if (!$purchaseReceipt) {
            return redirect()->route('vendor.purchase-receipts')->with('error', 'Purchase receipt not found.');
        }

        // Get purchase lines
        $purchaseLines = DB::table('purchase_lines')
            ->join('products', 'purchase_lines.product_id', '=', 'products.id')
            ->leftJoin('variations', 'purchase_lines.variation_id', '=', 'variations.id')
            ->where('purchase_lines.transaction_id', $id)
            ->select([
                'purchase_lines.*',
                'products.name as product_name',
                'products.sku as product_sku',
                'variations.name as variation_name',
                'variations.sub_sku',
            ])
            ->get();

        return view('vendor_portal.purchase_receipts_show', compact('vendor', 'purchaseReceipt', 'purchaseLines'));
    }

    /**
     * Show the form for creating a new purchase receipt
     */
    public function createPurchaseReceipt()
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->route('vendor.login')->with('error', 'Session expired.');
        }

        // Count vendor's products
        $productCount = DB::table('products_wp_vendors_table_pivot')
            ->where('wp_vendor_id', $vendor->id)
            ->count();

        return view('vendor_portal.purchase_receipts_create', compact('vendor', 'productCount'));
    }

    /**
     * Store a new purchase receipt and update inventory stock
     */
    public function storePurchaseReceipt(Request $request)
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return response()->json(['success' => false, 'msg' => 'Session expired.'], 401);
        }

        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|integer',
            'products.*.variation_id' => 'nullable|integer',
            'products.*.quantity' => 'required|numeric|min:0.001',
            'products.*.unit_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $businessId = $vendor->business_id ?? session('user.business_id');

            if (empty($businessId)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'msg' => 'Business not found for this vendor. Please contact support.'
                ], 422);
            }

            $location = DB::table('business_locations')->where('business_id', $businessId)->first();

            // Generate reference number for purchase receipt (VPR00001 format)
            $lastReceipt = DB::table('transactions')
                ->where('type', 'purchase')
                ->where('sub_type', 'vendor_receipt')
                ->where('ref_no', 'like', 'VPR%')
                ->orderBy('id', 'desc')
                ->first();

            $newNum = 1;
            if ($lastReceipt && preg_match('/VPR(\d+)/', $lastReceipt->ref_no, $matches)) {
                $newNum = (int)$matches[1] + 1;
            }
            $refNo = 'VPR' . str_pad($newNum, 5, '0', STR_PAD_LEFT);

            // Calculate total
            $totalBeforeTax = 0;
            foreach ($request->products as $item) {
                $totalBeforeTax += $item['quantity'] * $item['unit_cost'];
            }

            // Create the purchase transaction (received status)
            $transactionId = DB::table('transactions')->insertGetId([
                'business_id' => $businessId,
                'location_id' => $location ? $location->id : 1,
                'type' => 'purchase',
                'sub_type' => 'vendor_receipt',
                'status' => 'received',
                'payment_status' => 'paid',
                'contact_id' => $vendor->contact_id,
                'ref_no' => $refNo,
                'transaction_date' => now(),
                'total_before_tax' => $totalBeforeTax,
                'final_total' => $totalBeforeTax,
                'additional_notes' => $request->notes,
                'created_by' => $vendor->user_id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add purchase lines and update stock
            foreach ($request->products as $item) {
                $variationId = $item['variation_id'];
                $productId = $item['product_id'];
                $quantity = $item['quantity'];

                // If no variation_id, get the DUMMY variation
                if (!$variationId) {
                    $variation = DB::table('variations')
                        ->where('product_id', $productId)
                        ->whereNull('deleted_at')
                        ->where('name', 'DUMMY')
                        ->first();
                    $variationId = $variation->id ?? null;
                }

                // Insert purchase line
                DB::table('purchase_lines')->insert([
                    'transaction_id' => $transactionId,
                    'product_id' => $productId,
                    'variation_id' => $variationId,
                    'quantity' => $quantity,
                    'pp_without_discount' => $item['unit_cost'],
                    'purchase_price' => $item['unit_cost'],
                    'item_tax' => 0,
                    'purchase_price_inc_tax' => $item['unit_cost'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update stock in vendor's inventory (products_wp_vendors_table_pivot)
                DB::table('products_wp_vendors_table_pivot')
                    ->where('wp_vendor_id', $vendor->id)
                    ->where('product_id', $productId)
                    ->increment('vendor_stock_qty', $quantity);

                // Also update variation_vendor_pivot if it's a variable product
                if ($variationId) {
                    $variationPivot = DB::table('variation_vendor_pivot')
                        ->where('wp_vendor_id', $vendor->id)
                        ->where('variation_id', $variationId)
                        ->first();

                    if ($variationPivot) {
                        DB::table('variation_vendor_pivot')
                            ->where('wp_vendor_id', $vendor->id)
                            ->where('variation_id', $variationId)
                            ->increment('vendor_stock_qty', $quantity);
                    }
                }

                // Update variation_location_details (main stock tracking)
                if ($variationId && $location) {
                    $vld = DB::table('variation_location_details')
                        ->where('variation_id', $variationId)
                        ->where('location_id', $location->id)
                        ->first();

                    if ($vld) {
                        DB::table('variation_location_details')
                            ->where('id', $vld->id)
                            ->increment('qty_available', $quantity);
                    } else {
                        // Create new entry
                        DB::table('variation_location_details')->insert([
                            'product_id' => $productId,
                            'product_variation_id' => $variationId,
                            'variation_id' => $variationId,
                            'location_id' => $location->id,
                            'qty_available' => $quantity,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Purchase Receipt created successfully! Stock has been updated.',
                'ref_no' => $refNo
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create vendor purchase receipt: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'Failed to create purchase receipt: ' . $e->getMessage()], 500);
        }
    }
}

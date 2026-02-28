<?php

namespace App\Http\Controllers;

use App\Models\WpVendor;
use App\Contact;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class DropshipVendorController extends Controller
{
    /**
     * Display a listing of the vendors.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('dropship.manage_vendors')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $vendors = WpVendor::where('business_id', $business_id)
                ->select(['id', 'name', 'company_name', 'email', 'vendor_type', 'default_markup_percentage', 'status']);

            if ($request->filled('vendor_type')) {
                $vendors->where('vendor_type', $request->input('vendor_type'));
            }

            return DataTables::of($vendors)
                ->addColumn('display_name', function ($row) {
                    return $row->company_name ?: $row->name;
                })
                ->addColumn('vendor_type_badge', function ($row) {
                    return $row->vendor_type_badge;
                })
                ->addColumn('products_count', function ($row) {
                    return $row->products()->count();
                })
                ->addColumn('pending_orders', function ($row) {
                    return $row->pendingOrders()->count();
                })
                ->addColumn('status_badge', function ($row) {
                    return $row->status_badge;
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="tw-flex tw-gap-1">';
                    
                    if (auth()->user()->can('dropship.manage_vendors')) {
                        $actions .= '<a href="' . route('dropship.vendors.show', $row->id) . '" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-warning"><i class="fas fa-eye"></i></a>';
                        $actions .= '<a href="' . route('dropship.vendors.products', $row->id) . '" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-primary"><i class="fas fa-box"></i> Products</a>';
                        
                        if ($row->isEditable()) {
                            $actions .= '<a href="' . route('dropship.vendors.edit', $row->id) . '" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-warning"><i class="fas fa-edit"></i></a>';
                            $actions .= '<button class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-error delete-vendor" data-href="' . route('dropship.vendors.destroy', $row->id) . '"><i class="fas fa-trash"></i></button>';
                        }
                    }
                    
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['vendor_type_badge', 'status_badge', 'action'])
                ->make(true);
        }

        $vendorTypes = WpVendor::getVendorTypes();

        return view('dropship.vendors.index', compact('vendorTypes'));
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create(Request $request)
    {
        if (!auth()->user()->can('dropship.manage_vendors')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $vendorTypes = WpVendor::getCreatableVendorTypes();

        // Get suppliers for linking
        $suppliers = Contact::where('business_id', $business_id)
            ->where('type', 'supplier')
            ->pluck('name', 'id');

        return view('dropship.vendors.create', compact('vendorTypes', 'suppliers'));
    }

    /**
     * Store a newly created vendor.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('dropship.manage_vendors')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'vendor_type' => 'required|in:erp,erp_dropship',
        ]);

        try {
            $business_id = $request->session()->get('user.business_id');
            
            $vendor = WpVendor::create([
                'business_id' => $business_id,
                'name' => $request->input('name'),
                'company_name' => $request->input('company_name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'vendor_type' => $request->input('vendor_type'),
                'default_markup_percentage' => $request->input('default_markup_percentage', 0),
                'commission_type' => $request->input('commission_type', 'percentage'),
                'commission_value' => $request->input('commission_value', 0),
                'status' => $request->input('status', 'active'),
                'contact_id' => $request->input('contact_id'),
            ]);

            return redirect()->route('dropship.vendors.index')
                ->with('status', ['success' => 1, 'msg' => 'Vendor created successfully.']);

        } catch (\Exception $e) {
            Log::error('Failed to create vendor', ['error' => $e->getMessage()]);
            return back()->with('status', ['success' => 0, 'msg' => 'Failed to create vendor.']);
        }
    }

    /**
     * Display the specified vendor.
     */
    public function show(Request $request, $id)
    {
        if (!auth()->user()->can('dropship.manage_vendors')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $vendor = WpVendor::where('business_id', $business_id)->findOrFail($id);

        // Get performance metrics
        $totalOrders = DB::table('dropship_order_tracking')
            ->where('wp_vendor_id', $vendor->id)
            ->count();
        
        $completedOrders = DB::table('dropship_order_tracking')
            ->where('wp_vendor_id', $vendor->id)
            ->where('fulfillment_status', 'completed')
            ->count();
        
        $pendingOrders = DB::table('dropship_order_tracking')
            ->where('wp_vendor_id', $vendor->id)
            ->whereIn('fulfillment_status', ['pending', 'vendor_notified', 'vendor_accepted', 'processing'])
            ->count();
        
        $completionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0;
        
        // Calculate average fulfillment time (hours between created_at and shipped_at)
        $avgFulfillmentHours = DB::table('dropship_order_tracking')
            ->where('wp_vendor_id', $vendor->id)
            ->where('fulfillment_status', 'completed')
            ->whereNotNull('shipped_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, shipped_at)) as avg_hours')
            ->value('avg_hours') ?? 0;
        
        // Total revenue from completed orders
        $totalRevenue = DB::table('dropship_order_tracking')
            ->where('wp_vendor_id', $vendor->id)
            ->where('fulfillment_status', 'completed')
            ->sum('vendor_payout_amount') ?? 0;
        
        $performance = [
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'pending_orders' => $pendingOrders,
            'completion_rate' => $completionRate,
            'avg_fulfillment_hours' => round($avgFulfillmentHours, 1),
            'total_revenue' => $totalRevenue,
        ];

        // Get recent orders
        $recentOrders = DB::table('dropship_order_tracking')
            ->where('wp_vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                $order = (object) $order;
                $order->transaction = DB::table('transactions')->find($order->transaction_id);
                $order->status_badge = $this->getOrderStatusBadge($order->fulfillment_status);
                $order->created_at = \Carbon\Carbon::parse($order->created_at);
                return $order;
            });

        return view('dropship.vendors.show', compact('vendor', 'performance', 'recentOrders'));
    }

    /**
     * Helper to get status badge HTML
     */
    private function getOrderStatusBadge($status)
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'vendor_notified' => '<span class="badge bg-info">Notified</span>',
            'vendor_accepted' => '<span class="badge bg-primary">Accepted</span>',
            'processing' => '<span class="badge bg-blue">Processing</span>',
            'shipped' => '<span class="badge bg-purple">Shipped</span>',
            'completed' => '<span class="badge bg-success">Completed</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
        ];
        return $badges[$status] ?? '<span class="badge bg-secondary">' . ucfirst($status) . '</span>';
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit(Request $request, $id)
    {
        if (!auth()->user()->can('dropship.manage_vendors')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $vendor = WpVendor::where('business_id', $business_id)->findOrFail($id);

        if (!$vendor->isEditable()) {
            return back()->with('status', ['success' => 0, 'msg' => 'WooCommerce vendors cannot be edited directly.']);
        }

        $vendorTypes = WpVendor::getCreatableVendorTypes();
        $suppliers = Contact::where('business_id', $business_id)
            ->where('type', 'supplier')
            ->pluck('name', 'id');

        return view('dropship.vendors.edit', compact('vendor', 'vendorTypes', 'suppliers'));
    }

    /**
     * Update the specified vendor.
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('dropship.manage_vendors')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        try {
            $business_id = $request->session()->get('user.business_id');
            $vendor = WpVendor::where('business_id', $business_id)->findOrFail($id);

            if (!$vendor->isEditable()) {
                return response()->json(['success' => false, 'msg' => 'WooCommerce vendors cannot be edited.']);
            }

            $updateData = [
                'name' => $request->input('name'),
                'company_name' => $request->input('company_name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'vendor_type' => $request->input('vendor_type', $vendor->vendor_type),
                'default_markup_percentage' => $request->input('default_markup_percentage', 0),
                'commission_type' => $request->input('commission_type', 'percentage'),
                'commission_value' => $request->input('commission_value', 0),
                'status' => $request->input('status', 'active'),
                'contact_id' => $request->input('contact_id'),
            ];

            // Handle password update if provided
            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($request->input('password'));
            }

            $vendor->update($updateData);

            // Sync vendor portal user (login uses users table)
            if ($vendor->isErpDropshipVendor()) {
                // Keep portal user's email aligned with vendor email
                if (!empty($vendor->user_id) && $vendor->user) {
                    if (!empty($vendor->email) && $vendor->user->email !== $vendor->email) {
                        $vendor->user->email = $vendor->email;
                    }
                }

                // If password provided, set it on the portal user (create if missing)
                if ($request->filled('password')) {
                    $plainPassword = $request->input('password');

                    if (!empty($vendor->user_id) && $vendor->user) {
                        $vendor->user->password = bcrypt($plainPassword);
                        $vendor->user->save();
                    } else {
                        $vendor->createPortalUser($plainPassword);
                    }
                } else {
                    // Save email change if we modified user email above
                    if (!empty($vendor->user_id) && $vendor->user && $vendor->user->isDirty('email')) {
                        $vendor->user->save();
                    }
                }
            }

            return redirect()->route('dropship.vendors.index')
                ->with('status', ['success' => 1, 'msg' => 'Vendor updated successfully.']);

        } catch (\Exception $e) {
            Log::error('Failed to update vendor', ['error' => $e->getMessage()]);
            return back()->with('status', ['success' => 0, 'msg' => 'Failed to update vendor.']);
        }
    }

    /**
     * Remove the specified vendor.
     */
    public function destroy(Request $request, $id)
    {
        if (!auth()->user()->can('dropship.manage_vendors')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $vendor = WpVendor::where('business_id', $business_id)->findOrFail($id);
            
            if (!$vendor->isEditable()) {
                return response()->json(['success' => false, 'msg' => 'WooCommerce vendors cannot be deleted.']);
            }

            // Remove product mappings
            DB::table('products_wp_vendors_table_pivot')->where('wp_vendor_id', $id)->delete();

            $vendor->delete();

            return response()->json(['success' => true, 'msg' => 'Vendor deleted successfully.']);

        } catch (\Exception $e) {
            Log::error('Failed to delete vendor', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Failed to delete vendor.']);
        }
    }

    /**
     * Display vendor's products.
     */
    public function products(Request $request, $id)
    {
        if (!auth()->user()->can('dropship.manage_vendors')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $vendor = WpVendor::where('business_id', $business_id)->findOrFail($id);
        
        // Handle AJAX request for DataTable
        if ($request->ajax()) {
            $query = DB::table('products_wp_vendors_table_pivot')
                ->join('products', 'products_wp_vendors_table_pivot.product_id', '=', 'products.id')
                ->leftJoin('variations', function ($join) {
                    $join->on('products.id', '=', 'variations.product_id')
                         ->whereNull('variations.deleted_at');
                })
                ->where('products_wp_vendors_table_pivot.wp_vendor_id', $id)
                ->select([
                    'products.id',
                    'products.name',
                    'products.sku',
                    DB::raw('MAX(variations.sell_price_inc_tax) as sell_price_inc_tax'),
                    'products_wp_vendors_table_pivot.vendor_cost_price',
                    'products_wp_vendors_table_pivot.vendor_markup_percentage',
                    'products_wp_vendors_table_pivot.dropship_selling_price',
                    'products_wp_vendors_table_pivot.status as pivot_status',
                    'products_wp_vendors_table_pivot.is_primary_vendor',
                ])
                ->groupBy([
                    'products.id',
                    'products.name',
                    'products.sku',
                    'products_wp_vendors_table_pivot.vendor_cost_price',
                    'products_wp_vendors_table_pivot.vendor_markup_percentage',
                    'products_wp_vendors_table_pivot.dropship_selling_price',
                    'products_wp_vendors_table_pivot.status',
                    'products_wp_vendors_table_pivot.is_primary_vendor',
                ]);

            return DataTables::of($query)
                ->addColumn('vendor_cost', function ($row) {
                    $cost = $row->vendor_cost_price ?? 0;
                    return '$' . number_format((float)$cost, 2);
                })
                ->addColumn('markup', function ($row) {
                    $markup = $row->vendor_markup_percentage ?? 0;
                    return number_format((float)$markup, 2) . '%';
                })
                ->addColumn('selling_price', function ($row) {
                    $price = $row->dropship_selling_price ?? $row->sell_price_inc_tax ?? 0;
                    return '$' . number_format((float)$price, 2);
                })
                ->addColumn('status', function ($row) {
                    $status = $row->pivot_status ?? 'active';
                    $colors = ['active' => 'success', 'inactive' => 'secondary', 'out_of_stock' => 'danger'];
                    return '<span class="badge bg-' . ($colors[$status] ?? 'secondary') . '">' . ucfirst(str_replace('_', ' ', $status)) . '</span>';
                })
                ->addColumn('is_primary', function ($row) {
                    $isPrimary = $row->is_primary_vendor ?? false;
                    return $isPrimary ? '<i class="fas fa-star text-warning"></i>' : '-';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="tw-flex tw-gap-1">
                        <button class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-warning edit-mapping" data-product-id="' . $row->id . '"><i class="fas fa-edit"></i></button>
                        <button class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-warning remove-mapping" data-product-id="' . $row->id . '"><i class="fas fa-trash"></i></button>
                    </div>';
                })
                ->rawColumns(['status', 'is_primary', 'action'])
                ->make(true);
        }

        // Get products not already mapped to this vendor
        $mappedProductIds = DB::table('products_wp_vendors_table_pivot')
            ->where('wp_vendor_id', $id)
            ->pluck('product_id')
            ->toArray();

        $availableProducts = \App\Product::where('business_id', $business_id)
            ->whereNotIn('id', $mappedProductIds)
            ->where('is_inactive', 0)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('dropship.vendors.products', compact('vendor', 'availableProducts'));
    }

    /**
     * Add product mapping to vendor.
     */
    public function addProductMapping(Request $request, $id)
    {
        if (!auth()->user()->can('dropship.manage_vendors')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $vendor = WpVendor::where('business_id', $business_id)->findOrFail($id);

            $productId = $request->input('product_id');
            
            $exists = DB::table('products_wp_vendors_table_pivot')
                ->where('wp_vendor_id', $id)
                ->where('product_id', $productId)
                ->exists();

            if ($exists) {
                return response()->json(['success' => false, 'msg' => 'Product already mapped to this vendor.']);
            }

            DB::table('products_wp_vendors_table_pivot')->insert([
                'wp_vendor_id' => $id,
                'product_id' => $productId,
                'vendor_cost_price' => $request->input('vendor_cost_price'),
                'vendor_markup_percentage' => $request->input('vendor_markup_percentage'),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true, 'msg' => 'Product added successfully.']);

        } catch (\Exception $e) {
            Log::error('Failed to add product mapping', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Failed to add product.']);
        }
    }

    /**
     * Update product mapping.
     */
    public function updateProductMapping(Request $request, $vendorId, $productId)
    {
        if (!auth()->user()->can('dropship.manage_vendors')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::table('products_wp_vendors_table_pivot')
                ->where('wp_vendor_id', $vendorId)
                ->where('product_id', $productId)
                ->update([
                    'vendor_cost_price' => $request->input('vendor_cost_price'),
                    'vendor_markup_percentage' => $request->input('vendor_markup_percentage'),
                    'status' => $request->input('status', 'active'),
                    'updated_at' => now(),
        ]);

            return response()->json(['success' => true, 'msg' => 'Product mapping updated.']);

        } catch (\Exception $e) {
            Log::error('Failed to update product mapping', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Failed to update product.']);
        }
    }

    /**
     * Remove product mapping from vendor.
     */
    public function removeProductMapping(Request $request, $vendorId, $productId)
    {
        if (!auth()->user()->can('dropship.manage_vendors')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::table('products_wp_vendors_table_pivot')
                ->where('wp_vendor_id', $vendorId)
                ->where('product_id', $productId)
                ->delete();

            return response()->json(['success' => true, 'msg' => 'Product removed from vendor.']);

        } catch (\Exception $e) {
            Log::error('Failed to remove product mapping', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Failed to remove product.']);
        }
    }

    /**
     * Display vendor's orders.
     */
    public function orders(Request $request, $id)
    {
        if (!auth()->user()->can('dropship.manage_vendors')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $vendor = WpVendor::where('business_id', $business_id)->findOrFail($id);
        $orders = $vendor->orders()->orderBy('created_at', 'desc')->paginate(25);

        return view('dropship.orders.index', compact('vendor', 'orders'));
    }

    /**
     * Display all vendor product requests (Admin view).
     */
    public function productRequests(Request $request)
    {
        if (!auth()->user()->can('dropship.admin.manage_vendors')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $stats = [
            'pending' => DB::table('vendor_product_requests')
                ->join('wp_vendors', 'vendor_product_requests.wp_vendor_id', '=', 'wp_vendors.id')
                ->where('wp_vendors.business_id', $business_id)
                ->whereNull('vendor_product_requests.deleted_at')
                ->where('vendor_product_requests.status', 'pending')
                ->count(),
            'approved' => DB::table('vendor_product_requests')
                ->join('wp_vendors', 'vendor_product_requests.wp_vendor_id', '=', 'wp_vendors.id')
                ->where('wp_vendors.business_id', $business_id)
                ->whereNull('vendor_product_requests.deleted_at')
                ->where('vendor_product_requests.status', 'approved')
                ->count(),
            'rejected' => DB::table('vendor_product_requests')
                ->join('wp_vendors', 'vendor_product_requests.wp_vendor_id', '=', 'wp_vendors.id')
                ->where('wp_vendors.business_id', $business_id)
                ->whereNull('vendor_product_requests.deleted_at')
                ->where('vendor_product_requests.status', 'rejected')
                ->count(),
        ];

        $vendors = WpVendor::where('business_id', $business_id)
            ->select('id', 'name', 'company_name')
            ->orderBy('name')
            ->get();

        return view('dropship.product-requests.index', compact('stats', 'vendors'));
    }

    /**
     * DataTable data for vendor product requests.
     */
    public function productRequestsData(Request $request)
    {
        if (!auth()->user()->can('dropship.admin.manage_vendors')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $query = DB::table('vendor_product_requests')
            ->join('wp_vendors', 'vendor_product_requests.wp_vendor_id', '=', 'wp_vendors.id')
            ->leftJoin('products', 'vendor_product_requests.product_id', '=', 'products.id')
            ->leftJoin('categories', 'vendor_product_requests.proposed_category_id', '=', 'categories.id')
            ->leftJoin('brands', 'vendor_product_requests.proposed_brand_id', '=', 'brands.id')
            ->leftJoin('users as reviewer', 'vendor_product_requests.reviewed_by', '=', 'reviewer.id')
            ->where('wp_vendors.business_id', $business_id)
            ->whereNull('vendor_product_requests.deleted_at')
            ->select([
                'vendor_product_requests.id',
                'vendor_product_requests.request_type',
                'vendor_product_requests.proposed_name',
                'vendor_product_requests.proposed_sku',
                'vendor_product_requests.proposed_cost_price',
                'vendor_product_requests.proposed_selling_price',
                'vendor_product_requests.status',
                'vendor_product_requests.notes',
                'vendor_product_requests.admin_notes',
                'vendor_product_requests.reviewed_at',
                'vendor_product_requests.created_at',
                'wp_vendors.id as vendor_id',
                'wp_vendors.name as vendor_name',
                'wp_vendors.company_name as vendor_company',
                'wp_vendors.email as vendor_email',
                'products.name as existing_product_name',
                'products.sku as existing_product_sku',
                'categories.name as category_name',
                'brands.name as brand_name',
                'reviewer.first_name as reviewer_first_name',
                'reviewer.last_name as reviewer_last_name',
                'reviewer.username as reviewer_username',
            ]);

        if ($request->filled('status')) {
            $query->where('vendor_product_requests.status', $request->input('status'));
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_product_requests.wp_vendor_id', $request->input('vendor_id'));
        }
        if ($request->filled('request_type')) {
            $query->where('vendor_product_requests.request_type', $request->input('request_type'));
        }

        return DataTables::of($query)
            ->addColumn('vendor_display', function ($row) {
                $label = $row->vendor_company ?: $row->vendor_name;
                return '<span class="vendor-badge">' . e($label) . '</span>';
            })
            ->addColumn('type_badge', function ($row) {
                if ($row->request_type === 'new') {
                    return '<span class="badge" style="background:#3b82f6;color:#fff;">New Product</span>';
                }
                return '<span class="badge" style="background:#6b7280;color:#fff;">Existing</span>';
            })
            ->addColumn('product_display', function ($row) {
                if ($row->request_type === 'new') {
                    return '<strong>' . e($row->proposed_name ?: 'New Product') . '</strong>';
                }
                return e($row->existing_product_name ?: 'N/A');
            })
            ->addColumn('sku_display', function ($row) {
                if ($row->request_type === 'new') {
                    return '<code>' . e($row->proposed_sku ?: 'Auto') . '</code>';
                }
                return '<code>' . e($row->existing_product_sku ?: 'N/A') . '</code>';
            })
            ->addColumn('price_display', function ($row) {
                $cost = $row->proposed_cost_price ? '$' . number_format((float) $row->proposed_cost_price, 2) : 'N/A';
                $sell = $row->proposed_selling_price ? '$' . number_format((float) $row->proposed_selling_price, 2) : 'N/A';
                return '<div><small>Cost: </small>' . $cost . '<br><small>Sell: </small>' . $sell . '</div>';
            })
            ->addColumn('status_badge', function ($row) {
                $map = [
                    'pending' => ['#f59e0b', 'Pending'],
                    'approved' => ['#10b981', 'Approved'],
                    'rejected' => ['#ef4444', 'Rejected'],
                ];
                $meta = $map[$row->status] ?? ['#6b7280', ucfirst($row->status)];
                return '<span class="badge" style="background:' . $meta[0] . ';color:#fff;">' . $meta[1] . '</span>';
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
                return '<span style="font-size:12px;">' . e($name) . ($date ? '<br><span style="color:#9ca3af;font-size:11px;">' . $date . '</span>' : '') . '</span>';
            })
            ->addColumn('date_display', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('M d, Y');
            })
            ->addColumn('action', function ($row) {
                $buttons = '<div class="tw-flex tw-gap-2 action-btns">';
                $buttons .= '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline view-request" data-id="' . $row->id . '"><i class="fas fa-eye"></i> View</button>';
                if ($row->status === 'pending') {
                    if ($row->request_type === 'new') {
                        $buttons .= '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-success create-approve-request" data-id="' . $row->id . '"><i class="fas fa-check"></i> Create & Approve</button>';
                    } else {
                        $buttons .= '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-success approve-request" data-id="' . $row->id . '"><i class="fas fa-check"></i> Approve</button>';
                    }
                    $buttons .= '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-error reject-request" data-id="' . $row->id . '"><i class="fas fa-times"></i> Reject</button>';
                }
                $buttons .= '</div>';
                return $buttons;
            })
            ->rawColumns([
                'vendor_display',
                'type_badge',
                'product_display',
                'sku_display',
                'price_display',
                'status_badge',
                'reviewed_by_display',
                'action',
            ])
            ->make(true);
    }

    /**
     * Show a single request (JSON for modal).
     */
    public function showProductRequest($id)
    {
        if (!auth()->user()->can('dropship.manage_vendors')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $requestRow = DB::table('vendor_product_requests')
            ->join('wp_vendors', 'vendor_product_requests.wp_vendor_id', '=', 'wp_vendors.id')
            ->leftJoin('products', 'vendor_product_requests.product_id', '=', 'products.id')
            ->leftJoin('categories', 'vendor_product_requests.proposed_category_id', '=', 'categories.id')
            ->leftJoin('brands', 'vendor_product_requests.proposed_brand_id', '=', 'brands.id')
            ->where('vendor_product_requests.id', $id)
            ->where('wp_vendors.business_id', $business_id)
            ->select([
                'vendor_product_requests.*',
                'wp_vendors.name as vendor_name',
                'wp_vendors.company_name as vendor_company',
                'wp_vendors.email as vendor_email',
                'products.name as existing_product_name',
                'products.sku as existing_product_sku',
                'categories.name as category_name',
                'brands.name as brand_name',
            ])
            ->first();

        if (!$requestRow) {
            return response()->json(['success' => false, 'msg' => 'Request not found.']);
        }

        $requestData = (array) $requestRow;
        $requestData['proposed_variations'] = [];
        if (!empty($requestRow->proposed_variations)) {
            $decoded = json_decode($requestRow->proposed_variations, true);
            if (is_array($decoded)) {
                $requestData['proposed_variations'] = $decoded;
            }
        }

        return response()->json([
            'success' => true,
            'request' => $requestData,
        ]);
    }

    public function approveProductRequest(Request $request, $id)
    {
        return $this->updateRequestStatus($request, $id, 'approved');
    }

    public function rejectProductRequest(Request $request, $id)
    {
        return $this->updateRequestStatus($request, $id, 'rejected');
            }

    public function createAndApproveProductRequest(Request $request, $id)
    {
        return $this->updateRequestStatus($request, $id, 'approved', true);
    }

    private function updateRequestStatus(Request $request, $id, $status, $createProduct = false)
    {
        if (!auth()->user()->can('dropship.manage_vendors')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $row = DB::table('vendor_product_requests')->where('id', $id)->first();
            if (!$row) {
                return response()->json(['success' => false, 'msg' => 'Request not found.']);
            }

            $adminNotes = $request->input('admin_notes');

            if ($status === 'approved' && $row->request_type === 'existing') {
                // Attach product to vendor inventory if needed
                $exists = DB::table('products_wp_vendors_table_pivot')
                    ->where('wp_vendor_id', $row->wp_vendor_id)
                    ->where('product_id', $row->product_id)
                ->exists();
                if (!$exists && $row->product_id) {
                    DB::table('products_wp_vendors_table_pivot')->insert([
                        'wp_vendor_id' => $row->wp_vendor_id,
                        'product_id' => $row->product_id,
                    ]);
            }
            }

            DB::table('vendor_product_requests')
                ->where('id', $id)
                ->update([
                    'status' => $status,
                    'admin_notes' => $adminNotes,
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                    'approved_at' => $status === 'approved' ? now() : null,
                    'approved_by' => $status === 'approved' ? auth()->id() : null,
                    'updated_at' => now(),
                ]);

            $response = [
                'success' => true,
                'msg' => 'Request ' . $status . ' successfully.',
            ];

            if ($createProduct && $row->request_type === 'new') {
                $response['product_name'] = $row->proposed_name ?? 'New Product';
            }

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Failed to update product request', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Failed to update request.']);
            }
    }
}

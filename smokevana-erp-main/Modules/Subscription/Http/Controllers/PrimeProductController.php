<?php

namespace Modules\Subscription\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Product;
use Modules\Subscription\Entities\SubscriptionPlan;
use Modules\Subscription\Entities\PrimeProduct;
use Yajra\DataTables\Facades\DataTables;

class PrimeProductController extends Controller
{
    /**
     * Display prime products list
     */
    public function index()
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $plans = SubscriptionPlan::where('business_id', $business_id)
            ->where('is_prime', true)
            ->active()
            ->pluck('name', 'id');

        // Get prime products with their products
        $prime_products = PrimeProduct::where('business_id', $business_id)
            ->with(['product' => function($q) {
                $q->select('id', 'name', 'sku', 'image', 'product_updated_at');
            }])
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get();

        // Get categories from products
        $categories = \App\Category::where('business_id', $business_id)
            ->pluck('name', 'id');

        // Stats
        $stats = [
            'total_prime_products' => PrimeProduct::where('business_id', $business_id)->where('is_active', 1)->count(),
            'categories_count' => PrimeProduct::where('prime_products.business_id', $business_id)
                ->join('products', function ($join) use ($business_id) {
                    $join->on('prime_products.product_id', '=', 'products.id')
                        ->where('products.business_id', $business_id);
                })
                ->distinct('products.category_id')
                ->count('products.category_id'),
            'prime_orders' => 0, // TODO: Calculate from orders
        ];

        return view('subscription::prime_products.index', compact('plans', 'prime_products', 'categories', 'stats'));
    }

    /**
     * Get prime products data for DataTables
     */
    public function getPrimeProducts(Request $request)
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $primeProducts = PrimeProduct::where('prime_products.business_id', $business_id)
            ->with(['product', 'plan'])
            ->select('prime_products.*');

        // Apply filters
        if ($request->has('plan_id') && !empty($request->plan_id)) {
            $primeProducts->where('plan_id', $request->plan_id);
        }

        if ($request->has('access_type') && !empty($request->access_type)) {
            $primeProducts->where('access_type', $request->access_type);
        }

        return DataTables::of($primeProducts)
            ->addColumn('product_name', function ($row) {
                return $row->product ? $row->product->name : 'N/A';
            })
            ->addColumn('product_sku', function ($row) {
                return $row->product ? $row->product->sku : 'N/A';
            })
            ->addColumn('plan_name', function ($row) {
                return $row->plan ? $row->plan->name : 'All Prime Plans';
            })
            ->addColumn('access_type_badge', function ($row) {
                return '<span class="badge bg-' . $row->access_type_badge . '">' . $row->access_type_label . '</span>';
            })
            ->addColumn('discount_info', function ($row) {
                return $row->additional_discount > 0 ? $row->additional_discount . '%' : '-';
            })
            ->addColumn('status_badge', function ($row) {
                $status = $row->is_active ? 'Active' : 'Inactive';
                $badge = $row->is_active ? 'success' : 'secondary';
                return '<span class="badge bg-' . $badge . '">' . $status . '</span>';
            })
            ->addColumn('action', function ($row) {
                $actions = '<div class="btn-group">';
                
                if (auth()->user()->can('subscription.create')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-primary edit-prime-product" data-id="' . $row->id . '"><i class="fas fa-edit"></i></button>';
                    $actions .= '<button type="button" class="btn btn-sm btn-danger delete-prime-product" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['access_type_badge', 'status_badge', 'action'])
            ->make(true);
    }

    /**
     * Get product for AJAX search
     */
    public function searchProducts(Request $request)
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $term = $request->term;

        $products = Product::where('business_id', $business_id)
            ->where('is_inactive', 0)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%')
                    ->orWhere('sku', 'like', '%' . $term . '%');
            })
            ->select('id', 'name', 'sku')
            ->limit(20)
            ->get();

        $results = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'text' => $product->name . ' (' . $product->sku . ')'
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Store a new prime product
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('subscription.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'access_type' => 'required|in:exclusive,early_access,discounted',
        ]);

        $business_id = request()->session()->get('user.business_id');

        // Check if product is already prime
        $existing = PrimeProduct::where('business_id', $business_id)
            ->where('product_id', $request->product_id)
            ->where('plan_id', $request->plan_id ?: null)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'This product is already added as a Prime product.'
            ], 400);
        }

        try {
            $primeProduct = PrimeProduct::create([
                'business_id' => $business_id,
                'product_id' => $request->product_id,
                'plan_id' => $request->plan_id ?: null,
                'access_type' => $request->access_type,
                'early_access_days' => $request->early_access_days ?? 0,
                'additional_discount' => $request->additional_discount ?? 0,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->is_active ?? true,
                'valid_from' => $request->valid_from,
                'valid_until' => $request->valid_until,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Prime product added successfully.',
                'data' => $primeProduct
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add prime product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get prime product details for editing
     */
    public function edit($id)
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $primeProduct = PrimeProduct::where('business_id', $business_id)
            ->with(['product', 'plan'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $primeProduct
        ]);
    }

    /**
     * Update prime product
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('subscription.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $primeProduct = PrimeProduct::where('business_id', $business_id)
            ->findOrFail($id);

        try {
            $primeProduct->update([
                'plan_id' => $request->plan_id ?: null,
                'access_type' => $request->access_type,
                'early_access_days' => $request->early_access_days ?? 0,
                'additional_discount' => $request->additional_discount ?? 0,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->is_active ?? true,
                'valid_from' => $request->valid_from,
                'valid_until' => $request->valid_until,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Prime product updated successfully.',
                'data' => $primeProduct
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update prime product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete prime product
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('subscription.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $primeProduct = PrimeProduct::where('business_id', $business_id)
            ->findOrFail($id);

        try {
            $primeProduct->delete();

            return response()->json([
                'success' => true,
                'message' => 'Prime product removed successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove prime product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk add products as prime
     */
    public function bulkAdd(Request $request)
    {
        if (!auth()->user()->can('subscription.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'access_type' => 'required|in:exclusive,early_access,discounted',
        ]);

        $business_id = request()->session()->get('user.business_id');
        $added = 0;
        $skipped = 0;

        try {
            DB::beginTransaction();

            foreach ($request->product_ids as $product_id) {
                // Check if already exists
                $existing = PrimeProduct::where('business_id', $business_id)
                    ->where('product_id', $product_id)
                    ->where('plan_id', $request->plan_id ?: null)
                    ->first();

                if ($existing) {
                    $skipped++;
                    continue;
                }

                PrimeProduct::create([
                    'business_id' => $business_id,
                    'product_id' => $product_id,
                    'plan_id' => $request->plan_id ?: null,
                    'access_type' => $request->access_type,
                    'additional_discount' => $request->additional_discount ?? 0,
                    'is_active' => true,
                ]);

                $added++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$added} products added as Prime. {$skipped} skipped (already Prime)."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add prime products: ' . $e->getMessage()
            ], 500);
        }
    }
}

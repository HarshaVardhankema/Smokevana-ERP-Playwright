<?php

namespace Modules\Subscription\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\CustomerGroup;
use App\SellingPriceGroup;
use Modules\Subscription\Entities\SubscriptionPlan;
use Yajra\DataTables\Facades\DataTables;

class SubscriptionPlanController extends Controller
{
    /**
     * Display plans list
     */
    public function index()
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        return view('subscription::plans.index');
    }

    /**
     * Get plans data for DataTables
     */
    public function getPlans(Request $request)
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $plans = SubscriptionPlan::where('business_id', $business_id)
            ->with(['customerGroup', 'sellingPriceGroup'])
            ->select('subscription_plans.*');

        return DataTables::of($plans)
            ->addColumn('billing_info', function ($row) {
                $type = ucfirst($row->billing_type);
                $cycle = ucfirst(str_replace('_', ' ', $row->billing_cycle));
                return $type . ' / ' . $cycle;
            })
            ->addColumn('price_formatted', function ($row) {
                return $row->formatted_price;
            })
            ->addColumn('customer_group_name', function ($row) {
                return $row->customerGroup ? $row->customerGroup->name : '-';
            })
            ->addColumn('subscribers_count', function ($row) {
                $max = $row->max_subscribers ? ' / ' . $row->max_subscribers : '';
                return $row->current_subscribers . $max;
            })
            ->addColumn('status_badge', function ($row) {
                $status = $row->is_active ? 'Active' : 'Inactive';
                $badge = $row->is_active ? 'success' : 'secondary';
                $html = '<span class="badge bg-' . $badge . '">' . $status . '</span>';
                if ($row->is_prime) {
                    $html .= ' <span class="badge bg-warning">Prime</span>';
                }
                if ($row->is_featured) {
                    $html .= ' <span class="badge bg-info">Featured</span>';
                }
                return $html;
            })
            ->addColumn('action', function ($row) {
                $actions = '<div class="btn-group">';
                $actions .= '<a href="' . route('subscription.plans.show', $row->id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                
                if (auth()->user()->can('subscription.create')) {
                    $actions .= '<a href="' . route('subscription.plans.edit', $row->id) . '" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                }
                
                if (auth()->user()->can('subscription.delete') && $row->current_subscribers == 0) {
                    $actions .= '<button type="button" class="btn btn-sm btn-danger delete-plan" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    /**
     * Show create plan form
     */
    public function create()
    {
        if (!auth()->user()->can('subscription.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $customer_groups = CustomerGroup::forDropdown($business_id, true);
        $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
            ->where('is_active', 1)
            ->pluck('name', 'id')
            ->prepend(__('lang_v1.none'), '');
        $billing_cycles = SubscriptionPlan::BILLING_CYCLES;

        return view('subscription::plans.create', compact(
            'customer_groups',
            'selling_price_groups',
            'billing_cycles'
        ));
    }

    /**
     * Store a new plan
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('subscription.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_type' => 'required|in:recurring,one_time,date_based',
            'billing_cycle' => 'required|in:monthly,quarterly,semi_annual,annual,lifetime,custom',
        ]);

        $business_id = request()->session()->get('user.business_id');

        try {
            DB::beginTransaction();

            $plan = SubscriptionPlan::create([
                'business_id' => $business_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . uniqid(),
                'description' => $request->description,
                'price' => $request->price,
                'setup_fee' => $request->setup_fee ?? 0,
                'currency' => $request->currency ?? 'USD',
                'billing_type' => $request->billing_type,
                'billing_cycle' => $request->billing_cycle,
                'billing_interval_days' => $request->billing_interval_days,
                'has_trial' => $request->has_trial ?? false,
                'trial_days' => $request->trial_days ?? 0,
                'customer_group_id' => $request->customer_group_id ?: null,
                'selling_price_group_id' => $request->selling_price_group_id ?: null,
                'features' => $request->features ? array_filter($request->features) : null,
                'benefits' => [
                    'support' => array_values($request->input('benefits.support', []) ?: []),
                    'product_access' => array_values($request->input('benefits.product_access', []) ?: []),
                    'delivery' => array_values($request->input('benefits.delivery', []) ?: []),
                    'volume_guarantee' => array_values($request->input('benefits.volume_guarantee', []) ?: []),
                ],
                'is_prime' => $request->is_prime ?? false,
                'discount_percentage' => $request->discount_percentage ?? 0,
                'reward_points_multiplier' => $request->reward_points_multiplier ?? 1,
                'fast_delivery_enabled' => $request->fast_delivery_enabled ?? false,
                'prime_products_access' => $request->prime_products_access ?? false,
                'bnpl_enabled' => $request->bnpl_enabled ?? false,
                'bnpl_limit' => $request->bnpl_limit ?? 0,
                'bnpl_days' => $request->bnpl_days ?? 30,
                'max_subscribers' => $request->max_subscribers ?: null,
                'is_active' => $request->is_active ?? true,
                'is_featured' => $request->is_featured ?? false,
                'is_public' => $request->is_public ?? true,
                'sort_order' => $request->sort_order ?? 0,
                'badge_text' => $request->badge_text,
                'badge_color' => $request->badge_color,
                'created_by' => auth()->id(),
            ]);

            // Refresh plan to get saved values
            $plan->refresh();

            // Update selling price group prices if discount percentage is set
            if ($plan->discount_percentage > 0 && $plan->selling_price_group_id) {
                $plan->updateSellingPriceGroupPrices();
            }

            DB::commit();

            return redirect()->route('subscription.plans.index')
                ->with('success', 'Subscription plan created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Plan creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create plan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show plan details
     */
    public function show($id)
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $plan = SubscriptionPlan::where('business_id', $business_id)
            ->with(['customerGroup', 'sellingPriceGroup', 'activeSubscriptions.contact'])
            ->findOrFail($id);

        // Get subscription statistics for this plan
        $stats = [
            'total_subscribers' => $plan->subscriptions()->count(),
            'active_subscribers' => $plan->activeSubscriptions()->count(),
            'monthly_revenue' => $plan->subscriptions()
                ->join('subscription_transactions', 'customer_subscriptions.id', '=', 'subscription_transactions.subscription_id')
                ->where('subscription_transactions.type', 'payment')
                ->where('subscription_transactions.status', 'completed')
                ->whereMonth('subscription_transactions.created_at', now()->month)
                ->sum('subscription_transactions.amount'),
        ];

        return view('subscription::plans.show', compact('plan', 'stats'));
    }

    /**
     * Show edit plan form
     */
    public function edit($id)
    {
        if (!auth()->user()->can('subscription.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $plan = SubscriptionPlan::where('business_id', $business_id)
            ->findOrFail($id);

        $customer_groups = CustomerGroup::forDropdown($business_id, true);
        $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
            ->where('is_active', 1)
            ->pluck('name', 'id')
            ->prepend(__('lang_v1.none'), '');
        $billing_cycles = SubscriptionPlan::BILLING_CYCLES;

        return view('subscription::plans.edit', compact(
            'plan',
            'customer_groups',
            'selling_price_groups',
            'billing_cycles'
        ));
    }

    /**
     * Update plan
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('subscription.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_type' => 'required|in:recurring,one_time,date_based',
            'billing_cycle' => 'required|in:monthly,quarterly,semi_annual,annual,lifetime,custom',
        ]);

        $business_id = request()->session()->get('user.business_id');

        $plan = SubscriptionPlan::where('business_id', $business_id)
            ->findOrFail($id);

        try {
            DB::beginTransaction();

            // Store old values to check if we need to update group prices
            $oldDiscountPercentage = $plan->discount_percentage;
            $oldSellingPriceGroupId = $plan->selling_price_group_id;

            $plan->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'setup_fee' => $request->setup_fee ?? 0,
                'currency' => $request->currency ?? 'USD',
                'billing_type' => $request->billing_type,
                'billing_cycle' => $request->billing_cycle,
                'billing_interval_days' => $request->billing_interval_days,
                'has_trial' => $request->has_trial ?? false,
                'trial_days' => $request->trial_days ?? 0,
                'customer_group_id' => $request->customer_group_id ?: null,
                'selling_price_group_id' => $request->selling_price_group_id ?: null,
                'features' => $request->features ? array_filter($request->features) : null,
                'benefits' => array_merge(is_array($plan->benefits) ? $plan->benefits : [], [
                    'support' => array_values($request->input('benefits.support', []) ?: []),
                    'product_access' => array_values($request->input('benefits.product_access', []) ?: []),
                    'delivery' => array_values($request->input('benefits.delivery', []) ?: []),
                    'volume_guarantee' => array_values($request->input('benefits.volume_guarantee', []) ?: []),
                ]),
                'is_prime' => $request->is_prime ?? false,
                'discount_percentage' => $request->discount_percentage ?? 0,
                'reward_points_multiplier' => $request->reward_points_multiplier ?? 1,
                'fast_delivery_enabled' => $request->fast_delivery_enabled ?? false,
                'prime_products_access' => $request->prime_products_access ?? false,
                'bnpl_enabled' => $request->bnpl_enabled ?? false,
                'bnpl_limit' => $request->bnpl_limit ?? 0,
                'bnpl_days' => $request->bnpl_days ?? 30,
                'max_subscribers' => $request->max_subscribers ?: null,
                'is_active' => $request->is_active ?? true,
                'is_featured' => $request->is_featured ?? false,
                'is_public' => $request->is_public ?? true,
                'sort_order' => $request->sort_order ?? 0,
                'badge_text' => $request->badge_text,
                'badge_color' => $request->badge_color,
            ]);

            // Refresh plan to get updated values
            $plan->refresh();

            // Update selling price group prices if discount percentage or selling price group changed
            if (($oldDiscountPercentage != $plan->discount_percentage || 
                 $oldSellingPriceGroupId != $plan->selling_price_group_id) && 
                $plan->discount_percentage > 0 && 
                $plan->selling_price_group_id) {
                $plan->updateSellingPriceGroupPrices();
            }

            DB::commit();

            return redirect()->route('subscription.plans.index')
                ->with('success', 'Subscription plan updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update plan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete plan
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('subscription.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $plan = SubscriptionPlan::where('business_id', $business_id)
            ->findOrFail($id);

        // Check if plan has any subscriptions
        if ($plan->current_subscribers > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete plan with active subscriptions.'
            ], 400);
        }

        try {
            $plan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Plan deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete plan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle plan status
     */
    public function toggleStatus($id)
    {
        if (!auth()->user()->can('subscription.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $plan = SubscriptionPlan::where('business_id', $business_id)
            ->findOrFail($id);

        $plan->is_active = !$plan->is_active;
        $plan->save();

        return response()->json([
            'success' => true,
            'is_active' => $plan->is_active,
            'message' => 'Plan status updated successfully.'
        ]);
    }
}

<?php

namespace Modules\Subscription\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Contact;
use App\CustomerGroup;
use App\BusinessLocation;
use Modules\Subscription\Entities\SubscriptionPlan;
use Modules\Subscription\Entities\CustomerSubscription;
use Modules\Subscription\Entities\SubscriptionInvoice;
use Modules\Subscription\Entities\SubscriptionTransaction;
use Modules\Subscription\Entities\SubscriptionLog;
use Yajra\DataTables\Facades\DataTables;

class SubscriptionController extends Controller
{
    /**
     * Display subscription dashboard
     */
    public function index()
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Dashboard statistics
        $stats = $this->getDashboardStats($business_id);
        
        // Recent subscriptions
        $recent_subscriptions = CustomerSubscription::where('business_id', $business_id)
            ->with(['contact', 'plan'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Expiring soon
        $expiring_soon = CustomerSubscription::where('business_id', $business_id)
            ->expiringSoon(7)
            ->with(['contact', 'plan'])
            ->limit(5)
            ->get();

        // Plans for dropdown
        $plans = SubscriptionPlan::forDropdown($business_id, false);

        return view('subscription::subscriptions.index', compact(
            'stats',
            'recent_subscriptions',
            'expiring_soon',
            'plans'
        ));
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats($business_id)
    {
        $stats = [];

        // Total active subscriptions
        $stats['active_subscriptions'] = CustomerSubscription::where('business_id', $business_id)
            ->active()
            ->count();

        // Total revenue this month
        $stats['monthly_revenue'] = SubscriptionTransaction::where('business_id', $business_id)
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // New subscriptions this month
        $stats['new_subscriptions'] = CustomerSubscription::where('business_id', $business_id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Churn rate (cancelled this month / active at start of month)
        $cancelledThisMonth = CustomerSubscription::where('business_id', $business_id)
            ->where('status', 'cancelled')
            ->whereMonth('cancelled_at', now()->month)
            ->count();
        
        $activeStartOfMonth = CustomerSubscription::where('business_id', $business_id)
            ->where('created_at', '<', now()->startOfMonth())
            ->whereIn('status', ['active', 'trial', 'cancelled'])
            ->count();

        $stats['churn_rate'] = $activeStartOfMonth > 0 
            ? round(($cancelledThisMonth / $activeStartOfMonth) * 100, 2) 
            : 0;

        // Pending renewals
        $stats['pending_renewals'] = CustomerSubscription::where('business_id', $business_id)
            ->dueForRenewal()
            ->count();

        // Trial subscriptions
        $stats['trial_subscriptions'] = CustomerSubscription::where('business_id', $business_id)
            ->where('status', 'trial')
            ->count();

        // Past due subscriptions
        $stats['past_due'] = CustomerSubscription::where('business_id', $business_id)
            ->where('status', 'past_due')
            ->count();

        // Total plans
        $stats['total_plans'] = SubscriptionPlan::where('business_id', $business_id)
            ->active()
            ->count();

        return $stats;
    }

    /**
     * Get subscriptions data for DataTables
     */
    public function getSubscriptions(Request $request)
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $subscriptions = CustomerSubscription::where('customer_subscriptions.business_id', $business_id)
            ->with(['contact', 'plan', 'location'])
            ->select('customer_subscriptions.*');

        // Apply filters
        if ($request->has('status') && !empty($request->status)) {
            $subscriptions->where('status', $request->status);
        }

        if ($request->has('plan_id') && !empty($request->plan_id)) {
            $subscriptions->where('plan_id', $request->plan_id);
        }

        if ($request->has('date_range') && !empty($request->date_range)) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) == 2) {
                $subscriptions->whereBetween('created_at', [$dates[0], $dates[1] . ' 23:59:59']);
            }
        }

        return DataTables::of($subscriptions)
            ->addColumn('customer_name', function ($row) {
                return $row->contact ? $row->contact->name : 'N/A';
            })
            ->addColumn('plan_name', function ($row) {
                return $row->plan ? $row->plan->name : 'N/A';
            })
            ->addColumn('status_badge', function ($row) {
                return '<span class="badge bg-' . $row->status_badge . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('expires_at_formatted', function ($row) {
                return $row->expires_at ? $row->expires_at->format('M d, Y') : 'N/A';
            })
            ->addColumn('days_remaining', function ($row) {
                $days = $row->days_remaining;
                if ($days === null) return 'Lifetime';
                if ($days <= 0) return '<span class="text-danger">Expired</span>';
                if ($days <= 7) return '<span class="text-warning">' . $days . ' days</span>';
                return $days . ' days';
            })
            ->addColumn('action', function ($row) {
                $actions = '<div class="btn-group">';
                $actions .= '<a href="' . route('subscription.subscriptions.show', $row->id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                
                if (auth()->user()->can('subscription.update')) {
                    $actions .= '<a href="' . route('subscription.subscriptions.edit', $row->id) . '" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status_badge', 'days_remaining', 'action'])
            ->make(true);
    }

    /**
     * Show create subscription form
     */
    public function create()
    {
        if (!auth()->user()->can('subscription.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $plans = SubscriptionPlan::where('business_id', $business_id)
            ->active()
            ->get();
        
        $customers = Contact::where('business_id', $business_id)
            ->whereIn('type', ['customer', 'both'])
            ->active()
            ->select('id', 'name', 'supplier_business_name', 'contact_id')
            ->get();

        $locations = BusinessLocation::where('business_id', $business_id)
            ->where('is_active', 1)
            ->pluck('name', 'id');

        $payment_methods = [
            'manual' => __('subscription::lang.manual_payment'),
            'cash' => __('subscription::lang.cash'),
            'card' => __('subscription::lang.credit_debit_card'),
            'bank_transfer' => __('subscription::lang.bank_transfer'),
            'paypal' => __('subscription::lang.paypal'),
        ];

        return view('subscription::subscriptions.create', compact('plans', 'customers', 'locations', 'payment_methods'));
    }

    /**
     * Store a new subscription
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('subscription.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'plan_id' => 'required|exists:subscription_plans,id',
            'start_trial' => 'nullable|boolean',
            'auto_renew' => 'nullable|boolean',
        ]);

        $business_id = request()->session()->get('user.business_id');

        try {
            DB::beginTransaction();

            $plan = SubscriptionPlan::findOrFail($request->plan_id);
            
            // Check if customer already has an active subscription for this plan
            $existingSubscription = CustomerSubscription::where('business_id', $business_id)
                ->where('contact_id', $request->contact_id)
                ->where('plan_id', $request->plan_id)
                ->active()
                ->first();

            if ($existingSubscription) {
                return redirect()->back()
                    ->with('error', 'Customer already has an active subscription for this plan.');
            }

            // Create subscription
            $subscription = CustomerSubscription::create([
                'business_id' => $business_id,
                'contact_id' => $request->contact_id,
                'plan_id' => $request->plan_id,
                'location_id' => $request->location_id,
                'status' => 'pending',
                'auto_renew' => $request->auto_renew ?? true,
                'source' => 'erp_manual',
                'created_by' => auth()->id(),
            ]);

            // Start trial if requested and plan has trial
            if ($request->start_trial && $plan->has_trial && $plan->trial_days > 0) {
                $subscription->startTrial($plan->trial_days);
            } else {
                $subscription->activate();
            }

            // Update customer group if plan has one assigned
            if ($plan->customer_group_id) {
                Contact::where('id', $request->contact_id)
                    ->update(['customer_group_id' => $plan->customer_group_id]);
            }

            // Update plan subscriber count
            $plan->increment('current_subscribers');

            // Log event
            $subscription->logEvent('subscription_created', 'Subscription created manually via ERP');

            DB::commit();

            return redirect()->route('subscription.subscriptions.show', $subscription->id)
                ->with('success', 'Subscription created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Subscription creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create subscription: ' . $e->getMessage());
        }
    }

    /**
     * Show subscription details
     */
    public function show($id)
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $subscription = CustomerSubscription::where('business_id', $business_id)
            ->with(['contact', 'plan', 'location', 'invoices', 'transactions', 'logs' => function ($q) {
                $q->orderBy('created_at', 'desc')->limit(20);
            }])
            ->findOrFail($id);

        return view('subscription::subscriptions.show', compact('subscription'));
    }

    /**
     * Show edit subscription form
     */
    public function edit($id)
    {
        if (!auth()->user()->can('subscription.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $subscription = CustomerSubscription::where('business_id', $business_id)
            ->with(['contact', 'plan'])
            ->findOrFail($id);

        $plans = SubscriptionPlan::where('business_id', $business_id)
            ->active()
            ->get();

        return view('subscription::subscriptions.edit', compact('subscription', 'plans'));
    }

    /**
     * Update subscription
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('subscription.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $subscription = CustomerSubscription::where('business_id', $business_id)
            ->with(['plan', 'contact'])
            ->findOrFail($id);

        try {
            DB::beginTransaction();

            $oldValues = $subscription->toArray();

            // Handle plan change first (if requested)
            if ($request->filled('plan_id') && $request->plan_id != $subscription->plan_id) {
                $newPlan = SubscriptionPlan::where('business_id', $business_id)
                    ->findOrFail($request->plan_id);
                
                $subscription->changePlan($newPlan->id, true);
                
                // Refresh subscription after plan change
                $subscription->refresh();
            }

            $subscription->update([
                'auto_renew' => $request->auto_renew ?? false,
                'notes' => $request->notes,
                'updated_by' => auth()->id(),
            ]);

            // Handle status changes
            if ($request->has('status') && $request->status !== $subscription->status) {
                switch ($request->status) {
                    case 'active':
                        $subscription->activate();
                        break;
                    case 'paused':
                        $subscription->pause();
                        break;
                    case 'cancelled':
                        $subscription->cancel($request->cancellation_reason, 'immediate');
                        break;
                }
            }

            // Log changes
            $subscription->logEvent('settings_updated', 'Subscription settings updated', $oldValues, $subscription->fresh()->toArray());

            DB::commit();

            return redirect()->route('subscription.subscriptions.show', $subscription->id)
                ->with('success', 'Subscription updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Subscription update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update subscription: ' . $e->getMessage());
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request, $id)
    {
        if (!auth()->user()->can('subscription.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $subscription = CustomerSubscription::where('business_id', $business_id)
            ->findOrFail($id);

        try {
            $subscription->cancel(
                $request->cancellation_reason,
                $request->cancellation_type ?? 'end_of_period'
            );

            // Decrement plan subscriber count
            if ($subscription->plan) {
                $subscription->plan->decrement('current_subscribers');
            }

            return redirect()->back()
                ->with('success', 'Subscription cancelled successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    /**
     * Renew subscription manually
     */
    public function renew($id)
    {
        if (!auth()->user()->can('subscription.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $subscription = CustomerSubscription::where('business_id', $business_id)
            ->findOrFail($id);

        try {
            $subscription->renew();

            return redirect()->back()
                ->with('success', 'Subscription renewed successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to renew subscription: ' . $e->getMessage());
        }
    }

    /**
     * Pause subscription
     */
    public function pause($id)
    {
        if (!auth()->user()->can('subscription.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $subscription = CustomerSubscription::where('business_id', $business_id)
            ->findOrFail($id);

        try {
            $subscription->pause();

            return redirect()->back()
                ->with('success', 'Subscription paused successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to pause subscription: ' . $e->getMessage());
        }
    }

    /**
     * Resume subscription
     */
    public function resume($id)
    {
        if (!auth()->user()->can('subscription.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $subscription = CustomerSubscription::where('business_id', $business_id)
            ->findOrFail($id);

        try {
            $subscription->resume();

            return redirect()->back()
                ->with('success', 'Subscription resumed successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to resume subscription: ' . $e->getMessage());
        }
    }

    /**
     * Sync customer group with plan's customer group
     */
    public function syncCustomerGroup($id)
    {
        if (!auth()->user()->can('subscription.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $subscription = CustomerSubscription::where('business_id', $business_id)
            ->with(['plan', 'contact'])
            ->findOrFail($id);

        try {
            if (!$subscription->plan || !$subscription->contact) {
                throw new \Exception('Subscription plan or customer not found.');
            }

            // Update customer group
            $subscription->updateCustomerGroup();

            return redirect()->back()
                ->with('success', 'Customer group synced successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to sync customer group: ' . $e->getMessage());
        }
    }
}

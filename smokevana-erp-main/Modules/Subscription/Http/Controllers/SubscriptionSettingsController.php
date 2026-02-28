<?php

namespace Modules\Subscription\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Business;
use App\CustomerGroup;
use App\SellingPriceGroup;

class SubscriptionSettingsController extends Controller
{
    /**
     * Display subscription settings
     */
    public function index()
    {
        if (!auth()->user()->can('subscription.settings')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);
        
        $settings = $business->subscription_settings ?? [];
        
        $customer_groups = CustomerGroup::forDropdown($business_id, true);
        $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
            ->where('is_active', 1)
            ->pluck('name', 'id')
            ->prepend(__('lang_v1.none'), '');

        return view('subscription::settings.index', compact(
            'settings',
            'customer_groups',
            'selling_price_groups'
        ));
    }

    /**
     * Update subscription settings
     */
    public function update(Request $request)
    {
        if (!auth()->user()->can('subscription.settings')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);

        $settings = [
            // Default customer group when subscription expires
            'default_customer_group_id' => $request->default_customer_group_id ?: null,
            
            // Default selling price group when subscription expires
            'default_selling_price_group_id' => $request->default_selling_price_group_id ?: null,
            
            // Auto-update customer group on activation
            'auto_update_customer_group' => $request->has('auto_update_customer_group'),
            
            // Auto-revert customer group on expiry/cancel
            'auto_revert_customer_group' => $request->has('auto_revert_customer_group'),
            
            // Send renewal reminders (days before expiry)
            'renewal_reminder_days' => $request->renewal_reminder_days ?? 7,
            
            // Grace period for past due subscriptions
            'grace_period_days' => $request->grace_period_days ?? 3,
            
            // Allow plan upgrades
            'allow_plan_upgrades' => $request->has('allow_plan_upgrades'),
            
            // Allow plan downgrades
            'allow_plan_downgrades' => $request->has('allow_plan_downgrades'),
            
            // Prorate plan changes
            'prorate_plan_changes' => $request->has('prorate_plan_changes'),
            
            // Auto-expire subscriptions
            'auto_expire_subscriptions' => $request->has('auto_expire_subscriptions'),
            
            // Payment Gateway Settings
            'payment_demo_mode' => $request->has('payment_demo_mode'),
            'auto_retry_payments' => $request->has('auto_retry_payments'),
            'payment_retry_attempts' => $request->payment_retry_attempts ?? 3,
            
            // Email notifications
            'send_activation_email' => $request->has('send_activation_email'),
            'send_renewal_email' => $request->has('send_renewal_email'),
            'send_expiry_email' => $request->has('send_expiry_email'),
            'send_cancellation_email' => $request->has('send_cancellation_email'),
        ];

        $business->subscription_settings = $settings;
        $business->save();

        return redirect()->back()
            ->with('success', 'Subscription settings updated successfully.');
    }
}

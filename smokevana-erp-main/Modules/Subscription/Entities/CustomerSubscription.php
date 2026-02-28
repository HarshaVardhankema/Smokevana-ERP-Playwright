<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CustomerSubscription extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_subscriptions';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'applied_benefits' => 'array',
        'metadata' => 'array',
        'auto_renew' => 'boolean',
        'subscribed_at' => 'datetime',
        'trial_starts_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'paused_at' => 'datetime',
        'resumed_at' => 'datetime',
        'next_billing_date' => 'datetime',
        'last_billing_attempt' => 'datetime',
        'grace_period_ends_at' => 'datetime',
        'amount_paid' => 'decimal:4',
        'total_savings' => 'decimal:4',
        'deleted_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_TRIAL = 'trial';
    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_PAST_DUE = 'past_due';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * Active statuses
     */
    const ACTIVE_STATUSES = [self::STATUS_ACTIVE, self::STATUS_TRIAL];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            if (empty($subscription->subscription_no)) {
                $subscription->subscription_no = self::generateSubscriptionNumber($subscription->business_id);
            }
        });
    }

    /**
     * Generate unique subscription number
     */
    public static function generateSubscriptionNumber($business_id)
    {
        $prefix = 'SUB';
        $date = date('Ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return $prefix . $date . $random;
    }

    /**
     * Get the business that owns the subscription.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Get the contact (customer) for this subscription.
     */
    public function contact()
    {
        return $this->belongsTo(\App\Contact::class);
    }

    /**
     * Get the subscription plan.
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Get the business location.
     */
    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }

    /**
     * Get all invoices for this subscription.
     */
    public function invoices()
    {
        return $this->hasMany(SubscriptionInvoice::class, 'subscription_id');
    }

    /**
     * Get all transactions for this subscription.
     */
    public function transactions()
    {
        return $this->hasMany(SubscriptionTransaction::class, 'subscription_id');
    }

    /**
     * Get all logs for this subscription.
     */
    public function logs()
    {
        return $this->hasMany(SubscriptionLog::class, 'subscription_id');
    }

    /**
     * Get the user who created this subscription.
     */
    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Get the user who last updated this subscription.
     */
    public function updater()
    {
        return $this->belongsTo(\App\User::class, 'updated_by');
    }

    /**
     * Scope: Active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    /**
     * Scope: By status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Expiring soon
     */
    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES)
            ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    /**
     * Scope: Due for renewal
     */
    public function scopeDueForRenewal($query)
    {
        return $query->where('auto_renew', true)
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->where('next_billing_date', '<=', now());
    }

    /**
     * Scope: In grace period
     */
    public function scopeInGracePeriod($query)
    {
        return $query->where('status', self::STATUS_PAST_DUE)
            ->where('grace_period_ends_at', '>=', now());
    }

    /**
     * Check if subscription is active
     */
    public function isActive()
    {
        return in_array($this->status, self::ACTIVE_STATUSES);
    }

    /**
     * Check if subscription is in trial
     */
    public function onTrial()
    {
        return $this->status === self::STATUS_TRIAL && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    /**
     * Check if subscription is cancelled
     */
    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired()
    {
        return $this->status === self::STATUS_EXPIRED || 
               ($this->expires_at && $this->expires_at->isPast());
    }

    /**
     * Check if subscription is in grace period
     */
    public function inGracePeriod()
    {
        return $this->status === self::STATUS_PAST_DUE && 
               $this->grace_period_ends_at && 
               $this->grace_period_ends_at->isFuture();
    }

    /**
     * Get days remaining until expiry
     */
    public function getDaysRemainingAttribute()
    {
        if (!$this->expires_at) {
            return null;
        }
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    /**
     * Get the status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'trial' => 'info',
            'active' => 'success',
            'paused' => 'secondary',
            'past_due' => 'danger',
            'cancelled' => 'dark',
            'expired' => 'danger',
            'suspended' => 'danger',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    /**
     * Activate subscription
     */
    public function activate()
    {
        $this->status = self::STATUS_ACTIVE;
        $this->subscribed_at = $this->subscribed_at ?? now();
        $this->current_period_start = now();
        $this->current_period_end = now()->addDays($this->plan->billing_cycle_days);
        $this->expires_at = $this->current_period_end;
        
        if ($this->auto_renew) {
            $this->next_billing_date = $this->current_period_end->subDays(1);
        }
        
        $this->save();
        
        // Update customer group based on plan
        $this->updateCustomerGroup();
        
        $this->logEvent('subscription_activated', 'Subscription activated');
        
        return $this;
    }

    /**
     * Update customer group based on subscription plan
     */
    public function updateCustomerGroup()
    {
        if (!$this->plan || !$this->contact) {
            return;
        }

        $contact = $this->contact;
        
        // Store the previous customer group if not already stored
        if (empty($this->previous_customer_group_id)) {
            $this->previous_customer_group_id = $contact->customer_group_id;
            $this->save();
        }

        // Update to plan's customer group if specified
        if ($this->plan->customer_group_id) {
            $oldGroupId = $contact->customer_group_id;
            $contact->customer_group_id = $this->plan->customer_group_id;
            $contact->save();

            $oldGroupName = $oldGroupId ? \App\CustomerGroup::find($oldGroupId)?->name : 'None';
            $newGroupName = \App\CustomerGroup::find($this->plan->customer_group_id)?->name ?? 'Unknown';

            $this->logEvent(
                'customer_group_updated',
                "Customer group updated from '{$oldGroupName}' to '{$newGroupName}' based on plan '{$this->plan->name}'"
            );
        }

        // Note: Selling price group is managed through the customer group relationship
        // The CustomerGroup model has selling_price_group_id, not the Contact model directly
    }

    /**
     * Revert customer group to default group from settings
     */
    public function revertCustomerGroup()
    {
        if (!$this->contact) {
            return;
        }

        $contact = $this->contact;
        $settings = $this->getSubscriptionSettings();
        
        // Priority: Use default from settings, fallback to previous group
        $revertGroupId = null;
        
        // First check if default is set in settings
        if (!empty($settings['default_customer_group_id'])) {
            $revertGroupId = $settings['default_customer_group_id'];
        }
        // Fallback to previous group if no default is set
        elseif (!empty($this->previous_customer_group_id)) {
            $revertGroupId = $this->previous_customer_group_id;
        }

        if ($revertGroupId && $contact->customer_group_id != $revertGroupId) {
            $oldGroupName = $contact->customer_group_id ? \App\CustomerGroup::find($contact->customer_group_id)?->name : 'None';
            $newGroupName = \App\CustomerGroup::find($revertGroupId)?->name ?? 'Default';

            $contact->customer_group_id = $revertGroupId;
            $contact->save();

            $this->logEvent(
                'customer_group_reverted',
                "Customer group reverted from '{$oldGroupName}' to '{$newGroupName}' (benefits suspended)"
            );
        }

        // Note: Selling price group is managed through the customer group relationship
    }

    /**
     * Get subscription settings for the business
     */
    protected function getSubscriptionSettings()
    {
        $settings = \App\Business::find($this->business_id)?->subscription_settings;
        return $settings ?? [];
    }

    /**
     * Start trial
     */
    public function startTrial($days = null)
    {
        $trialDays = $days ?? $this->plan->trial_days;
        
        $this->status = self::STATUS_TRIAL;
        $this->trial_starts_at = now();
        $this->trial_ends_at = now()->addDays($trialDays);
        $this->save();
        
        $this->logEvent('trial_started', "Trial started for {$trialDays} days");
        
        return $this;
    }

    /**
     * Cancel subscription
     */
    public function cancel($reason = null, $type = 'end_of_period')
    {
        $oldPlan = $this->plan;
        
        $this->status = self::STATUS_CANCELLED;
        $this->cancelled_at = now();
        $this->cancellation_reason = $reason;
        $this->cancellation_type = $type;
        $this->auto_renew = false;
        $this->save();
        
        // Revert customer group if immediate cancellation
        if ($type === 'immediate') {
            $this->revertCustomerGroup();
            
            // Decrement subscriber count
            if ($oldPlan) {
                $oldPlan->decrement('current_subscribers');
            }
        }
        
        $this->logEvent('subscription_cancelled', 'Subscription cancelled: ' . ($reason ?? 'No reason provided'));
        
        return $this;
    }

    /**
     * Expire subscription (called when subscription period ends)
     */
    public function expire()
    {
        $this->status = self::STATUS_EXPIRED;
        $this->save();
        
        // Revert customer group
        $this->revertCustomerGroup();
        
        // Decrement subscriber count
        if ($this->plan) {
            $this->plan->decrement('current_subscribers');
        }
        
        $this->logEvent('subscription_expired', 'Subscription expired');
        
        return $this;
    }

    /**
     * Change subscription plan (upgrade/downgrade)
     */
    public function changePlan($newPlanId, $effectiveImmediately = true)
    {
        $oldPlan = $this->plan;
        $newPlan = SubscriptionPlan::findOrFail($newPlanId);
        
        if ($oldPlan && $oldPlan->id === $newPlan->id) {
            throw new \Exception('New plan is the same as the current plan.');
        }

        // Check if new plan has available slots
        if (!$newPlan->hasAvailableSlots()) {
            throw new \Exception('New plan has reached maximum subscribers.');
        }

        // Store old values for logging
        $oldValues = [
            'plan_id' => $this->plan_id,
            'plan_name' => $oldPlan?->name,
            'customer_group_id' => $this->contact->customer_group_id ?? null,
        ];

        // Decrement old plan subscriber count
        if ($oldPlan) {
            $oldPlan->decrement('current_subscribers');
        }

        // Update subscription
        $this->plan_id = $newPlan->id;
        
        if ($effectiveImmediately) {
            $this->current_period_start = now();
            $this->current_period_end = now()->addDays($newPlan->billing_cycle_days);
            $this->expires_at = $this->current_period_end;
            
            if ($this->auto_renew) {
                $this->next_billing_date = $this->current_period_end->subDays(1);
            }
        }
        
        $this->save();
        
        // Increment new plan subscriber count
        $newPlan->increment('current_subscribers');
        
        // Update customer group based on new plan
        if ($newPlan->customer_group_id || $newPlan->selling_price_group_id) {
            $this->updateCustomerGroupForPlan($newPlan);
        }

        // Log the change
        $newValues = [
            'plan_id' => $newPlan->id,
            'plan_name' => $newPlan->name,
            'customer_group_id' => $this->contact->customer_group_id ?? null,
        ];

        $changeType = ($newPlan->price > ($oldPlan->price ?? 0)) ? 'upgraded' : 'downgraded';
        $this->logEvent(
            'plan_changed',
            "Subscription {$changeType} from '{$oldPlan?->name}' to '{$newPlan->name}'",
            $oldValues,
            $newValues
        );

        return $this;
    }

    /**
     * Update customer group for a specific plan
     */
    protected function updateCustomerGroupForPlan(SubscriptionPlan $plan)
    {
        if (!$this->contact) {
            return;
        }

        $contact = $this->contact;

        // Update customer group if specified
        if ($plan->customer_group_id) {
            $oldGroupName = $contact->customer_group_id 
                ? \App\CustomerGroup::find($contact->customer_group_id)?->name 
                : 'None';
            
            $contact->customer_group_id = $plan->customer_group_id;
            $contact->save();

            $newGroupName = \App\CustomerGroup::find($plan->customer_group_id)?->name ?? 'Unknown';
            
            $this->logEvent(
                'customer_group_updated',
                "Customer group updated from '{$oldGroupName}' to '{$newGroupName}' based on new plan"
            );
        }

        // Update selling price group via customer group (customer group has the selling price group)
        // The selling_price_group_id is managed through the customer_group, not directly on contact
    }

    /**
     * Pause subscription
     */
    public function pause()
    {
        $this->status = self::STATUS_PAUSED;
        $this->paused_at = now();
        $this->save();
        
        // Revert customer group to default when paused (benefits suspended)
        $this->revertCustomerGroup();
        
        $this->logEvent('subscription_paused', 'Subscription paused - benefits suspended');
        
        return $this;
    }

    /**
     * Resume subscription
     */
    public function resume()
    {
        $this->status = self::STATUS_ACTIVE;
        $this->resumed_at = now();
        $this->paused_at = null;
        $this->save();
        
        // Restore customer group based on plan when resumed (benefits restored)
        $this->updateCustomerGroup();
        
        $this->logEvent('subscription_resumed', 'Subscription resumed');
        
        return $this;
    }

    /**
     * Renew subscription
     */
    public function renew()
    {
        $this->current_period_start = now();
        $this->current_period_end = now()->addDays($this->plan->billing_cycle_days);
        $this->expires_at = $this->current_period_end;
        $this->next_billing_date = $this->current_period_end->subDays(1);
        $this->billing_attempts = 0;
        $this->status = self::STATUS_ACTIVE;
        $this->save();
        
        $this->logEvent('subscription_renewed', 'Subscription renewed');
        
        return $this;
    }

    /**
     * Mark as past due
     */
    public function markAsPastDue()
    {
        $this->status = self::STATUS_PAST_DUE;
        $this->grace_period_ends_at = now()->addDays($this->grace_period_days);
        $this->save();
        
        $this->logEvent('subscription_past_due', 'Subscription marked as past due');
        
        return $this;
    }

    /**
     * Log subscription event
     */
    public function logEvent($eventType, $description, $oldValues = null, $newValues = null)
    {
        // Get performed_by - only set if it's a valid user (admin), not a contact (customer)
        $performedBy = null;
        if (auth()->guard('web')->check()) {
            // Admin user from web guard
            $performedBy = auth()->guard('web')->id();
        } elseif (auth()->check() && auth()->user() instanceof \App\User) {
            // Fallback to default guard if it's a User instance
            $performedBy = auth()->id();
        }
        // Note: If authenticated via 'api' guard with Contact, performed_by stays null
        // This is intentional as contacts are stored in contact_id, not performed_by

        return SubscriptionLog::create([
            'business_id' => $this->business_id,
            'subscription_id' => $this->id,
            'contact_id' => $this->contact_id,
            'event_type' => $eventType,
            'event_description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'performed_by' => $performedBy,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Check if customer has Prime benefits
     */
    public function hasPrimeBenefits()
    {
        return $this->isActive() && $this->plan && $this->plan->is_prime;
    }

    /**
     * Get customer's Prime discount percentage
     */
    public function getPrimeDiscount()
    {
        if (!$this->hasPrimeBenefits()) {
            return 0;
        }
        return $this->plan->discount_percentage;
    }

    /**
     * Get customer's reward points multiplier
     */
    public function getRewardPointsMultiplier()
    {
        if (!$this->hasPrimeBenefits()) {
            return 1;
        }
        return $this->plan->reward_points_multiplier;
    }

    /**
     * Check if customer has fast delivery benefit
     */
    public function hasFastDelivery()
    {
        return $this->hasPrimeBenefits() && $this->plan->fast_delivery_enabled;
    }

    /**
     * Check if customer can access Prime-only products
     */
    public function canAccessPrimeProducts()
    {
        return $this->hasPrimeBenefits() && $this->plan->prime_products_access;
    }

    /**
     * Check if customer has Buy Now Pay Later benefit
     */
    public function hasBNPL()
    {
        return $this->hasPrimeBenefits() && $this->plan->bnpl_enabled;
    }

    /**
     * Get BNPL limit
     */
    public function getBNPLLimit()
    {
        if (!$this->hasBNPL()) {
            return 0;
        }
        return $this->plan->bnpl_limit;
    }

    /**
     * Check if subscription is fully paid
     */
    public function isPaid()
    {
        // If no plan, consider paid
        if (!$this->plan) {
            return true;
        }
        
        // Compare amount paid with plan price
        return $this->amount_paid >= $this->plan->price;
    }

    /**
     * Get the payment status
     */
    public function getPaymentStatusAttribute()
    {
        if (!$this->plan) {
            return 'paid';
        }
        
        if ($this->amount_paid >= $this->plan->price) {
            return 'paid';
        } elseif ($this->amount_paid > 0) {
            return 'partial';
        }
        return 'unpaid';
    }

    /**
     * Get the payment status badge color
     */
    public function getPaymentStatusBadgeAttribute()
    {
        $status = $this->payment_status;
        $badges = [
            'paid' => 'success',
            'partial' => 'warning',
            'unpaid' => 'danger',
        ];
        return $badges[$status] ?? 'secondary';
    }
}

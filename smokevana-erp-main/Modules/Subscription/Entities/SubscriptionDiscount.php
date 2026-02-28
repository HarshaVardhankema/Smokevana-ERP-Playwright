<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionDiscount extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscription_discounts';

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
        'applicable_plan_ids' => 'array',
        'is_active' => 'boolean',
        'first_subscription_only' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'value' => 'decimal:4',
        'minimum_amount' => 'decimal:4',
        'deleted_at' => 'datetime',
    ];

    /**
     * Discount type constants
     */
    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_FIXED = 'fixed';
    const TYPE_TRIAL_EXTENSION = 'trial_extension';
    const TYPE_FREE_MONTHS = 'free_months';

    /**
     * Duration constants
     */
    const DURATION_ONCE = 'once';
    const DURATION_FIRST_N_MONTHS = 'first_n_months';
    const DURATION_FOREVER = 'forever';

    /**
     * Get the business that owns the discount.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Get the user who created this discount.
     */
    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Scope: Active discounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Valid discounts (within validity period)
     */
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('valid_from')
                ->orWhere('valid_from', '<=', now());
        })->where(function ($q) {
            $q->whereNull('valid_until')
                ->orWhere('valid_until', '>=', now());
        });
    }

    /**
     * Scope: With available uses
     */
    public function scopeWithAvailableUses($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('max_uses')
                ->orWhereRaw('current_uses < max_uses');
        });
    }

    /**
     * Check if discount is valid
     */
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->valid_from && $this->valid_from->isFuture()) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        if ($this->max_uses !== null && $this->current_uses >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Check if discount can be used by customer
     */
    public function canBeUsedByCustomer($contactId)
    {
        if (!$this->isValid()) {
            return false;
        }

        // Check max uses per customer
        if ($this->max_uses_per_customer > 0) {
            $usageCount = CustomerSubscription::where('contact_id', $contactId)
                ->whereJsonContains('metadata->discount_code', $this->code)
                ->count();

            if ($usageCount >= $this->max_uses_per_customer) {
                return false;
            }
        }

        // Check first subscription only
        if ($this->first_subscription_only) {
            $hasExistingSubscription = CustomerSubscription::where('contact_id', $contactId)
                ->whereNotIn('status', ['pending', 'cancelled'])
                ->exists();

            if ($hasExistingSubscription) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if discount applies to plan
     */
    public function appliesToPlan($planId)
    {
        if (empty($this->applicable_plan_ids)) {
            return true; // Applies to all plans
        }

        return in_array($planId, $this->applicable_plan_ids);
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount($subtotal)
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->minimum_amount && $subtotal < $this->minimum_amount) {
            return 0;
        }

        switch ($this->type) {
            case self::TYPE_PERCENTAGE:
                return round($subtotal * ($this->value / 100), 2);
            case self::TYPE_FIXED:
                return min($this->value, $subtotal);
            default:
                return 0;
        }
    }

    /**
     * Increment usage count
     */
    public function incrementUsage()
    {
        $this->current_uses++;
        $this->save();
        return $this;
    }

    /**
     * Get formatted value
     */
    public function getFormattedValueAttribute()
    {
        switch ($this->type) {
            case self::TYPE_PERCENTAGE:
                return $this->value . '%';
            case self::TYPE_FIXED:
                return '$' . number_format($this->value, 2);
            case self::TYPE_TRIAL_EXTENSION:
                return $this->value . ' extra trial days';
            case self::TYPE_FREE_MONTHS:
                return $this->value . ' free months';
            default:
                return $this->value;
        }
    }

    /**
     * Find discount by code
     */
    public static function findByCode($code, $businessId)
    {
        return self::where('code', strtoupper($code))
            ->where('business_id', $businessId)
            ->active()
            ->valid()
            ->withAvailableUses()
            ->first();
    }
}

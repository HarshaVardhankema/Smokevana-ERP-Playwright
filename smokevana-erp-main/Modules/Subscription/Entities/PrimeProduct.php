<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;

class PrimeProduct extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prime_products';

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
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'additional_discount' => 'decimal:2',
    ];

    /**
     * Access type constants
     */
    const ACCESS_EXCLUSIVE = 'exclusive';
    const ACCESS_EARLY_ACCESS = 'early_access';
    const ACCESS_DISCOUNTED = 'discounted';

    /**
     * Get the business that owns this prime product.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo(\App\Product::class);
    }

    /**
     * Get the subscription plan (if plan-specific).
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Scope: Active prime products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Valid prime products (within validity period)
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
     * Scope: By access type
     */
    public function scopeByAccessType($query, $accessType)
    {
        return $query->where('access_type', $accessType);
    }

    /**
     * Scope: Exclusive products
     */
    public function scopeExclusive($query)
    {
        return $query->where('access_type', self::ACCESS_EXCLUSIVE);
    }

    /**
     * Scope: Early access products
     */
    public function scopeEarlyAccess($query)
    {
        return $query->where('access_type', self::ACCESS_EARLY_ACCESS);
    }

    /**
     * Scope: For specific plan
     */
    public function scopeForPlan($query, $planId)
    {
        return $query->where(function ($q) use ($planId) {
            $q->whereNull('plan_id')
                ->orWhere('plan_id', $planId);
        });
    }

    /**
     * Check if this prime product is valid
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

        return true;
    }

    /**
     * Check if customer can access this product
     */
    public function canBeAccessedBy($subscription)
    {
        if (!$this->isValid()) {
            return false;
        }

        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        // Check if plan matches (if plan-specific)
        if ($this->plan_id && $subscription->plan_id !== $this->plan_id) {
            return false;
        }

        // Check if subscription plan has prime products access
        if (!$subscription->canAccessPrimeProducts()) {
            return false;
        }

        return true;
    }

    /**
     * Get the effective discount for this prime product
     */
    public function getEffectiveDiscount($subscription = null)
    {
        if (!$subscription || !$subscription->isActive()) {
            return $this->additional_discount;
        }

        // Stack plan discount with additional product discount
        $planDiscount = $subscription->getPrimeDiscount();
        $productDiscount = $this->additional_discount;

        return $planDiscount + $productDiscount;
    }

    /**
     * Check if product is available for early access
     */
    public function isAvailableForEarlyAccess()
    {
        if ($this->access_type !== self::ACCESS_EARLY_ACCESS) {
            return false;
        }

        if (!$this->isValid()) {
            return false;
        }

        // Check if still in early access period
        if ($this->valid_from) {
            $publicAvailableDate = $this->valid_from->addDays($this->early_access_days);
            return now()->isBefore($publicAvailableDate);
        }

        return true;
    }

    /**
     * Get access type badge color
     */
    public function getAccessTypeBadgeAttribute()
    {
        $badges = [
            'exclusive' => 'danger',
            'early_access' => 'warning',
            'discounted' => 'success',
        ];
        return $badges[$this->access_type] ?? 'secondary';
    }

    /**
     * Get access type label
     */
    public function getAccessTypeLabelAttribute()
    {
        $labels = [
            'exclusive' => 'Prime Exclusive',
            'early_access' => 'Early Access',
            'discounted' => 'Prime Discount',
        ];
        return $labels[$this->access_type] ?? ucfirst($this->access_type);
    }

    /**
     * Get all prime product IDs for a business
     */
    public static function getPrimeProductIds($businessId, $planId = null)
    {
        $query = self::where('business_id', $businessId)
            ->active()
            ->valid();

        if ($planId) {
            $query->forPlan($planId);
        }

        return $query->pluck('product_id')->toArray();
    }

    /**
     * Get exclusive product IDs for a business
     */
    public static function getExclusiveProductIds($businessId, $planId = null)
    {
        $query = self::where('business_id', $businessId)
            ->active()
            ->valid()
            ->exclusive();

        if ($planId) {
            $query->forPlan($planId);
        }

        return $query->pluck('product_id')->toArray();
    }
}

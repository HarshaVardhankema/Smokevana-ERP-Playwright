<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscription_plans';

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
        'features' => 'array',
        'benefits' => 'array',
        'has_trial' => 'boolean',
        'is_prime' => 'boolean',
        'fast_delivery_enabled' => 'boolean',
        'prime_products_access' => 'boolean',
        'bnpl_enabled' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'price' => 'decimal:4',
        'setup_fee' => 'decimal:4',
        'discount_percentage' => 'decimal:2',
        'bnpl_limit' => 'decimal:4',
        'deleted_at' => 'datetime',
    ];

    /**
     * Billing cycle options
     */
    const BILLING_CYCLES = [
        'monthly' => ['days' => 30, 'label' => 'Monthly'],
        'quarterly' => ['days' => 90, 'label' => 'Quarterly'],
        'semi_annual' => ['days' => 180, 'label' => 'Semi-Annual'],
        'annual' => ['days' => 365, 'label' => 'Annual'],
        'lifetime' => ['days' => 0, 'label' => 'Lifetime'],
        'custom' => ['days' => null, 'label' => 'Custom'],
    ];

    /**
     * Get the business that owns the plan.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Get the customer group associated with this plan.
     */
    public function customerGroup()
    {
        return $this->belongsTo(\App\CustomerGroup::class);
    }

    /**
     * Get the selling price group associated with this plan.
     */
    public function sellingPriceGroup()
    {
        return $this->belongsTo(\App\SellingPriceGroup::class);
    }

    /**
     * Get all subscriptions for this plan.
     */
    public function subscriptions()
    {
        return $this->hasMany(CustomerSubscription::class, 'plan_id');
    }

    /**
     * Get active subscriptions for this plan.
     */
    public function activeSubscriptions()
    {
        return $this->hasMany(CustomerSubscription::class, 'plan_id')
            ->whereIn('status', ['active', 'trial']);
    }

    /**
     * Get prime-only products for this plan.
     */
    public function primeProducts()
    {
        return $this->hasMany(PrimeProduct::class, 'plan_id');
    }

    /**
     * Get the user who created this plan.
     */
    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Scope: Active plans only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Public plans only
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope: Prime plans only
     */
    public function scopePrime($query)
    {
        return $query->where('is_prime', true);
    }

    /**
     * Scope: Featured plans
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get billing cycle in days
     */
    public function getBillingCycleDaysAttribute()
    {
        if ($this->billing_cycle === 'custom') {
            return $this->billing_interval_days;
        }
        return self::BILLING_CYCLES[$this->billing_cycle]['days'] ?? 30;
    }

    /**
     * Check if plan has available slots
     */
    public function hasAvailableSlots()
    {
        if (is_null($this->max_subscribers)) {
            return true;
        }
        return $this->current_subscribers < $this->max_subscribers;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return $this->currency . ' ' . number_format($this->price, 2);
    }

    /**
     * Get all benefits as an array
     */
    public function getAllBenefits()
    {
        $benefits = $this->benefits ?? [];
        
        // Add computed benefits based on plan settings
        if ($this->is_prime) {
            if ($this->discount_percentage > 0) {
                $benefits['discount'] = $this->discount_percentage . '% off on all orders';
            }
            if ($this->reward_points_multiplier > 1) {
                $benefits['reward_points'] = $this->reward_points_multiplier . 'x reward points';
            }
            if ($this->fast_delivery_enabled) {
                $benefits['fast_delivery'] = 'Priority fast delivery';
            }
            if ($this->prime_products_access) {
                $benefits['exclusive_products'] = 'Access to Prime-only products';
            }
            if ($this->bnpl_enabled) {
                $benefits['bnpl'] = 'Buy Now Pay Later up to ' . $this->currency . ' ' . number_format($this->bnpl_limit, 2);
            }
        }
        
        return $benefits;
    }

    /**
     * Dropdown for plans
     */
    public static function forDropdown($business_id, $prepend_none = true, $active_only = true)
    {
        $query = self::where('business_id', $business_id);
        
        if ($active_only) {
            $query->active();
        }
        
        $plans = $query->orderBy('sort_order')->pluck('name', 'id');
        
        if ($prepend_none) {
            $plans = $plans->prepend(__('lang_v1.none'), '');
        }
        
        return $plans;
    }

    /**
     * Update selling price group prices based on discount percentage
     * This applies the discount percentage to all products for the associated selling price group
     */
    public function updateSellingPriceGroupPrices()
    {
        // Only proceed if plan has a selling price group and discount percentage
        if (!$this->selling_price_group_id || $this->discount_percentage <= 0) {
            return;
        }

        try {
            // Get all variations for this business
            $variations = \App\Variation::join('products as p', 'variations.product_id', '=', 'p.id')
                ->where('p.business_id', $this->business_id)
                ->whereIn('p.type', ['single', 'variable'])
                ->select('variations.id', 'variations.sell_price_inc_tax')
                ->get();

            foreach ($variations as $variation) {
                // Calculate the percentage to store (100 - discount = price percentage)
                // Example: 10% discount means customer pays 90% of original price
                $pricePercentage = 100 - $this->discount_percentage;
                
                // Update or create group price with percentage discount
                \App\VariationGroupPrice::updateOrCreate(
                    [
                        'variation_id' => $variation->id,
                        'price_group_id' => $this->selling_price_group_id,
                    ],
                    [
                        'price_inc_tax' => $pricePercentage, // Store as percentage (e.g., 90 for 10% discount)
                        'price_type' => 'percentage', // Set as percentage type
                    ]
                );
            }

            \Log::info("Updated selling price group prices for plan: {$this->name}", [
                'plan_id' => $this->id,
                'selling_price_group_id' => $this->selling_price_group_id,
                'discount_percentage' => $this->discount_percentage,
                'variations_updated' => $variations->count(),
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to update selling price group prices for plan: {$this->name}", [
                'plan_id' => $this->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

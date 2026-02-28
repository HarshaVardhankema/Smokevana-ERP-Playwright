<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'couponName',
        'setPriority',
        'logo',
        'couponCode',
        'applyDate',
        'endDate',
        'isDisabled',
        'isPrimary',
        'discountType',
        'filter',
        'discount',
        'discountValue',
        'minBuyQty',
        'maxBuyQty',
        'freeQty',
        'useLimit',
        'getYproductId',
        'allRuleMatch',
        'rulesOnCart',
        'rulesOnPurchaseHistory',
        'rulesOnShipping',
        'rulesOnCustomer',
        'isLifeCycleCoupon',
        'couponLife',
        'custom_meta'
    ];

    protected $casts = [
        'filter' => 'array',
        'getYproductId' => 'array',
        'rulesOnCart' => 'array',
        'rulesOnPurchaseHistory' => 'array',
        'rulesOnShipping' => 'array',
        'rulesOnCustomer' => 'array',
        'couponLife' => 'array',
        'custom_meta' => 'array',
        'isDisabled' => 'boolean',
        'isPrimary' => 'boolean',
        'allRuleMatch' => 'boolean',
        'isLifeCycleCoupon' => 'boolean',
        'applyDate' => 'date',
        'endDate' => 'date'
    ];

    public function scopeActive($query)
    {
        return $query->where('isDisabled', false)
            ->whereDate('applyDate', '<=', now())
            ->whereDate('endDate', '>=', now());
    }

    public function scopePrimary($query)
    {
        return $query->where('isPrimary', true);
    }

    public function scopeValidForCart($query, $cart)
    {
        return $query->active()->where(function($q) use ($cart) {
            $q->whereNull('rulesOnCart')
              ->orWhereJsonContains('rulesOnCart', $cart->toArray());
        });
    }

    public function scopeValidForCustomer($query, $customer)
    {
        return $query->active()->where(function($q) use ($customer) {
            $q->whereNull('rulesOnCustomer')
              ->orWhereJsonContains('rulesOnCustomer', $customer->toArray());
        });
    }
}

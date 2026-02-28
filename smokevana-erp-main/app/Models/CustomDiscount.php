<?php

namespace App\Models;

use App\Brands;
use App\BusinessLocation;
use Illuminate\Database\Eloquent\Model;

class CustomDiscount extends Model
{
    protected $table = 'custom_discounts';
    
    protected $guarded = [];

    protected $casts = [
        'isDisabled' => 'boolean',
        'isPrimary' => 'boolean',
        'isLifeCycleCoupon' => 'boolean',
        'filter' => 'array',
        'discount' => 'string',
        'custom_meta' => 'array',
        'applyDate' => 'datetime',
        'endDate' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('isDisabled', false);
    }

    public function scopePrimary($query)
    {
        return $query->where('isPrimary', true);
    }

    public function scopeValid($query)
    {
        $now = now();
        return $query->where(function($q) use ($now) {
            $q->whereNull('applyDate')
              ->orWhere('applyDate', '<=', $now);
        })->where(function($q) use ($now) {
            $q->whereNull('endDate')
              ->orWhere('endDate', '>=', $now);
        });
    }

    public function location()
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    /**
     * Get the brands associated with this discount
     * This is a more efficient way to access brand data when needed
     */
    public function brands()
    {
        $brand_ids = $this->getBrandIds();
        
        if (empty($brand_ids)) {
            return collect();
        }

        return Brands::whereIn('id', $brand_ids)->get();
    }

    /**
     * Get brand IDs as an array
     */
    public function getBrandIds()
    {
        if (empty($this->brand_id)) {
            return [];
        }

        $brand_ids = json_decode($this->brand_id, true);
        
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($brand_ids)) {
            return [];
        }

        // Check if "all" is in the array
        if (in_array('all', $brand_ids)) {
            return ['all'];
        }

        $filtered_ids = array_filter(array_map('intval', $brand_ids), function($id) {
            return $id > 0;
        });

        return  $filtered_ids;
    }
    
    /**
     * Get formatted brand names as a string
     * Uses caching for better performance
     */
    public function brand()
    {
        // Use the helper method to get brand IDs
        $brand_ids = $this->getBrandIds();
        
        if (empty($brand_ids)) {
            return "All Brands";
        }

        // Fetch all brands in a single query to avoid N+1 problem
            $brands = Brands::whereIn('id', $brand_ids)
                ->whereNotNull('name')
                ->pluck('name')
                ->toArray();

            return !empty($brands) ? implode(', ', $brands) : "All Brands";
    }

   
} 
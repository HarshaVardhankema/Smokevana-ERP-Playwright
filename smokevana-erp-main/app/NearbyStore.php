<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NearbyStore extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'store_name',
        'address',
        'latitude',
        'longitude',
        'contact_person',
        'contact_number',
        'discovered_by_sales_rep_id',
        'notes',
        'converted_to_lead_id',
        'is_converted',
        'discovered_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_converted' => 'boolean',
        'discovered_at' => 'datetime',
    ];

    /**
     * Get the business that owns the nearby store.
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    /**
     * Get the sales rep who discovered this store.
     */
    public function discoveredBySalesRep()
    {
        return $this->belongsTo(User::class, 'discovered_by_sales_rep_id');
    }

    /**
     * Get the lead this store was converted to.
     */
    public function convertedToLead()
    {
        return $this->belongsTo(Lead::class, 'converted_to_lead_id');
    }

    /**
     * Scope a query to only include stores that haven't been converted to leads.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotConverted($query)
    {
        return $query->where('is_converted', false)
            ->orWhereNull('is_converted');
    }

    /**
     * Scope a query to only include converted stores.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConverted($query)
    {
        return $query->where('is_converted', true);
    }

    /**
     * Scope a query to filter by business.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $businessId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }
}


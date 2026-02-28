<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Brands;
use App\BusinessLocation;

class PreferredBrand extends Model
{
    protected $table = 'preferred_brands';

    protected $fillable = [
        'location_id',
        'brand_id',
        'sort_order',
        'status',
        'policy_type',
        'contact_id',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Preferred brands are scoped by location (e.g. B2B location).
     * Brands in this list show first in buyers' search results (Amazon-style "Prefer brands").
     */

    public function brand()
    {
        return $this->belongsTo(Brands::class, 'brand_id');
    }

    public function location()
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }
}

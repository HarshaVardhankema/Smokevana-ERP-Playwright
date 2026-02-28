<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Category;
use App\BusinessLocation;

class PreferredCategory extends Model
{
    protected $table = 'preferred_categories';

    protected $fillable = [
        'location_id',
        'category_id',
        'sort_order',
        'status',
        'policy_type',
        'contact_id',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Preferred categories are scoped by location (e.g. B2B location).
     * Categories in this list show first in buyers' search results (Amazon-style "Prefer categories").
     */

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function location()
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }
}

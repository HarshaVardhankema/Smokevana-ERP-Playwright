<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brands extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Return list of brands for a business
     *
     * @param  int  $business_id
     * @param  bool  $show_none = false
     * @return array
     */
    public static function forDropdown($business_id, $show_none = false, $filter_use_for_repair = false ,$location_id = null)
    {
        $query = Brands::where('business_id', $business_id);

        if ($filter_use_for_repair) {
            $query->where('use_for_repair', 1);
        }

        if ($location_id) {
            $query->where(function ($q) use ($location_id) {
                $q->where('location_id', $location_id)->orWhereNull('location_id');
            });
        }

        $brands = $query->orderBy('name', 'asc')
                    ->get();

        // Deduplicate by name when location_id is null to avoid duplicates from multiple locations
        if (!$location_id) {
            $brands = $brands->unique('name');
        }

        $brands = $brands->pluck('name', 'id');

        if ($show_none) {
            $brands->prepend(__('lang_v1.none'), '');
        }

        return $brands;
    }
    // B2B relationship: One brand belongs to one category (legacy)
    public function category()
    {
        return $this->belongsTo(Category::class, 'category', 'id');
    }
    
    // B2C relationship: Many-to-many with categories
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'brand_category', 'brand_id', 'category_id');
    }

    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    /**
     * Get the brand config for the brand
     */
    public function brandConfig()
    {
        return $this->hasOne(BrandConfig::class, 'brand_id');
    }

    /**
     * Get preferred brand policies for this brand
     */
    public function preferredBrands()
    {
        return $this->hasMany(\App\Models\PreferredBrand::class, 'brand_id');
    }
    // public function children()
    // {
    //     return $this->hasMany(Brands::class, 'category', 'id');
    // }
    // public static function getBrandsHierarchy()
    // {
    //     return Brands::with('children')->whereNotNull('category')->get();
    // }
}

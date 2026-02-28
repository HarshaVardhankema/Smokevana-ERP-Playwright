<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
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

    // B2B relationship: One category has many brands (legacy)
    public function brands()
    {
        return $this->hasMany(Brands::class, 'category', 'id');
    }
    
    // B2C relationship: Many-to-many with brands
    public function brandCategories()
    {
        return $this->belongsToMany(Brands::class, 'brand_category', 'category_id', 'brand_id');
    }
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    public static function getCategoriesHierarchy()
    {
        return Category::with('children')->where('parent_id', 0);
    }

    public static function getBrandsHierarchy()
    {
        return Category::with('children')->where('parent_id', 0)->get();
    }
    /**
     * Combines Category and sub-category
     *
     * @param  int  $business_id
     * @return array
     */
    public static function catAndSubCategories($business_id)
    {
        $all_categories = Category::where('business_id', $business_id)
            ->where('category_type', 'product')
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();

        if (empty($all_categories)) {
            return [];
        }
        $categories = [];
        $sub_categories = [];

        foreach ($all_categories as $category) {
            if ($category['parent_id'] == 0) {
                $categories[] = $category;
            } else {
                $sub_categories[] = $category;
            }
        }

        $sub_cat_by_parent = [];
        if (! empty($sub_categories)) {
            foreach ($sub_categories as $sub_category) {
                if (empty($sub_cat_by_parent[$sub_category['parent_id']])) {
                    $sub_cat_by_parent[$sub_category['parent_id']] = [];
                }

                $sub_cat_by_parent[$sub_category['parent_id']][] = $sub_category;
            }
        }

        foreach ($categories as $key => $value) {
            if (! empty($sub_cat_by_parent[$value['id']])) {
                $categories[$key]['sub_categories'] = $sub_cat_by_parent[$value['id']];
            }
        }

        return $categories;
    }

    /**
     * Category Dropdown
     *
     * @param  int  $business_id
     * @param  string  $type category type
     * @return array
     */
    public static function forDropdown($business_id, $type ,$location_id = null)
    {
        $query = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->where('category_type', $type)
            ->when($location_id != null, function ($query) use ($location_id) {
                $query->where('location_id', $location_id);
            });

        $categories = $query->select('id', 'name', 'short_code')
            ->orderBy('name', 'asc')
            ->get();

        // Deduplicate by name when location_id is null to avoid duplicates from multiple locations
        if (!$location_id) {
            $categories = $categories->unique('name');
        }

        // Format the name with short_code if available
        $dropdown = $categories->mapWithKeys(function ($category) {
            $name = $category->short_code ? $category->name . '-' . $category->short_code : $category->name;
            return [$category->id => $name];
        });

        return $dropdown;
    }

    public function sub_categories()
    {
        return $this->hasMany(\App\Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'webcategories_product');
    }
    /**
     * Scope a query to only include main categories.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnlyParent($query)
    {
        return $query->where('parent_id', 0);
    }

    /**
     * Get preferred category policies for this category
     */
    public function preferredCategories()
    {
        return $this->hasMany(\App\Models\PreferredCategory::class, 'category_id');
    }
}

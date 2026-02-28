<?php

namespace App;

use App\Models\ProductGalleryImage;
use App\Models\ProductOrderLimit;
use App\Models\WpVendor;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $appends = ['image_url'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sub_unit_ids' => 'array',
        'locationTaxType' => 'array',
        'custom_sub_categories' => 'array',
    ];

    /**
     * Product source type constants
     */
    const SOURCE_TYPE_IN_HOUSE = 'in_house';
    const SOURCE_TYPE_DROPSHIPPED = 'dropshipped';

    /**
     * Check if product is dropshipped (from WooCommerce)
     *
     * @return bool
     */
    public function isDropshipped()
    {
        return $this->product_source_type === self::SOURCE_TYPE_DROPSHIPPED;
    }

    /**
     * Check if product is in-house (created in ERP)
     *
     * @return bool
     */
    public function isInHouse()
    {
        return $this->product_source_type === self::SOURCE_TYPE_IN_HOUSE;
    }

    /**
     * Scope a query to only include dropshipped products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDropshipped($query)
    {
        return $query->where('products.product_source_type', self::SOURCE_TYPE_DROPSHIPPED);
    }

    /**
     * Scope a query to only include in-house products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInHouse($query)
    {
        return $query->where('products.product_source_type', self::SOURCE_TYPE_IN_HOUSE);
    }

    /**
     * Scope for front-end/API catalog: exclude gift card products from general listings.
     * Gift cards (is_gift_card=1) are only shown when the user is viewing the dedicated gift card category.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array|null  $categorySlugs  Slugs of categories being listed (e.g. from request). If any is a gift card category slug, gift cards are included.
     * @param  array|null  $categoryIds  Optional category IDs being listed; resolved to slugs for the same check.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCatalogListing($query, $categorySlugs = null, $categoryIds = null)
    {
        $giftCardSlugs = config('constants.gift_card_category_slugs', ['gift-code', 'gift-card']);
        $slugs = is_array($categorySlugs) ? $categorySlugs : [];

        if (is_array($categoryIds) && !empty($categoryIds)) {
            $resolved = \App\Category::whereIn('id', $categoryIds)->pluck('slug')->filter()->toArray();
            $slugs = array_merge($slugs, $resolved);
        }

        $isGiftCardCategoryView = !empty(array_intersect($slugs, $giftCardSlugs));

        if (!$isGiftCardCategoryView) {
            $query->where(function ($q) {
                $q->where('products.is_gift_card', 0)->orWhereNull('products.is_gift_card');
            });
        }

        return $query;
    }

    /**
     * Get the products image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (! empty($this->image)) {
            return asset('/uploads/img/'.rawurlencode($this->image));
        }
        // Fallback to first variation's first media when product image is empty (e.g. guest catalog)
        if ($this->relationLoaded('variations')) {
            foreach ($this->variations as $variation) {
                if ($variation->relationLoaded('media') && $variation->media->isNotEmpty()) {
                    $media = $variation->media->first();
                    return $media->display_url ?? asset('/uploads/media/'.rawurlencode($media->file_name));
                }
            }
        }
        return asset('/img/default.png');
    }

    /**
     * Get the products image path.
     *
     * @return string
     */
    public function getImagePathAttribute()
    {
        if (! empty($this->image)) {
            $image_path = public_path('uploads').'/'.config('constants.product_img_path').'/'.$this->image;
        } else {
            $image_path = null;
        }

        return $image_path;
    }

    public function product_variations()
    {
        return $this->hasMany(\App\ProductVariation::class);
    }

    /**
     * Get the brand associated with the product.
     */
    public function brand()
    {
        return $this->belongsTo(\App\Brands::class);
    }

    /**
     * Get the unit associated with the product.
     */
    public function unit()
    {
        return $this->belongsTo(\App\Unit::class);
    }

    /**
     * Get the unit associated with the product.
     */
    public function second_unit()
    {
        return $this->belongsTo(\App\Unit::class, 'secondary_unit_id');
    }

    /**
     * Get category associated with the product.
     */
    public function category()
    {
        return $this->belongsTo(\App\Category::class);
    }

    /**
     * Get sub-category associated with the product.
     */
    public function sub_category()
    {
        return $this->belongsTo(\App\Category::class, 'sub_category_id', 'id');
    }

    public function webcategories()
    {
        return $this->belongsToMany(Category::class,'webcategories_product');
    }
    /**
     * Get the tax associated with the product.
     */
    public function product_tax()
    {
        return $this->belongsTo(\App\TaxRate::class, 'tax', 'id');
    }

    /**
     * Get the variations associated with the product.
     */
    public function variations()
    {
        return $this->hasMany(\App\Variation::class);
    }

   

    /**
     * If product type is modifier get products associated with it.
     */
    public function modifier_products()
    {
        return $this->belongsToMany(\App\Product::class, 'res_product_modifier_sets', 'modifier_set_id', 'product_id');
    }

    /**
     * If product type is modifier get products associated with it.
     */
    public function modifier_sets()
    {
        return $this->belongsToMany(\App\Product::class, 'res_product_modifier_sets', 'product_id', 'modifier_set_id');
    }

    /**
     * Get the purchases associated with the product.
     */
    public function purchase_lines()
    {
        return $this->hasMany(\App\PurchaseLine::class);
    }

    /**
     * Scope a query to only include active products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('products.is_inactive', 0);
    }

    /**
     * Scope a query to only include inactive products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('products.is_inactive', 1);
    }

    /**
     * Scope a query to only include products for sales.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProductForSales($query)
    {
        return $query->where('not_for_selling', 0);
    }

    /**
     * Scope a query to only include products not for sales.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProductNotForSales($query)
    {
        return $query->where('not_for_selling', 1);
    }

    public function product_locations()
    {
        return $this->belongsToMany(\App\BusinessLocation::class, 'product_locations', 'product_id', 'location_id');
    }

    /**
     * Scope a query to only include products available for a location.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForLocation($query, $location_id)
    {
        return $query->where(function ($q) use ($location_id) {
            $q->whereHas('product_locations', function ($query) use ($location_id) {
                $query->where('product_locations.location_id', $location_id);
            });
        });
    }

    /**
     * Get warranty associated with the product.
     */
    public function warranty()
    {
        return $this->belongsTo(\App\Warranty::class);
    }

    public function media()
    {
        return $this->morphMany(\App\Media::class, 'model');
    }

    public function rack_details()
    {
        return $this->hasMany(\App\ProductRack::class);
    }

    public function customer_price_recalls()
    {
        return $this->hasMany(CustomerPriceRecall::class);
    }

    public function product_gallery_images()
    {
        return $this->hasMany(ProductGalleryImage::class);
    }

    public function product_order_limits()
    {
        return $this->hasMany(ProductOrderLimit::class,'product_id');
    }
    public function product_states()
    {
        return $this->hasMany(ProductState::class,'product_id');
    }
    /**
     * Get all customer reviews for this product
     */
    public function customer_reviews()
    {
        return $this->hasMany(\App\Models\CustomerReview::class, 'product_id')->where('is_deleted', 0);
    }
    /**
     * Get the vendors associated with the product on many to many basis
     */
    public function vendors()
    {
        return $this->belongsToMany(WpVendor::class, 'products_wp_vendors_table_pivot', 'product_id', 'wp_vendor_id')
            ->withPivot([
                'vendor_cost_price',
                'vendor_markup_percentage',
                'vendor_markup_amount',
                'dropship_selling_price',
                'vendor_sku',
                'is_primary_vendor',
                'lead_time_days',
                'min_order_qty',
                'status',
                'vendor_stock_qty',
                'stock_last_updated',
                'notes'
            ])
            ->withTimestamps();
    }

    /**
     * Get the primary vendor for this product
     */
    public function primaryVendor()
    {
        return $this->vendors()->wherePivot('is_primary_vendor', true)->first();
    }

    /**
     * Get the vendor contacts associated with the product (many-to-many relationship)
     */
    public function vendorContacts()
    {
        return $this->belongsToMany(\App\Contact::class, 'vendor_product', 'product_id', 'vendor_id')
                    ->withPivot('assigned_at', 'assigned_by')
                    ->withTimestamps();
    }

    /**
     * Get all product requests for this product
     */
    public function vendorProductRequests()
    {
        return $this->hasMany(\Modules\Vendor\Entities\VendorProductRequest::class, 'product_id');
    }
}

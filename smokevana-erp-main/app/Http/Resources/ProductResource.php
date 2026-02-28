<?php

namespace App\Http\Resources;

use App\Brands;
use App\Category;
use App\ProductVariation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'enable_stock' => $this->enable_stock,
            'slug' => $this->slug,
            'image_url' => $this->image_url,
            'business_id' => $this->business_id,
            'type'=>$this->type,
            'sku' => $this->sku,
            'image' => $this->image,
            'productVisibility' => $this->product_visibility,
            'maxSaleLimit' => $this->maxSaleLimit,
            'b2c_price' => $this->ad_price??null,
            // For guest session only: pricing and ad_prices show prime (customer group) price
            'pricing' => $this->when(!Auth::guard('api')->check(), $this->prime_price ?? $this->ad_price ?? null),
            'ad_prices' => $this->when(!Auth::guard('api')->check(), $this->prime_price ?? $this->ad_price ?? null),
            'average_rating' => $this->average_rating ? round($this->average_rating, 2) : null,
            'total_reviews' => $this->total_reviews ?? 0,
            'product_variations'=>$this->product_variations,
            'variations' => $this->variations ??[],
            'brand' => $this->brand,
            'category' => $this->category,
            'webcategories' =>$this->webcategories,
            'product_states' => $this->product_states,
            'state_check'=>$this->state_check,
            'product_description' => $this->product_description,
            'product_gallery_images' => $this->product_gallery_images,
            'customer_reviews' => $this->whenLoaded('customer_reviews', function () {
                return $this->customer_reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'title' => $review->title,
                        'customer_name' => $review->contact ? ($review->contact->name ?? ($review->contact->first_name . ' ' . $review->contact->last_name) ?? $review->contact->supplier_business_name) : 'Anonymous',
                        'customer_email' => $review->contact ? ($review->contact->email ?? null) : null,
                        'description' => $review->description,
                        'rating' => $review->rating ?? null,
                        'likes' => $review->likes ?? 0,
                        'created_at' => $review->created_at ? $review->created_at->format('Y-m-d H:i:s') : null,
                    ];
                });
            }),
            'reviews_summary' => $this->whenLoaded('customer_reviews', function () {
                $reviews = $this->customer_reviews;
                $totalReviews = $reviews->count();
                $averageRating = $reviews->whereNotNull('rating')->avg('rating');
                
                return [
                    'total_reviews' => $totalReviews ?: ($this->total_reviews ?? 0),
                    'average_rating' => $averageRating ? round($averageRating, 2) : ($this->average_rating ? round($this->average_rating, 2) : null),
                    // 'rating_counts' => $ratingCounts,
                ];
            }),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Utils\ContactUtil;

class CateLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $adPrice = $this->ad_price ?? null;
        
        // Apply customer group percentage to ad_price for authenticated B2B users
        $contact = Auth::guard('api')->user();
        if ($contact && !empty($adPrice)) {
            $business_id = $contact->business_id ?? 1;
            $contactUtil = new ContactUtil();
            $cg = $contactUtil->getCustomerGroup($business_id, $contact->id);
            
            if (!empty($cg)) {
                $priceTier = $contact->price_tier;
                $priceGroupId = key($priceTier);
                
                // Apply price_percentage if customer group has selling_price_group_id
                if (!empty($cg->price_percentage) && !empty($cg->selling_price_group_id) && $cg->selling_price_group_id == $priceGroupId) {
                    $percent = (float)$cg->price_percentage; // Respect sign: negative = discount, positive = markup
                    $adPrice = $adPrice + ($percent * $adPrice / 100);
                } elseif (!empty($cg->amount) && $cg->price_calculation_type == 'percentage') {
                    // Fallback to old percentage logic
                    $percent = (float)$cg->amount; // Respect sign: negative = discount, positive = markup
                    $adPrice = $adPrice + ($percent * $adPrice / 100);
                }
            }
        }
        
        return [
            'id' => $this->id,
            'type'=>$this->type,
            'name' => $this->name,
            'slug' => $this->slug,
            'image_url' => $this->image_url,
            'business_id' => $this->business_id,
            'sku' => $this->sku,
            'image' => $this->image,
            'productVisibility' => $this->product_visibility,
            'maxSaleLimit' => $this->maxSaleLimit,
            'ad_price' => $adPrice,
            'b2c_price' => $this->b2c_price ?? null,
            // Prime price for guest/B2C: use when you want to show only prime prices (same as ad_price for guest when prime_price is loaded)
            'pricing' => $this->prime_price ?? $this->b2c_price ?? null,
            // 'product_variations'=>$this->product_variations,
            // 'variations' => $this->variations ??[],
            'brand' => $this->brand,
            'webcategories' =>$this->webcategories,
            'wishlist_id' => $this->wishlist_id,

            'average_rating' => $this->average_rating ? round((float) $this->average_rating, 2) : null,
            'total_reviews' => (int) ($this->total_reviews ?? 0),
            'customer_reviews' => $this->whenLoaded('customer_reviews', function () {
                return $this->customer_reviews->map(function ($review) {
                    return [    
                        'id' => $review->id,
                        'customer_name' => $review->contact ? ($review->contact->name ?? trim(($review->contact->first_name ?? '') . ' ' . ($review->contact->last_name ?? '')) ?: $review->contact->supplier_business_name ?? 'Anonymous') : 'Anonymous',
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
                    'total_reviews' => $totalReviews ?: (int) ($this->total_reviews ?? 0),
                    'average_rating' => $averageRating !== null ? round((float) $averageRating, 2) : ($this->average_rating ? round((float) $this->average_rating, 2) : null),
                ];
            }),
        ];
    }
}

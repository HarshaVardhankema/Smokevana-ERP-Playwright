<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class B2bProductResource extends JsonResource
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
            'slug' => $this->slug,
            'image_url' => $this->image_url,
            'business_id' => $this->business_id,
            'type'=>$this->type,
            'sku' => $this->sku,
            'image' => $this->image,
            'productVisibility' => $this->product_visibility,
            'maxSaleLimit' => $this->maxSaleLimit,
            'ad_price' => $this->ad_price??null,
            'product_variations'=>$this->product_variations,
            'variations' => $this->variations ??[],
            'brand' => $this->brand,
            'webcategories' =>$this->webcategories,
            'product_description' => $this->product_description,
            'product_gallery_images' => $this->product_gallery_images,
        ];
    }
}

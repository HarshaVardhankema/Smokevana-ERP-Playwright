<?php

namespace App\Models;

use App\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGalleryImage extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    protected $appends = ['image_url'];
    public function getImageUrlAttribute()
    {
        if (!empty($this->image_path)) {
            return url($this->image_path);
        }
        return null;
    }
}

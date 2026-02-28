<?php

namespace App\Models;

use App\Product;
use App\Variation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOrderLimit extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    // Relationships
    public function consumers()
    {
        return $this->hasMany(ProductOrderLimitConsumer::class, 'session_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variation(){
        return $this->belongsTo(Variation::class, 'variant_id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function customer(){
        return $this->belongsTo(Contact::class,'user_id','id')->select(['id','supplier_business_name','name','shipping_address','email']);
    }
    public function product(){
        return $this->belongsTo(Product::class,'product_id','id')->select(['id','name','sku','slug','type','image']);
    }
}

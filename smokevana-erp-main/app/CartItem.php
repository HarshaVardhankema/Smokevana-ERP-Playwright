<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'id');
    }
}

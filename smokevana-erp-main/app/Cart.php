<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'applied_discounts' => 'array',
        'gift_cards_to_purchase' => 'array',
        'applied_gift_cards' => 'array',
    ];
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
    
}

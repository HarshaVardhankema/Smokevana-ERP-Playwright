<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestCartItem extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    /**
     * Get the guest session that owns the cart item.
     */
    public function guestSession()
    {
        return $this->belongsTo(GuestSession::class, 'guest_session_id');
    }
    
    /**
     * Get the product that owns the cart item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    
    /**
     * Get the variation that owns the cart item.
     */
    public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }
    
    /**
     * Scope a query to only include cart items for a specific guest session.
     */
    public function scopeForGuest($query, $guestSessionId)
    {
        return $query->where('guest_session_id', $guestSessionId);
    }
    
    /**
     * Scope a query to only include cart items for a specific product.
     */
    public function scopeForProduct($query, $productId, $variationId = null)
    {
        $query = $query->where('product_id', $productId);
        
        if ($variationId) {
            $query->where('variation_id', $variationId);
        } else {
            $query->whereNull('variation_id');
        }
        
        return $query;
    }
}

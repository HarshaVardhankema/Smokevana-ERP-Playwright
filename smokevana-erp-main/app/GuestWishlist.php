<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuestWishlist extends Model
{
    protected $guarded = ['id'];

    public function guestSession()
    {
        return $this->belongsTo(GuestSession::class, 'guest_session_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}

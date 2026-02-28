<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsLetterSubscriber extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }

    /**
     * Get the brand that owns the newsletter subscriber.
     * NEWLY ADDED: This relationship enables displaying brand names in the newsletter table
     */
    public function brand()
    {
        return $this->belongsTo(\App\Brands::class, 'brand_id');
    }
}

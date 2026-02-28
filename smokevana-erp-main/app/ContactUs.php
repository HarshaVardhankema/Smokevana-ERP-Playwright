<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'meta' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contact) {
            $contact->reference_no = 'CU' . mt_rand(1000000000, 9999999999);
            
            // If location_id is 1, set brand_id to null
            if ($contact->location_id == 1) {
                $contact->brand_id = null;
            }
        });
    }

    public function metaEntries()
    {
        return $this->hasMany(ContactUsMeta::class, 'contact_id');
    }

    /**
     * Get the business location that owns the contact us.
     */
    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }

    /**
     * Get the brand that owns the contact us.
     */
    public function brand()
    {
        return $this->belongsTo(\App\Brands::class, 'brand_id');
    }
}
 
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GuestSession extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
        "applied_discounts" => "array",
    ];

    /**
     * Create a new guest session
     */
    public static function createSession($locationId, $brandId, $expiryDays = 30)
    {
        $uuid = Str::uuid()->toString();
        $expiresAt = Carbon::now()->addDays($expiryDays);

        return self::create([
            'uuid' => $uuid,
            'location_id' => $locationId,
            'brand_id' => $brandId,
            'expires_at' => $expiresAt
        ]);
    }

    /**
     * Find valid session by UUID
     */
    public static function findValidSession($uuid, $locationId, $brandId)
    {
        return self::where('uuid', $uuid)
            ->where('location_id', $locationId)
            ->where('brand_id', $brandId)
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }
        /**
     * Find valid session by UUID
     */
    // public static function findValidSession($uuid, $locationId, $brandId)
    // {
    //     $session = self::where('uuid', $uuid)
    //         ->where('location_id', $locationId)
    //         ->where('brand_id', $brandId)
    //         ->first();
    
    //     if ($session) {
    //         // \Log::info("Session Expiry: " . $session->expires_at->toDateTimeString());
    //         // \Log::info("Now: " . now()->toDateTimeString());
    //         // if diff is less than one day extend the session
    //         if ($session->expires_at->diffInDays(now()) < 1) {
    //             $session->extendSession();
    //         }
    //     }
    
    //     return $session && $session->expires_at > now() ? $session : null;
    // }

    /**
     * Check if session is valid
     */
    public function isValid()
    {
        return $this->expires_at > Carbon::now();
    }

    /**
     * Extend session expiry
     */
    public function extendSession($days = 30)
    {
        $this->update([
            'expires_at' => Carbon::now()->addDays($days)
        ]);
    }

    /**
     * Get cart items for this guest session
     */
    public function cartItems()
    {
        return $this->hasMany(GuestCartItem::class, 'guest_session_id');
    }

    /**
     * Get wishlist items for this guest session
     */
    public function wishlistItems()
    {
        return $this->hasMany(GuestWishlist::class, 'guest_session_id');
    }

    /**
     * Get the location that owns the guest session
     */
    public function location()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    /**
     * Get the brand that owns the guest session
     */
    public function brand()
    {
        return $this->belongsTo(Brands::class);
    }

    /**
     * Clean up expired sessions
     */
    public static function cleanupExpired()
    {
        return self::where('expires_at', '<', Carbon::now())->delete();
    }
}

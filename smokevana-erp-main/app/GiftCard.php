<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GiftCard extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'initial_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'purchased_at' => 'datetime',
        'redeemed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the purchaser (contact) that owns the gift card.
     */
    public function purchaser()
    {
        return $this->belongsTo(Contact::class, 'purchaser_contact_id');
    }

    /**
     * Generate a unique, human-friendly gift card code.
     */
    public static function generateUniqueCode(int $length = 16): string
    {
        do {
            $raw = strtoupper(Str::random($length));
            $code = implode('-', str_split($raw, 4));
        } while (static::where('code', $code)->exists());

        return $code;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    protected $table = 'password_reset_otps';

    protected $fillable = [
        'email',
        'contact_id',
        'otp',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Get the contact that owns the OTP.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Scope: valid (not expired). OTPs are consumed by delete(), so no used_at check needed.
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now());
    }
}

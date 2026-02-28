<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirebasePushNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'user_id',
        'platform',
        'timestamp',
        'is_active'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Get the user that owns the push notification token.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only active tokens
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get tokens by platform
     */
    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }
}

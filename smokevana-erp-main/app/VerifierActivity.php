<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerifierActivity extends Model
{
    protected $table = 'verifier_activity';
    
    protected $fillable = [
        'user_id',
        'is_active',
        'last_assigned'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_assigned' => 'datetime'
    ];

    /**
     * Get the user that owns the verifier activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get active verifiers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get inactive verifiers
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PickersActivity extends Model
{
    protected $table = 'pickers_activity';
    
    protected $fillable = [
        'user_id',
        'is_active',
        'last_assigned',
        'current_status'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_assigned' => 'datetime'
    ];

    /**
     * Get the user that owns the picker activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get active pickers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get inactive pickers
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesRepShift extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_rep_shifts';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'shift_start_time' => 'datetime',
        'shift_end_time' => 'datetime',
        'start_latitude' => 'decimal:8',
        'start_longitude' => 'decimal:8',
        'end_latitude' => 'decimal:8',
        'end_longitude' => 'decimal:8',
        'duration_minutes' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the sales rep (user) who owns the shift.
     */
    public function salesRep()
    {
        return $this->belongsTo(\App\User::class, 'sales_rep_id');
    }

    /**
     * Get the business that owns the shift.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Scope a query to only include active shifts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include ended shifts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnded($query)
    {
        return $query->where('status', 'ended');
    }

    /**
     * Calculate and update the shift duration in minutes.
     */
    public function calculateDuration()
    {
        if ($this->shift_start_time && $this->shift_end_time) {
            $this->duration_minutes = $this->shift_end_time->diffInMinutes($this->shift_start_time);
            $this->save();
        }
    }

    /**
     * Get formatted duration string (e.g., "8 hours 30 minutes").
     *
     * @return string
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->shift_start_time || !$this->shift_end_time) {
            return 'N/A';
        }

        // Calculate duration in seconds for more accuracy
        $totalSeconds = $this->shift_end_time->diffInSeconds($this->shift_start_time);
        
        // If less than a minute, show seconds
        if ($totalSeconds < 60) {
            return $totalSeconds . " second" . ($totalSeconds != 1 ? 's' : '');
        }

        $totalMinutes = $this->duration_minutes ?? floor($totalSeconds / 60);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} hour" . ($hours > 1 ? 's' : '') . " {$minutes} minute" . ($minutes > 1 ? 's' : '');
        } elseif ($hours > 0) {
            return "{$hours} hour" . ($hours > 1 ? 's' : '');
        } else {
            return "{$minutes} minute" . ($minutes > 1 ? 's' : '');
        }
    }

    /**
     * Check if shift is currently active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'active' && !$this->shift_end_time;
    }
}


<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lead_id',
        'user_id',
        'reference_no',
        'ticket_description',
        'issue_type',
        'issue_priority',
        'initial_image',
        'status',
        'closed_by',
        'closed_at'
    ];

    /**
     * Get the full URL for the initial image
     *
     * @return string|null
     */
    public function getInitialImageUrlAttribute()
    {
        if ($this->initial_image) {
            return url('uploads/tickets/' . $this->initial_image);
        }
        return null;
    }

    /**
     * Check if initial image exists
     *
     * @return bool
     */
    public function hasInitialImage()
    {
        return !empty($this->initial_image);
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the lead that owns the ticket.
     */
    public function lead()
    {
        return $this->belongsTo(\App\Lead::class, 'lead_id');
    }

    /**
     * Get the user that owns the ticket.
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    /**
     * Get the admin who closed the ticket.
     */
    public function closedBy()
    {
        return $this->belongsTo(\App\User::class, 'closed_by');
    }

    /**
     * Get the activities for the ticket.
     */
    public function activities()
    {
        return $this->hasMany(\App\TicketActivity::class, 'ticket_id');
    }

    /**
     * Scope a query to only include tickets for a specific lead.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $lead_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForLead($query, $lead_id)
    {
        return $query->where('lead_id', $lead_id);
    }

    /**
     * Scope a query to only include tickets with a specific status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}


<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;

class SubscriptionLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscription_logs';

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
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Get the business that owns the log.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Get the subscription for this log.
     */
    public function subscription()
    {
        return $this->belongsTo(CustomerSubscription::class, 'subscription_id');
    }

    /**
     * Get the contact (customer) for this log.
     */
    public function contact()
    {
        return $this->belongsTo(\App\Contact::class);
    }

    /**
     * Get the user who performed the action.
     */
    public function performer()
    {
        return $this->belongsTo(\App\User::class, 'performed_by');
    }

    /**
     * Scope: By event type
     */
    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope: Recent logs
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get formatted event type
     */
    public function getFormattedEventTypeAttribute()
    {
        $types = [
            'subscription_created' => 'Created',
            'subscription_activated' => 'Activated',
            'subscription_cancelled' => 'Cancelled',
            'subscription_paused' => 'Paused',
            'subscription_resumed' => 'Resumed',
            'subscription_renewed' => 'Renewed',
            'subscription_expired' => 'Expired',
            'subscription_past_due' => 'Past Due',
            'trial_started' => 'Trial Started',
            'trial_ended' => 'Trial Ended',
            'payment_received' => 'Payment Received',
            'payment_failed' => 'Payment Failed',
            'plan_changed' => 'Plan Changed',
            'settings_updated' => 'Settings Updated',
        ];
        
        return $types[$this->event_type] ?? ucfirst(str_replace('_', ' ', $this->event_type));
    }

    /**
     * Get event badge color
     */
    public function getEventBadgeAttribute()
    {
        $badges = [
            'subscription_created' => 'primary',
            'subscription_activated' => 'success',
            'subscription_cancelled' => 'danger',
            'subscription_paused' => 'warning',
            'subscription_resumed' => 'info',
            'subscription_renewed' => 'success',
            'subscription_expired' => 'danger',
            'subscription_past_due' => 'danger',
            'trial_started' => 'info',
            'trial_ended' => 'warning',
            'payment_received' => 'success',
            'payment_failed' => 'danger',
            'plan_changed' => 'primary',
            'settings_updated' => 'secondary',
        ];
        
        return $badges[$this->event_type] ?? 'secondary';
    }
}

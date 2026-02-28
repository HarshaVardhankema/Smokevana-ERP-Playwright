<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class SentMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'user_id',
        'order_id',
        'order_type',
        'status',
        'priority',
        'deleted'
    ];

    protected $casts = [
        'deleted' => 'boolean'
    ];

    /**
     * Get the user that owns the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    /**
     * Scope a query to only include read messages.
     */
    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    /**
     * Scope a query to only include non-deleted messages.
     */
    public function scopeNotDeleted($query)
    {
        return $query->where('deleted', false);
    }

    /**
     * Scope a query to only include deleted messages.
     */
    public function scopeDeleted($query)
    {
        return $query->where('deleted', true);
    }

    /**
     * Scope a query to only include urgent messages.
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    /**
     * Scope a query to only include non-urgent messages.
     */
    public function scopeNonUrgent($query)
    {
        return $query->where('priority', 'non_urgent');
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        $this->status = 'read';
        $this->save();
    }

    /**
     * Mark message as unread
     */
    public function markAsUnread()
    {
        $this->status = 'unread';
        $this->save();
    }

    /**
     * Soft delete the message
     */
    public function softDelete()
    {
        $this->deleted = true;
        $this->save();
    }

    /**
     * Restore the message
     */
    public function restore()
    {
        $this->deleted = false;
        $this->save();
    }

    /**
     * Get the redirect URL for the message
     * Returns "/order-success" if order_id exists, null otherwise
     */
    public function getRedirectAttribute()
    {
        return $this->order_id ? '/order-success' : null;
    }
}

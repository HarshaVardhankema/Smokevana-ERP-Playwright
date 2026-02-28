<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketActivity extends Model
{
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
    protected $appends = ['file_url'];

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
     * Get the ticket that owns the activity.
     */
    public function ticket()
    {
        return $this->belongsTo(\App\Ticket::class, 'ticket_id');
    }

    /**
     * Get the user that created the activity.
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    /**
     * Get the file URL attribute.
     *
     * @return string|null
     */
    public function getFileUrlAttribute()
    {
        if (!empty($this->attachment)) {
            return asset('uploads/tickets/' . $this->attachment);
        }
        return null;
    }

    /**
     * Check if the activity is an image.
     *
     * @return bool
     */
    public function isImage()
    {
        if (empty($this->attachment)) {
            return false;
        }
        
        $extension = strtolower(pathinfo($this->attachment, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /**
     * Get file extension.
     *
     * @return string|null
     */
    public function getFileExtension()
    {
        if (empty($this->attachment)) {
            return null;
        }
        
        return strtolower(pathinfo($this->attachment, PATHINFO_EXTENSION));
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTrackingStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'status',
        'status_date',
        'notes',
        'updated_by',
    ];

    protected $casts = [
        'status_date' => 'datetime',
    ];

    /**
     * Get the transaction that owns the tracking status
     */
    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class);
    }

    /**
     * Get the user who updated the status
     */
    public function updatedBy()
    {
        return $this->belongsTo(\App\User::class, 'updated_by');
    }
}

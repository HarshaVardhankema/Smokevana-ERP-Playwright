<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Contact;
use App\Business;

class OrderDownloadLog extends Model
{
    use HasFactory;

    protected $table = 'order_download_logs';

    protected $fillable = [
        'contact_id',
        'business_id',
        'download_type',
        'filename',
        'total_orders',
        'order_numbers',
        'order_ids',
        'filters',
        'date_range',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'order_numbers' => 'array',
        'order_ids' => 'array',
        'filters' => 'array',
        'date_range' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the contact (user) who downloaded
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    /**
     * Get the business
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
}

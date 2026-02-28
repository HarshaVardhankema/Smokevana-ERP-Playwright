<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\CustomerAddress;
use App\Contact;

class DeliveryPreference extends Model
{
    use HasFactory;

    protected $table = 'delivery_preferences';

    protected $fillable = [
        'address_id',
        'contact_id',
        'delivery_times',
        'preferred_day_1',
        'preferred_day_2',
        'make_default_delivery_option',
        'drop_off_location',
        'security_code',
        'call_box_name_or_number',
        'additional_info',
        'observed_holidays',
        'custom_holidays',
        'pallet_preference',
    ];

    protected $casts = [
        'delivery_times' => 'array',
        'observed_holidays' => 'array',
        'custom_holidays' => 'array',
        'pallet_preference' => 'array',
        'make_default_delivery_option' => 'boolean',
    ];

    /**
     * Get the address that owns the delivery preference
     */
    public function address()
    {
        return $this->belongsTo(CustomerAddress::class, 'address_id');
    }

    /**
     * Get the contact that owns the delivery preference
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
}

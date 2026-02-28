<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'multiple_address_customer';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the contact that owns the address.
     */
    public function contact()
    {
        return $this->belongsTo(\App\Contact::class, 'contact_id');
    }

    /**
     * Get formatted full address
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        $address_parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state,
            $this->zip_code,
            $this->country
        ]);

        return implode(', ', $address_parts);
    }
}


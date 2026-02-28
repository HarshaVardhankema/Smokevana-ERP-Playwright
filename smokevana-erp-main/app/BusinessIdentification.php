<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessIdentification extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'business_identifications';

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
        'business_types' => 'array',
        'state_licenses' => 'array',
        'age_gating_methods' => 'array',
        'attachments' => 'array',
        'prohibited_jurisdictions_acknowledged' => 'boolean',
    ];

    /**
     * Get the user that created the identification.
     */
    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Get the contact (customer) associated with the identification.
     */
    public function contact()
    {
        return $this->belongsTo(\App\Contact::class, 'contact_id');
    }
}


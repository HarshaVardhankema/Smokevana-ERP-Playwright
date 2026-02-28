<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'complaints';

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
        'attachments' => 'array',
        'variation_ids' => 'array',
    ];

    /**
     * Get the business that owns the complaint.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }

    /**
     * Get the user that created the complaint.
     */
    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Get the contact associated with the complaint.
     */
    public function contact()
    {
        return $this->belongsTo(\App\Contact::class, 'contact_id');
    }

    /**
     * Get the variations associated with the complaint (multiple products).
     */
    public function variations()
    {
        if (empty($this->variation_ids)) {
            return collect([]);
        }
        
        return \App\Variation::whereIn('id', $this->variation_ids)->get();
    }

    /**
     * Get the transaction associated with the complaint.
     */
    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class, 'transaction_id');
    }
}


<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class MerchantApplication extends Model
{
    protected $guarded = [];

    protected $casts = [
        'additional_owners' => 'array',
        'additional_documents' => 'array',
        'date_of_birth' => 'date',
        'has_previous_processing' => 'boolean'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
} 
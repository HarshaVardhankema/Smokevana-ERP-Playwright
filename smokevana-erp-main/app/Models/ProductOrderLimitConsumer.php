<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOrderLimitConsumer extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    // Relationships
    public function session()
    {
        return $this->belongsTo(ProductOrderLimit::class, 'session_id');
    }
}

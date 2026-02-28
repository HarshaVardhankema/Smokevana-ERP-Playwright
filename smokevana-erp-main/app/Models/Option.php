<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Option extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'key',
        'value',
        'modal_type',
        'modal_id',
        'use_for',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'modal_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the user who created the option.
     */
    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Get the user who last updated the option.
     */
    public function updater()
    {
        return $this->belongsTo(\App\User::class, 'updated_by');
    }
}

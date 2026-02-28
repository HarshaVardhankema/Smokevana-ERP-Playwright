<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeoRestrictionLog extends Model
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
    protected $casts = [
        'changes' => 'array'
    ];

    /**
     * Get the geo restriction that owns the log.
     */
    public function geoRestriction()
    {
        return $this->belongsTo(\App\GeoRestriction::class);
    }

    /**
     * Get the user that created the log.
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}


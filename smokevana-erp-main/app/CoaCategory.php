<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoaCategory extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function lists()
    {
        return $this->hasMany(CoaList::class);
    }

    public function location()
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brands::class, 'brand_id');
    }
}

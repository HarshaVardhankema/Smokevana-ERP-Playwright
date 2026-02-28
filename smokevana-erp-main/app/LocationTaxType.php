<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationTaxType extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function locationTaxCharges()
    {
        return $this->hasMany(LocationTaxCharge::class, 'location_id'); 
    }
}

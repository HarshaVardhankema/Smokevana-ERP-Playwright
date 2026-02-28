<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationTaxCharge extends Model
{
    use HasFactory;
    
    protected $guarded=[];
    public function locationTaxType()
    {
        return $this->belongsTo(LocationTaxType::class, 'location_id','id'); 
    }
}

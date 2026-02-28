<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentBuffer extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
    ];
    
    public function contact(){
        return $this->belongsTo(Contact::class,'customer_id','id')->whereIn('type',['customer','both']);
    }
}

<?php

namespace App\Models;

use App\Contact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcomReferalProgram extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function beneficiaryCustomer()
    {
        return $this->belongsTo(Contact::class, 'customer_id');
    }

    public function referredByCustomer()
    {
        return $this->belongsTo(Contact::class, 'referred_by_customer_id');
    }

    public function discount()
    {
        return $this->belongsTo(CustomDiscount::class, 'discount_id');
    }
}

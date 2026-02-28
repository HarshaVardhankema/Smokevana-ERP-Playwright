<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerPaymentMethod extends Model
{
    protected $table = 'customer_payment_methods';

    protected $fillable = [
        'user_id',
        'cardholder_name',
        'brand',
        'last4',
        'exp_month',
        'exp_year',
        'billing_zip',
        'token',
        'is_default',
    ];
}


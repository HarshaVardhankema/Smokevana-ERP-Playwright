<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPaymentGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_name',
        'business_id',
        'transaction_id',
        'payment_method_id',
        'amount',
        'group_ref_no',
        'contact_id',
    ];

    /**
     * Get the transaction related to this payment group entry
     */
    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class, 'transaction_id');
    }

    /**
     * Get the payment method (TransactionPayment) related to this group entry
     */
    public function paymentMethod()
    {
        return $this->belongsTo(\App\TransactionPayment::class, 'payment_method_id');
    }

    /**
     * Get the business
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }
}

<?php

namespace App\Models;

use App\Contact;
use App\Product;
use App\Business;
use App\Transaction;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerReview extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = [];
    
    /**
     * Get the customer (contact) that made the review.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
    
    /**
     * Get the product being reviewed.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get the transaction/order linked to this review.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    
    /**
     * Get the business associated with this review.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    
    /**
     * Get the user who created the review.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the user who last updated the review.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    /**
     * Get the user who deleted the review.
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}

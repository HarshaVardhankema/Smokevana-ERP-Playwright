<?php

namespace App\Models;

use App\Product;
use App\Contact;
use App\Variation;
use Illuminate\Database\Eloquent\Model;

class StockAlert extends Model
{
    protected $fillable = [
        'product_id', 
        'contact_id',
        'variation_id',
        'email',
        'is_recursive',
        'notified'
    ];

    protected $casts = [
        'notified' => 'boolean',
        'is_recursive' => 'boolean'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }

    /**
     * Get the email - use stored email if available, otherwise get from contact
     */
    public function getEmailAttribute($value)
    {
        // If email is stored directly in the column, return it
        if (!empty($value)) {
            return $value;
        }
        
        // Otherwise, get email from contact if available
        if ($this->relationLoaded('contact') || $this->contact) {
            $contact = $this->relationLoaded('contact') ? $this->getRelation('contact') : $this->contact()->first();
            
            if ($contact && !empty($contact->email)) {
                // Store contact email in the email column for future use (if record exists)
                if ($this->exists) {
                    $this->attributes['email'] = $contact->email;
                    $this->saveQuietly();
                }
                return $contact->email;
            }
        }
        
        return null;
    }

    /**
     * Get the name from the related contact
     */
    public function getNameAttribute()
    {
        return $this->contact ? $this->contact->name : null;
    }

    /**
     * Mark as notified and update the timestamp
     */
    public function markAsNotified()
    {
        $this->update(['notified' => true]);
    }

    /**
     * Reset notification status
     */
    public function resetNotification()
    {
        $this->update(['notified' => false]);
    }

    /**
     * Get the stored email value (raw attribute, not accessor)
     */
    public function getStoredEmail()
    {
        return $this->attributes['email'] ?? null;
    }

    /**
     * Handle deletion based on is_recursive flag
     * If email exists and is_recursive is true, don't delete
     * If is_recursive is false, delete
     */
    public function shouldDelete()
    {
        $storedEmail = $this->getStoredEmail();
        
        // If email exists and is_recursive is true, don't delete
        if (!empty($storedEmail) && $this->is_recursive) {
            return false;
        }
        
        // If is_recursive is false, can delete
        return !$this->is_recursive;
    }

    /**
     * Mark as notified and delete if not recursive
     */
    public function markAsNotifiedAndHandleDeletion()
    {
        $this->markAsNotified();
        
        // If not recursive, delete after notification
        if (!$this->is_recursive) {
            $this->delete();
        }
    }
}
<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionInvoice extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscription_invoices';

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
        'line_items' => 'array',
        'billing_period_start' => 'datetime',
        'billing_period_end' => 'datetime',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'subtotal' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'total' => 'decimal:4',
        'amount_paid' => 'decimal:4',
        'amount_due' => 'decimal:4',
        'deleted_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_PARTIALLY_PAID = 'partially_paid';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_no)) {
                $invoice->invoice_no = self::generateInvoiceNumber($invoice->business_id);
            }
        });
    }

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber($business_id)
    {
        $prefix = 'SINV';
        $date = date('Ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return $prefix . $date . $random;
    }

    /**
     * Get the business that owns the invoice.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Get the subscription for this invoice.
     */
    public function subscription()
    {
        return $this->belongsTo(CustomerSubscription::class, 'subscription_id');
    }

    /**
     * Get the contact (customer) for this invoice.
     */
    public function contact()
    {
        return $this->belongsTo(\App\Contact::class);
    }

    /**
     * Get the plan for this invoice.
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Get all transactions for this invoice.
     */
    public function transactions()
    {
        return $this->hasMany(SubscriptionTransaction::class, 'invoice_id');
    }

    /**
     * Get the user who created this invoice.
     */
    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Scope: By status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Unpaid
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PARTIALLY_PAID, self::STATUS_OVERDUE]);
    }

    /**
     * Scope: Paid
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope: Overdue
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_OVERDUE)
            ->orWhere(function ($q) {
                $q->whereIn('status', [self::STATUS_PENDING, self::STATUS_PARTIALLY_PAID])
                    ->where('due_date', '<', now());
            });
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue()
    {
        return $this->status === self::STATUS_OVERDUE || 
               ($this->due_date && $this->due_date->isPast() && !$this->isPaid());
    }

    /**
     * Get the status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'secondary',
            'pending' => 'warning',
            'paid' => 'success',
            'partially_paid' => 'info',
            'overdue' => 'danger',
            'cancelled' => 'dark',
            'refunded' => 'info',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    /**
     * Get formatted total
     */
    public function getFormattedTotalAttribute()
    {
        return $this->currency . ' ' . number_format($this->total, 2);
    }

    /**
     * Get formatted amount due
     */
    public function getFormattedAmountDueAttribute()
    {
        return $this->currency . ' ' . number_format($this->amount_due, 2);
    }

    /**
     * Record a payment
     */
    public function recordPayment($amount, $paymentMethod = null, $gatewayTransactionId = null)
    {
        $this->amount_paid += $amount;
        $this->amount_due = max(0, $this->total - $this->amount_paid);
        
        if ($this->amount_due <= 0) {
            $this->status = self::STATUS_PAID;
            $this->paid_at = now();
        } else {
            $this->status = self::STATUS_PARTIALLY_PAID;
        }
        
        $this->payment_method = $paymentMethod;
        $this->gateway_transaction_id = $gatewayTransactionId;
        $this->save();
        
        return $this;
    }

    /**
     * Mark as overdue
     */
    public function markAsOverdue()
    {
        $this->status = self::STATUS_OVERDUE;
        $this->save();
        return $this;
    }

    /**
     * Cancel invoice
     */
    public function cancelInvoice()
    {
        $this->status = self::STATUS_CANCELLED;
        $this->save();
        return $this;
    }

    /**
     * Refund invoice
     */
    public function refund()
    {
        $this->status = self::STATUS_REFUNDED;
        $this->save();
        return $this;
    }
}

<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionTransaction extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscription_transactions';

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
        'gateway_response' => 'array',
        'next_retry_at' => 'datetime',
        'amount' => 'decimal:4',
        'fee_amount' => 'decimal:4',
        'net_amount' => 'decimal:4',
        'deleted_at' => 'datetime',
    ];

    /**
     * Transaction type constants
     */
    const TYPE_PAYMENT = 'payment';
    const TYPE_REFUND = 'refund';
    const TYPE_CHARGEBACK = 'chargeback';
    const TYPE_CREDIT = 'credit';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_FEE = 'fee';
    const TYPE_PRORATION = 'proration';

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_DISPUTED = 'disputed';

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_no)) {
                $transaction->transaction_no = self::generateTransactionNumber($transaction->business_id);
            }
        });
    }

    /**
     * Generate unique transaction number
     */
    public static function generateTransactionNumber($business_id)
    {
        $prefix = 'STX';
        $date = date('Ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return $prefix . $date . $random;
    }

    /**
     * Get the business that owns the transaction.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Get the subscription for this transaction.
     */
    public function subscription()
    {
        return $this->belongsTo(CustomerSubscription::class, 'subscription_id');
    }

    /**
     * Get the invoice for this transaction.
     */
    public function invoice()
    {
        return $this->belongsTo(SubscriptionInvoice::class, 'invoice_id');
    }

    /**
     * Get the contact (customer) for this transaction.
     */
    public function contact()
    {
        return $this->belongsTo(\App\Contact::class);
    }

    /**
     * Get the user who created this transaction.
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
     * Scope: By type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Completed payments
     */
    public function scopeCompletedPayments($query)
    {
        return $query->where('type', self::TYPE_PAYMENT)
            ->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope: Failed transactions
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope: Pending retry
     */
    public function scopePendingRetry($query)
    {
        return $query->where('status', self::STATUS_FAILED)
            ->whereNotNull('next_retry_at')
            ->where('next_retry_at', '<=', now());
    }

    /**
     * Check if transaction is successful
     */
    public function isSuccessful()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if transaction failed
     */
    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Get the status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'dark',
            'refunded' => 'info',
            'disputed' => 'danger',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    /**
     * Get the type badge color
     */
    public function getTypeBadgeAttribute()
    {
        $badges = [
            'payment' => 'success',
            'refund' => 'info',
            'chargeback' => 'danger',
            'credit' => 'primary',
            'adjustment' => 'warning',
            'fee' => 'secondary',
            'proration' => 'info',
        ];
        return $badges[$this->type] ?? 'secondary';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        $sign = in_array($this->type, [self::TYPE_REFUND, self::TYPE_CHARGEBACK, self::TYPE_CREDIT]) ? '-' : '';
        return $sign . $this->currency . ' ' . number_format($this->amount, 2);
    }

    /**
     * Get masked card number
     */
    public function getMaskedCardAttribute()
    {
        if (!$this->card_last_four) {
            return null;
        }
        return '**** **** **** ' . $this->card_last_four;
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted($gatewayTransactionId = null, $gatewayResponse = null)
    {
        $this->status = self::STATUS_COMPLETED;
        $this->gateway_transaction_id = $gatewayTransactionId ?? $this->gateway_transaction_id;
        $this->gateway_response = $gatewayResponse;
        $this->save();
        
        return $this;
    }

    /**
     * Mark as failed
     */
    public function markAsFailed($reason, $responseCode = null, $responseMessage = null)
    {
        $this->status = self::STATUS_FAILED;
        $this->failure_reason = $reason;
        $this->gateway_response_code = $responseCode;
        $this->gateway_response_message = $responseMessage;
        $this->attempt_count++;
        
        // Set next retry if attempts are below threshold
        $maxRetries = config('subscription.payment.retry_failed_payments', 3);
        $retryInterval = config('subscription.payment.retry_interval_hours', 24);
        
        if ($this->attempt_count < $maxRetries) {
            $this->next_retry_at = now()->addHours($retryInterval);
        }
        
        $this->save();
        
        return $this;
    }

    /**
     * Process refund
     */
    public function processRefund($amount = null)
    {
        $refundAmount = $amount ?? $this->amount;
        
        $refundTransaction = self::create([
            'business_id' => $this->business_id,
            'subscription_id' => $this->subscription_id,
            'invoice_id' => $this->invoice_id,
            'contact_id' => $this->contact_id,
            'type' => self::TYPE_REFUND,
            'status' => self::STATUS_COMPLETED,
            'amount' => $refundAmount,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'payment_gateway' => $this->payment_gateway,
            'notes' => 'Refund for transaction: ' . $this->transaction_no,
            'created_by' => auth()->id(),
        ]);
        
        $this->status = self::STATUS_REFUNDED;
        $this->save();
        
        return $refundTransaction;
    }
}

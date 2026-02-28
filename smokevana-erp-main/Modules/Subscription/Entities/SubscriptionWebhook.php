<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;

class SubscriptionWebhook extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscription_webhooks';

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
        'payload' => 'array',
        'headers' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PROCESSED = 'processed';
    const STATUS_FAILED = 'failed';
    const STATUS_SKIPPED = 'skipped';

    /**
     * Get the business that owns this webhook.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Get the related subscription.
     */
    public function subscription()
    {
        return $this->belongsTo(CustomerSubscription::class, 'subscription_id');
    }

    /**
     * Get the related transaction.
     */
    public function transaction()
    {
        return $this->belongsTo(SubscriptionTransaction::class, 'transaction_id');
    }

    /**
     * Scope: By status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Pending webhooks
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: Failed webhooks
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope: By gateway
     */
    public function scopeByGateway($query, $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    /**
     * Scope: By event type
     */
    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Check if webhook is already processed (idempotency check)
     */
    public static function isAlreadyProcessed($idempotencyKey)
    {
        if (empty($idempotencyKey)) {
            return false;
        }

        return self::where('idempotency_key', $idempotencyKey)
            ->where('status', self::STATUS_PROCESSED)
            ->exists();
    }

    /**
     * Mark as processing
     */
    public function markAsProcessing()
    {
        $this->status = self::STATUS_PROCESSING;
        $this->processing_attempts++;
        $this->save();
        return $this;
    }

    /**
     * Mark as processed
     */
    public function markAsProcessed($subscriptionId = null, $transactionId = null)
    {
        $this->status = self::STATUS_PROCESSED;
        $this->processed_at = now();
        $this->subscription_id = $subscriptionId;
        $this->transaction_id = $transactionId;
        $this->save();
        return $this;
    }

    /**
     * Mark as failed
     */
    public function markAsFailed($errorMessage, $errorTrace = null)
    {
        $this->status = self::STATUS_FAILED;
        $this->error_message = $errorMessage;
        $this->error_trace = $errorTrace;
        $this->save();
        return $this;
    }

    /**
     * Mark as skipped
     */
    public function markAsSkipped($reason)
    {
        $this->status = self::STATUS_SKIPPED;
        $this->error_message = $reason;
        $this->processed_at = now();
        $this->save();
        return $this;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'processing' => 'info',
            'processed' => 'success',
            'failed' => 'danger',
            'skipped' => 'secondary',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    /**
     * Can be retried
     */
    public function canBeRetried()
    {
        return $this->status === self::STATUS_FAILED && $this->processing_attempts < 5;
    }

    /**
     * Retry processing
     */
    public function retry()
    {
        if (!$this->canBeRetried()) {
            return false;
        }

        $this->status = self::STATUS_PENDING;
        $this->error_message = null;
        $this->error_trace = null;
        $this->save();

        return true;
    }

    /**
     * Create webhook record
     */
    public static function record($gateway, $eventType, $payload, $headers = null, $businessId = null)
    {
        return self::create([
            'webhook_id' => $payload['id'] ?? uniqid('wh_'),
            'gateway' => $gateway,
            'event_type' => $eventType,
            'payload' => $payload,
            'headers' => $headers,
            'business_id' => $businessId,
            'idempotency_key' => $payload['idempotency_key'] ?? $payload['id'] ?? null,
            'ip_address' => request()->ip(),
            'status' => self::STATUS_PENDING,
        ]);
    }
}

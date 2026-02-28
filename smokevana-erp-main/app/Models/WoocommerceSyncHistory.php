<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WoocommerceSyncHistory extends Model
{
    use HasFactory;

    protected $table = 'woocommerce_sync_history';

    protected $fillable = [
        'business_id',
        'sync_type',
        'status',
        'total_items',
        'synced_count',
        'failed_count',
        'skipped_count',
        'details',
        'error_message',
        'started_at',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'details' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    /**
     * Update sync progress
     *
     * @param int $synced
     * @param int $failed
     * @return void
     */
    public function updateProgress($synced, $failed)
    {
        $this->update([
            'synced_count' => $synced,
            'failed_count' => $failed,
            'status' => self::STATUS_IN_PROGRESS,
        ]);
    }

    /**
     * Mark sync as completed
     *
     * @param int $synced
     * @param int $failed
     * @param int $skipped
     * @param array $details
     * @return void
     */
    public function markCompleted($synced, $failed, $skipped, $details = [])
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'synced_count' => $synced,
            'failed_count' => $failed,
            'skipped_count' => $skipped,
            'details' => $details,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark sync as failed
     *
     * @param string $errorMessage
     * @return void
     */
    public function markFailed($errorMessage)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
            'completed_at' => now(),
        ]);
    }

    /**
     * Start a new sync operation
     *
     * @param int $businessId
     * @param string $syncType
     * @param int|null $userId
     * @return static
     */
    public static function startSync($businessId, $syncType, $userId = null)
    {
        return static::create([
            'business_id' => $businessId,
            'sync_type' => $syncType,
            'status' => self::STATUS_PENDING,
            'total_items' => 0,
            'synced_count' => 0,
            'failed_count' => 0,
            'skipped_count' => 0,
            'started_at' => now(),
            'created_by' => $userId ?? auth()->id(),
        ]);
    }

    /**
     * Get business relationship
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Get created by user relationship
     */
    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
}

<?php

namespace App\Models;

use App\Transaction;
use Illuminate\Database\Eloquent\Model;

class DropshipOrderTracking extends Model
{
    protected $table = 'dropship_order_tracking';

    protected $guarded = ['id'];

    protected $casts = [
        'shipping_cost' => 'decimal:4',
        'vendor_payout_amount' => 'decimal:4',
        'customer_notified_shipped' => 'boolean',
        'customer_notified_delivered' => 'boolean',
        'meta_data' => 'array',
        'order_placed_at' => 'datetime',
        'vendor_notified_at' => 'datetime',
        'vendor_accepted_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'woocommerce_last_sync' => 'datetime',
        'vendor_payout_date' => 'datetime',
    ];

    /**
     * Fulfillment status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_VENDOR_NOTIFIED = 'vendor_notified';
    const STATUS_VENDOR_ACCEPTED = 'vendor_accepted';
    const STATUS_PROCESSING = 'processing';
    const STATUS_READY_TO_SHIP = 'ready_to_ship';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_RETURNED = 'returned';

    /**
     * Sync status constants
     */
    const SYNC_PENDING = 'pending';
    const SYNC_SYNCED = 'synced';
    const SYNC_FAILED = 'failed';
    const SYNC_RETRYING = 'retrying';
    const SYNC_NOT_APPLICABLE = 'not_applicable'; // For ERP dropship vendors (no WooCommerce sync)

    /**
     * Get the child transaction (wp_sales_order)
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    /**
     * Get the parent transaction (original sales_order)
     */
    public function parentTransaction()
    {
        return $this->belongsTo(Transaction::class, 'parent_transaction_id');
    }

    /**
     * Get the vendor
     */
    public function vendor()
    {
        return $this->belongsTo(WpVendor::class, 'wp_vendor_id');
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->whereIn('fulfillment_status', [
            self::STATUS_PENDING,
            self::STATUS_VENDOR_NOTIFIED,
            self::STATUS_VENDOR_ACCEPTED,
            self::STATUS_PROCESSING
        ]);
    }

    /**
     * Scope for shipped orders
     */
    public function scopeShipped($query)
    {
        return $query->whereIn('fulfillment_status', [
            self::STATUS_SHIPPED,
            self::STATUS_IN_TRANSIT,
            self::STATUS_OUT_FOR_DELIVERY
        ]);
    }

    /**
     * Scope for completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('fulfillment_status', [
            self::STATUS_DELIVERED,
            self::STATUS_COMPLETED
        ]);
    }

    /**
     * Scope for orders needing sync
     */
    public function scopeNeedsSync($query)
    {
        return $query->where(function($q) {
            $q->whereIn('sync_status', [self::SYNC_PENDING, self::SYNC_FAILED, ''])
              ->orWhereNull('sync_status');
        })->where('sync_attempts', '<', 5);
    }

    /**
     * Scope for a specific vendor
     */
    public function scopeForVendor($query, $vendor_id)
    {
        return $query->where('wp_vendor_id', $vendor_id);
    }

    /**
     * Scope for a specific business
     */
    public function scopeForBusiness($query, $business_id)
    {
        return $query->where('business_id', $business_id);
    }

    /**
     * Update fulfillment status with timestamp
     */
    public function updateStatus($status)
    {
        $timestampMap = [
            self::STATUS_VENDOR_NOTIFIED => 'vendor_notified_at',
            self::STATUS_VENDOR_ACCEPTED => 'vendor_accepted_at',
            self::STATUS_SHIPPED => 'shipped_at',
            self::STATUS_DELIVERED => 'delivered_at',
            self::STATUS_COMPLETED => 'completed_at',
        ];

        $data = ['fulfillment_status' => $status];

        if (isset($timestampMap[$status])) {
            $data[$timestampMap[$status]] = now();
        }

        $this->update($data);

        return $this;
    }

    /**
     * Add tracking information
     */
    public function addTracking($tracking_number, $carrier = null, $tracking_url = null)
    {
        $this->update([
            'tracking_number' => $tracking_number,
            'carrier' => $carrier,
            'carrier_tracking_url' => $tracking_url,
            'fulfillment_status' => self::STATUS_SHIPPED,
            'shipped_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark sync as successful
     */
    public function markSynced()
    {
        $this->update([
            'sync_status' => self::SYNC_SYNCED,
            'woocommerce_last_sync' => now(),
            'sync_error_message' => null,
        ]);

        return $this;
    }

    /**
     * Mark sync as failed
     */
    public function markSyncFailed($error_message = null)
    {
        $this->update([
            'sync_status' => self::SYNC_FAILED,
            'sync_attempts' => $this->sync_attempts + 1,
            'sync_error_message' => $error_message,
        ]);

        return $this;
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'pending' => 'bg-secondary',
            'vendor_notified' => 'bg-info',
            'vendor_accepted' => 'bg-primary',
            'processing' => 'bg-warning',
            'ready_to_ship' => 'bg-purple',
            'shipped' => 'bg-cyan',
            'in_transit' => 'bg-orange',
            'out_for_delivery' => 'bg-yellow',
            'delivered' => 'bg-success',
            'completed' => 'bg-green',
            'cancelled' => 'bg-danger',
            'returned' => 'bg-dark'
        ];

        $labels = [
            'pending' => 'Pending',
            'vendor_notified' => 'Vendor Notified',
            'vendor_accepted' => 'Vendor Accepted',
            'processing' => 'Processing',
            'ready_to_ship' => 'Ready to Ship',
            'shipped' => 'Shipped',
            'in_transit' => 'In Transit',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'returned' => 'Returned'
        ];

        $color = $colors[$this->fulfillment_status] ?? 'bg-secondary';
        $label = $labels[$this->fulfillment_status] ?? ucfirst($this->fulfillment_status);

        return "<span class='badge {$color}'>{$label}</span>";
    }

    /**
     * Get sync status badge HTML
     */
    public function getSyncStatusBadgeAttribute()
    {
        $colors = [
            'pending' => 'bg-warning',
            'synced' => 'bg-success',
            'failed' => 'bg-danger',
            'retrying' => 'bg-info',
            'syncing' => 'bg-info',
            'not_applicable' => 'bg-secondary',
            '' => 'bg-warning' // Empty string - needs sync
        ];

        $labels = [
            'pending' => 'Pending',
            'synced' => 'Synced',
            'failed' => 'Failed',
            'retrying' => 'Retrying',
            'syncing' => 'Syncing...',
            'not_applicable' => 'Portal Only',
            '' => 'Not Synced' // Empty string - needs sync
        ];

        $status = $this->sync_status ?? '';
        $color = $colors[$status] ?? 'bg-warning';
        $label = $labels[$status] ?? 'Not Synced';

        return "<span class='badge {$color}'>{$label}</span>";
    }

    /**
     * Get tracking URL
     */
    public function getTrackingUrlAttribute()
    {
        if ($this->carrier_tracking_url) {
            return $this->carrier_tracking_url;
        }

        // Generate tracking URL based on carrier
        if ($this->tracking_number && $this->carrier) {
            $carrier = strtolower($this->carrier);
            $tracking = $this->tracking_number;

            $urls = [
                'usps' => "https://tools.usps.com/go/TrackConfirmAction?tLabels={$tracking}",
                'ups' => "https://www.ups.com/track?tracknum={$tracking}",
                'fedex' => "https://www.fedex.com/apps/fedextrack/?tracknumbers={$tracking}",
                'dhl' => "https://www.dhl.com/us-en/home/tracking/tracking-global-forwarding.html?submit=1&tracking-id={$tracking}",
            ];

            return $urls[$carrier] ?? null;
        }

        return null;
    }

    /**
     * Check if this is a WooCommerce managed order (status synced from WC)
     */
    public function isWooCommerceManagedOrder()
    {
        // If the transaction type is wp_sales_order, status is managed by WooCommerce
        $transaction = $this->transaction;
        if ($transaction && $transaction->type === 'wp_sales_order') {
            return true;
        }
        return false;
    }
    
    /**
     * Check if this is an ERP Dropship (Vendor Dropship) order
     * These orders are managed by vendors through their portal - ERP admin cannot edit
     */
    public function isERPDropshipOrder()
    {
        $transaction = $this->transaction;
        if ($transaction) {
            // Check for erp_dropship_order sub_type or type
            if ($transaction->sub_type === 'erp_dropship_order' || $transaction->type === 'erp_dropship_order') {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check if this is a vendor-managed order (either WooCommerce or ERP Dropship)
     * These orders are read-only for ERP admin
     */
    public function isVendorManagedOrder()
    {
        return $this->isWooCommerceManagedOrder() || $this->isERPDropshipOrder();
    }
    
    /**
     * Check if order can be edited
     * WooCommerce orders and ERP Dropship orders cannot be edited from ERP - they are managed by vendors
     */
    public function canEdit()
    {
        // WooCommerce and ERP Dropship orders can't be manually edited - status comes from vendors
        if ($this->isVendorManagedOrder()) {
            return false;
        }
        
        return in_array($this->fulfillment_status, [
            self::STATUS_PENDING,
            self::STATUS_VENDOR_NOTIFIED,
            self::STATUS_VENDOR_ACCEPTED,
            self::STATUS_PROCESSING
        ]);
    }

    /**
     * Check if tracking can be added
     * WooCommerce orders and ERP Dropship orders cannot have tracking added from ERP - it comes from vendors
     */
    public function canAddTracking()
    {
        // WooCommerce and ERP Dropship orders can't have tracking added manually - comes from vendors
        if ($this->isVendorManagedOrder()) {
            return false;
        }
        
        return in_array($this->fulfillment_status, [
            self::STATUS_VENDOR_ACCEPTED,
            self::STATUS_PROCESSING,
            self::STATUS_READY_TO_SHIP
        ]);
    }

    /**
     * Get time elapsed since order placed
     */
    public function getTimeElapsedAttribute()
    {
        if ($this->order_placed_at) {
            return $this->order_placed_at->diffForHumans();
        }
        return $this->created_at->diffForHumans();
    }

    /**
     * Get fulfillment duration in hours
     */
    public function getFulfillmentDurationHoursAttribute()
    {
        if ($this->completed_at && $this->order_placed_at) {
            return $this->order_placed_at->diffInHours($this->completed_at);
        }
        return null;
    }
}













<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorStoreMapping extends Model
{
    protected $table = 'vendor_store_mappings';

    protected $guarded = ['id'];

    protected $casts = [
        'is_primary' => 'boolean',
        'auto_sync_products' => 'boolean',
        'auto_sync_orders' => 'boolean',
        'auto_sync_inventory' => 'boolean',
        'connection_healthy' => 'boolean',
        'settings' => 'array',
        'last_connection_test' => 'datetime',
    ];

    /**
     * Get the vendor this mapping belongs to
     */
    public function vendor()
    {
        return $this->belongsTo(WpVendor::class, 'wp_vendor_id');
    }

    /**
     * Scope for primary store
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope for active stores
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

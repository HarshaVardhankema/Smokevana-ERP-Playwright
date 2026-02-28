<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Dropship module permissions
     */
    protected $permissions = [
        // Vendor Portal Permissions
        'dropship.vendor.view_dashboard',
        'dropship.vendor.view_own_products',
        'dropship.vendor.manage_stock',
        'dropship.vendor.view_own_orders',
        'dropship.vendor.fulfill_orders',
        'dropship.vendor.add_tracking',
        'dropship.vendor.view_earnings',
        
        // Admin Permissions
        'dropship.admin.view_dashboard',
        'dropship.admin.manage_vendors',
        'dropship.admin.create_vendor',
        'dropship.admin.edit_vendor',
        'dropship.admin.delete_vendor',
        'dropship.admin.manage_product_mappings',
        'dropship.admin.view_all_orders',
        'dropship.admin.sync_products',
        'dropship.admin.view_analytics',
        'dropship.admin.manage_settings',
    ];

    /**
     * Run the migrations.
     */
    public function up()
    {
        $guard = 'web';

        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => $guard]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Permission::whereIn('name', $this->permissions)->delete();
    }
};







<?php

namespace App\Models;

use App\Product;
use App\Transaction;
use App\User;
use App\Contact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WpVendor extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'margin_percentage' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'default_markup_percentage' => 'decimal:2',
        'is_synced_from_woocommerce' => 'boolean',
        'woocommerce_last_sync' => 'datetime',
        'allow_product_edit' => 'boolean',
    ];

    /**
     * Vendor type constants
     */
    const TYPE_ERP = 'erp';                    // Normal internal vendor
    const TYPE_WOOCOMMERCE = 'woocommerce';    // Auto-synced from WooCommerce (read-only)
    const TYPE_ERP_DROPSHIP = 'erp_dropship';  // ERP vendors who fulfill via portal

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PENDING = 'pending';

    /**
     * Commission type constants
     */
    const COMMISSION_PERCENTAGE = 'percentage';
    const COMMISSION_FIXED = 'fixed';

    /**
     * Get all vendor types for dropdown
     */
    public static function getVendorTypes()
    {
        return [
            self::TYPE_ERP => 'ERP Vendor (Internal)',
            self::TYPE_WOOCOMMERCE => 'WooCommerce Vendor (Auto-Synced)',
            self::TYPE_ERP_DROPSHIP => 'ERP Dropship Vendor (Portal Fulfillment)',
        ];
    }

    /**
     * Get creatable vendor types (excludes WooCommerce which is auto-created)
     */
    public static function getCreatableVendorTypes()
    {
        return [
            self::TYPE_ERP => 'ERP Vendor (Internal)',
            self::TYPE_ERP_DROPSHIP => 'ERP Dropship Vendor (Portal Fulfillment)',
        ];
    }

    /**
     * Get dropship-capable vendor types
     */
    public static function getDropshipVendorTypes()
    {
        return [
            self::TYPE_WOOCOMMERCE => 'WooCommerce Vendor',
            self::TYPE_ERP_DROPSHIP => 'ERP Dropship Vendor',
        ];
    }

    /**
     * Get the products associated with the WpVendor on many to many basis
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'products_wp_vendors_table_pivot', 'wp_vendor_id', 'product_id')
            ->withPivot([
                'vendor_cost_price',
                'vendor_markup_amount',
                'vendor_markup_percentage',
                'dropship_selling_price',
                'vendor_sku',
                'is_primary_vendor',
                'lead_time_days',
                'min_order_qty',
                'status',
                'vendor_stock_qty',
                'stock_last_updated',
                'notes'
            ])
            ->withTimestamps();
    }

    /**
     * Get active products for this vendor
     */
    public function activeProducts()
    {
        return $this->products()->wherePivot('status', 'active');
    }

    /**
     * Get the user associated with this vendor (for portal access)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the contact (supplier) associated with this vendor
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get all dropship orders for this vendor
     */
    public function orders()
    {
        return $this->hasMany(DropshipOrderTracking::class, 'wp_vendor_id');
    }

    /**
     * Get pending orders for this vendor
     */
    public function pendingOrders()
    {
        return $this->orders()->whereIn('fulfillment_status', ['pending', 'vendor_notified', 'vendor_accepted', 'processing']);
    }

    /**
     * Get completed orders for this vendor
     */
    public function completedOrders()
    {
        return $this->orders()->where('fulfillment_status', 'completed');
    }

    /**
     * Get store mappings for this vendor (multi-store support)
     */
    public function storeMappings()
    {
        return $this->hasMany(VendorStoreMapping::class, 'wp_vendor_id');
    }

    /**
     * Get the primary store for this vendor
     */
    public function primaryStore()
    {
        return $this->storeMappings()->where('is_primary', true)->first();
    }

    /**
     * Scope for active vendors
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for vendors by business
     */
    public function scopeForBusiness($query, $business_id)
    {
        return $query->where('business_id', $business_id);
    }

    /**
     * Scope for ERP vendors only
     */
    public function scopeErpVendors($query)
    {
        return $query->where('vendor_type', self::TYPE_ERP);
    }

    /**
     * Scope for WooCommerce vendors only
     */
    public function scopeWoocommerceVendors($query)
    {
        return $query->where('vendor_type', self::TYPE_WOOCOMMERCE);
    }

    /**
     * Scope for ERP Dropship vendors only
     */
    public function scopeErpDropshipVendors($query)
    {
        return $query->where('vendor_type', self::TYPE_ERP_DROPSHIP);
    }

    /**
     * Scope for dropship-capable vendors (WooCommerce + ERP Dropship)
     */
    public function scopeDropshipCapable($query)
    {
        return $query->whereIn('vendor_type', [self::TYPE_WOOCOMMERCE, self::TYPE_ERP_DROPSHIP]);
    }

    /**
     * Scope for manually editable vendors (not auto-synced from WooCommerce)
     */
    public function scopeEditable($query)
    {
        return $query->whereIn('vendor_type', [self::TYPE_ERP, self::TYPE_ERP_DROPSHIP]);
    }

    /**
     * Check if vendor is active
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if vendor is WooCommerce type (read-only)
     */
    public function isWoocommerceVendor()
    {
        return $this->vendor_type === self::TYPE_WOOCOMMERCE;
    }

    /**
     * Check if vendor is ERP type
     */
    public function isErpVendor()
    {
        return $this->vendor_type === self::TYPE_ERP;
    }

    /**
     * Check if vendor is ERP Dropship type (portal fulfillment)
     */
    public function isErpDropshipVendor()
    {
        return $this->vendor_type === self::TYPE_ERP_DROPSHIP;
    }

    /**
     * Check if vendor can fulfill dropship orders
     */
    public function canFulfillDropship()
    {
        return in_array($this->vendor_type, [self::TYPE_WOOCOMMERCE, self::TYPE_ERP_DROPSHIP]);
    }

    /**
     * Check if vendor is editable (not auto-synced from WooCommerce)
     */
    public function isEditable()
    {
        return !$this->isWoocommerceVendor();
    }

    /**
     * Check if vendor needs WooCommerce sync
     */
    public function needsWoocommerceSync()
    {
        return $this->vendor_type === self::TYPE_WOOCOMMERCE;
    }

    /**
     * Get total products count
     */
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }

    /**
     * Get active products count
     */
    public function getActiveProductsCountAttribute()
    {
        return $this->activeProducts()->count();
    }

    /**
     * Get pending orders count
     */
    public function getPendingOrdersCountAttribute()
    {
        return $this->pendingOrders()->count();
    }

    /**
     * Get total completed orders count
     */
    public function getCompletedOrdersCountAttribute()
    {
        return $this->completedOrders()->count();
    }

    /**
     * Calculate total revenue from this vendor's orders
     */
    public function getTotalRevenueAttribute()
    {
        return $this->orders()
            ->where('fulfillment_status', 'completed')
            ->sum('vendor_payout_amount');
    }

    /**
     * Get display name (company name or vendor name)
     */
    public function getDisplayNameAttribute()
    {
        return $this->company_name ?: $this->name;
    }

    /**
     * Get logo URL
     */
    public function getLogoUrlAttribute()
    {
        if (!empty($this->logo)) {
            return asset('uploads/vendor_logos/' . $this->logo);
        }
        return asset('img/default-vendor.png');
    }

    /**
     * Get the full address formatted
     */
    public function getFormattedAddressAttribute()
    {
        return $this->address;
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'active' => 'bg-success',
            'inactive' => 'bg-danger',
            'pending' => 'bg-warning'
        ];

        $color = $colors[$this->status] ?? 'bg-secondary';
        $label = ucfirst($this->status);

        return "<span class='badge {$color}'>{$label}</span>";
    }

    /**
     * Get vendor type badge HTML
     */
    public function getVendorTypeBadgeAttribute()
    {
        $colors = [
            self::TYPE_ERP => 'bg-secondary',
            self::TYPE_WOOCOMMERCE => 'bg-purple',
            self::TYPE_ERP_DROPSHIP => 'bg-info',
        ];

        $labels = [
            self::TYPE_ERP => 'ERP',
            self::TYPE_WOOCOMMERCE => 'WooCommerce',
            self::TYPE_ERP_DROPSHIP => 'Dropship',
        ];

        $color = $colors[$this->vendor_type] ?? 'bg-secondary';
        $label = $labels[$this->vendor_type] ?? ucfirst($this->vendor_type);

        return "<span class='badge {$color}'>{$label}</span>";
    }

    /**
     * Get vendor type label
     */
    public function getVendorTypeLabelAttribute()
    {
        $labels = self::getVendorTypes();
        return $labels[$this->vendor_type] ?? ucfirst($this->vendor_type);
    }

    /**
     * Create vendor portal user with proper permissions
     */
    public function createPortalUser($password = null)
    {
        if ($this->user_id) {
            return $this->user;
        }

        $password = $password ?: \Str::random(12);

        $user = User::create([
            'surname' => $this->name,
            'first_name' => 'Vendor',
            'last_name' => $this->name,
            'username' => 'vendor_' . $this->id,
            'email' => $this->email,
            'password' => bcrypt($password),
            'business_id' => $this->business_id,
            'user_type' => 'vendor',
            'status' => 'active',
        ]);

        $this->update(['user_id' => $user->id]);
        
        // Assign vendor role if exists, otherwise create it
        $this->assignVendorRole($user);

        return $user;
    }
    
    /**
     * Assign vendor role with dropship permissions
     */
    protected function assignVendorRole($user)
    {
        $business_id = $this->business_id;
        $roleName = "Dropship Vendor#$business_id";
        
        // Check if role exists
        $role = \Spatie\Permission\Models\Role::where('name', $roleName)
            ->where('guard_name', 'web')
            ->first();
        
        if (!$role) {
            // Create the role with vendor permissions
            $role = \Spatie\Permission\Models\Role::create([
                'name' => $roleName,
                'guard_name' => 'web',
                'business_id' => $business_id
            ]);
        }
        
        // Ensure permissions exist and assign them to role
        $vendorPermissions = [
            'dropship.vendor_access',
            'dropship.view_products',
            'dropship.manage_products',
            'dropship.view_orders',
            'dropship.manage_orders',
            'dropship.view_earnings',
        ];
        
        foreach ($vendorPermissions as $permissionName) {
            // Create permission if it doesn't exist
            $permission = \Spatie\Permission\Models\Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
            
            // Assign permission to role if not already assigned
            if (!$role->hasPermissionTo($permissionName)) {
                $role->givePermissionTo($permissionName);
            }
        }
        
        // Assign role to user
        if (!$user->hasRole($roleName)) {
            $user->assignRole($roleName);
        }
    }
}

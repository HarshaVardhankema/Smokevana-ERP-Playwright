<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\WpVendor;

class RedirectVendorToPortal
{
    /**
     * Routes that vendor-only users should NOT be able to access
     * They will be redirected to vendor portal
     */
    protected $protectedPrefixes = [
        'home',
        'products',
        'sells',
        'purchases',
        'contacts',
        'users',
        'roles',
        'business',
        'account',
        'cash-register',
        'stock',
        'reports',
        'notification',
        'settings',
        'brands',
        'categories',
        'units',
        'tax',
        'warranties',
        'variation',
        'selling-price',
        'bookkeeping',
        'modules',
        'import',
        'export',
        'labels',
        'restaurant',
        'table',
        'modifiers',
        'printer',
        'opening-stock',
        'payment',
        'expense',
        'asset',
        'subscription',
        'packages',
        'superadmin',
        'crm',
        'woocommerce',
        'dropshipping',
    ];

    /**
     * Routes that are ALLOWED for vendors (will NOT redirect)
     */
    protected $allowedPrefixes = [
        'vendor-portal',
        'vendorlogin',
        'login',
        'logout',
        'password',
        'register',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        // Skip if not logged in
        if (!$user) {
            return $next($request);
        }

        // Get the first segment of the URL
        $segment = $request->segment(1);

        // Always allow certain routes
        if (in_array($segment, $this->allowedPrefixes)) {
            return $next($request);
        }

        // Check if this user is vendor-only
        if ($this->isVendorOnlyUser($user)) {
            // Redirect to vendor portal
            return redirect()->route('vendor.dashboard')
                ->with('info', 'You have been redirected to your vendor portal.');
        }

        return $next($request);
    }

    /**
     * Check if user is a vendor-only user (should not access admin panel)
     */
    protected function isVendorOnlyUser($user)
    {
        // Check if user has a vendor profile
        $vendorProfile = WpVendor::where('user_id', $user->id)->first();
        
        if (!$vendorProfile) {
            return false;
        }

        // Check if user has vendor permissions
        $hasVendorPermissions = $user->can('dropship.vendor.access_portal') 
            || $user->can('dropship.vendor_access');

        if (!$hasVendorPermissions) {
            return false;
        }

        // Check if user has admin/dashboard access permissions
        // If they have ANY of these, they are NOT vendor-only
        $adminPermissions = [
            'dashboard.data',
            'admin',
            'superadmin',
            'access_all_locations',
            'product.view',
            'product.create',
            'sell.view',
            'sell.create',
            'purchase.view',
            'purchase.create',
            'user.view',
            'user.create',
            'brand.view',
            'category.view',
            'unit.view',
            'tax_rate.view',
            'expense.access',
            'account.access',
        ];

        foreach ($adminPermissions as $permission) {
            if ($user->can($permission)) {
                return false; // User has admin access
            }
        }

        // User is vendor-only
        return true;
    }
}

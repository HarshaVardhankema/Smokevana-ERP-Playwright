<?php

namespace App\Http\Controllers\VendorPortal;

use App\Http\Controllers\Controller;
use App\Models\WpVendor;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class VendorAuthController extends Controller
{
    /**
     * Show the vendor login form
     */
    public function showLoginForm()
    {
        // If vendor is already logged in, redirect to dashboard
        if (Auth::check() && session('vendor_portal.vendor_id')) {
            return redirect()->route('vendor.dashboard');
        }

        return view('vendor_portal.auth.login');
    }

    /**
     * Handle vendor login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'No account found with this email address.']);
        }

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Invalid credentials provided.']);
        }

        // Find associated vendor
        $vendor = WpVendor::where('user_id', $user->id)->first();

        if (!$vendor) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'This account is not associated with a vendor. Please contact admin.']);
        }

        // Check if vendor is active
        if ($vendor->status !== 'active') {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Your vendor account is not active. Please contact admin.']);
        }

        // Check if vendor is a dropship vendor (allowed to access portal)
        if (!$vendor->canFulfillDropship()) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Your account does not have portal access. Please contact admin.']);
        }

        // Log in the user
        Auth::login($user, $request->filled('remember'));

        // Store vendor info in session
        $this->setVendorSession($vendor);

        Log::info('Vendor logged in', [
            'vendor_id' => $vendor->id,
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return redirect()->route('vendor.dashboard')
            ->with('success', 'Welcome back, ' . $vendor->display_name . '!');
    }

    /**
     * Log the vendor out
     */
    public function logout(Request $request)
    {
        $vendorId = session('vendor_portal.vendor_id');
        
        Log::info('Vendor logged out', ['vendor_id' => $vendorId]);

        // Clear vendor session data
        Session::forget('vendor_portal');

        // Log out the user
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('vendor.login')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Set vendor session data
     */
    protected function setVendorSession(WpVendor $vendor)
    {
        // Get pending orders count
        $pendingOrdersCount = $vendor->pendingOrders()->count();
        $pendingProductRequestsCount = DB::table('vendor_product_requests')
            ->where('wp_vendor_id', $vendor->id)
            ->where('status', 'pending')
            ->count();

        Session::put('vendor_portal', [
            'vendor_id' => $vendor->id,
            'vendor' => $vendor,
            'business_id' => $vendor->business_id,
            'pending_orders_count' => $pendingOrdersCount,
            'pending_product_requests_count' => $pendingProductRequestsCount,
            'vendor_type' => $vendor->vendor_type,
        ]);
    }

    /**
     * Refresh vendor session (call this when data changes)
     */
    public static function refreshVendorSession()
    {
        $vendorId = session('vendor_portal.vendor_id');
        
        if ($vendorId) {
            $vendor = WpVendor::find($vendorId);
            if ($vendor) {
                $pendingOrdersCount = $vendor->pendingOrders()->count();
                $pendingProductRequestsCount = DB::table('vendor_product_requests')
                    ->where('wp_vendor_id', $vendor->id)
                    ->where('status', 'pending')
                    ->count();
                
                Session::put('vendor_portal', [
                    'vendor_id' => $vendor->id,
                    'vendor' => $vendor,
                    'business_id' => $vendor->business_id,
                    'pending_orders_count' => $pendingOrdersCount,
                    'pending_product_requests_count' => $pendingProductRequestsCount,
                    'vendor_type' => $vendor->vendor_type,
                ]);
            }
        }
    }
}

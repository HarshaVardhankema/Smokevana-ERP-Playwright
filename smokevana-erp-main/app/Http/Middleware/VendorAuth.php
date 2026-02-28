<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorAuth
{
    /**
     * Handle an incoming request.
     * Ensures the user is authenticated and is a vendor with portal access.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'msg' => 'Unauthorized. Please login.'], 401);
            }
            return redirect()->route('vendor.login')->with('error', 'Please login to continue.');
        }

        // Check if vendor session exists
        $vendorId = session('vendor_portal.vendor_id');
        
        if (!$vendorId) {
            // User is logged in but doesn't have vendor session
            // This might be an admin trying to access vendor portal
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'msg' => 'Access denied. Vendor account required.'], 403);
            }
            
            Auth::logout();
            return redirect()->route('vendor.login')
                ->with('error', 'Access denied. Please login with a vendor account.');
        }

        // Verify vendor still exists and is active
        $vendor = \App\Models\WpVendor::find($vendorId);
        
        if (!$vendor) {
            session()->forget('vendor_portal');
            Auth::logout();
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor account not found.');
        }

        if ($vendor->status !== 'active') {
            session()->forget('vendor_portal');
            Auth::logout();
            return redirect()->route('vendor.login')
                ->with('error', 'Your vendor account has been deactivated.');
        }

        // Verify the logged-in user matches the vendor's user
        if ($vendor->user_id !== Auth::id()) {
            session()->forget('vendor_portal');
            Auth::logout();
            return redirect()->route('vendor.login')
                ->with('error', 'Session mismatch. Please login again.');
        }

        return $next($request);
    }
}

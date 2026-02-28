<?php

namespace App\Http\Middleware;

use App\BusinessLocation;
use App\Brands;
use App\GuestSession;
use Closure;
use Illuminate\Http\Request;

class EcomGuestValidate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse | \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Extract location_id and brand_name from route parameters
        $locationId = $request->route('location_id');
        $brandName = $request->route('brand_name');
        
        // Validate location exists and is active
        $location = BusinessLocation::where('id', $locationId)
            ->where('business_id', '!=', null)
            ->first();
            
        if (!$location) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid location.',
            ], 404);
        }
        
        // Validate brand exists for this location's business
        $brand = null;
        if ($brandName) {
            $brand = Brands::where('business_id', $location->business_id)
                ->where('slug', $brandName)
                ->first();
                
            if (!$brand) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid brand for this location.',
                ], 404);
            }
        }
        
        // Get guest token from header
        $guestToken = $request->query('guest_session');
        
        // If guest token is provided, validate it
        if ($guestToken) {
            $guestSession = GuestSession::findValidSession($guestToken, $locationId, $brand->id);
            
            if (!$guestSession) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or expired guest session.',
                ], 401);
            }
            
            // Add guest session info to request
            $request->merge([
                'current_guest_session' => $guestSession,
                'current_location' => $location,
                'current_brand' => $brand,
                'current_business_id' => $location->business_id
            ]);
        } else {
            // No guest token provided, just add location and brand info
            $request->merge([
                'current_location' => $location,
                'current_brand' => $brand,
                'current_business_id' => $location->business_id
            ]);
        }
        
        return $next($request);
    }
}

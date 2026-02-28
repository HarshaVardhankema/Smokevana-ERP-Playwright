<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\BusinessLocation;
use App\Brands;
use App\GuestSession;

class EcomUnifiedAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\JsonResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $locationId = $request->route('location_id');
        $brandName = $request->route('brand_name');

        // Validate location exists and is active
        $location = BusinessLocation::where('id', $locationId)
            ->where('is_active', 1)
            ->first();

        if (!$location) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid location or location is inactive.'
            ], 404);
        }

        // Validate brand exists and is active
        $brand = Brands::where('slug', $brandName)
            // ->where('is_active', 1)
            ->first();
        if (!$brand) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid brand or brand is inactive.'
            ], 404);
        }else{
            $request->merge(['brand_id' => $brand->id]);
        }

        // Check if request has guest_session parameter
        $guestSessionId = $request->query('guest_session');
        
        if ($guestSessionId) {
            // This is a guest request - validate with clear error reasons
            $guestSessionByUuid = GuestSession::where('uuid', $guestSessionId)->first();
            if (!$guestSessionByUuid) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid guest session. Session not found. Create one with POST /api/' . $locationId . '/' . $brandName . '/guest/session',
                ], 401);
            }
            if ($guestSessionByUuid->location_id != $locationId || (int) $guestSessionByUuid->brand_id !== (int) $brand->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Guest session does not match this store (location/brand). Use a session created for this store or create one with POST /api/' . $locationId . '/' . $brandName . '/guest/session',
                ], 401);
            }
            if ($guestSessionByUuid->expires_at <= \Carbon\Carbon::now()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Guest session has expired. Create a new one with POST /api/' . $locationId . '/' . $brandName . '/guest/session',
                ], 401);
            }
            $guestSession = $guestSessionByUuid;
            
            // Attach guest session to request
            $request->attributes->add(['current_guest_session' => $guestSession]);
            $request->attributes->add(['is_guest_request' => true]);
        } else {
            // This should be an authenticated customer request
            if (!Auth::guard('api')->check()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Authentication required. Please login or provide guest session.'
                ], 401);
            }
            
            $user = Auth::guard('api')->user();
            
            // Validate user belongs to this location and brand
            if ($user->location_id != $locationId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Forbidden: User not authorized for this location.'
                ], 403);
            }
            
            if ($user->brand_id != $brand->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Forbidden: User not authorized for this brand.'
                ], 403);
            }
            
            $request->attributes->add(['is_guest_request' => false]);
        }

        // Attach location and brand to request for use in controllers
        $request->attributes->add(['current_location' => $location]);
        $request->attributes->add(['current_brand' => $brand]);

        return $next($request);
    }
}

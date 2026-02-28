<?php

namespace App\Http\Middleware;

use App\BusinessLocation;
use App\GuestSession;
use Closure;
use Illuminate\Http\Request;

class EcomUnifiedGuestValidate
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
        // Extract location_id from route parameters (no brand_name for unified routes)
        $locationId = $request->route('location_id');
        
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
        
        // Get guest token from header or query
        $guestToken = $request->query('guest_session');
        
        // If guest token is provided, validate it (without brand restriction)
        if ($guestToken) {
            // For unified routes, we validate guest session by location only (no brand)
            // Note: GuestSession uses 'uuid' field, not 'session_token'
            $guestSession = GuestSession::where('uuid', $guestToken)
                ->where('location_id', $locationId)
                ->where('expires_at', '>', now())
                ->first();
            
            if (!$guestSession) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or expired guest session.',
                ], 401);
            }
            
            // Add guest session info to request (no brand)
            $request->merge([
                'current_guest_session' => $guestSession,
                'current_location' => $location,
                'current_business_id' => $location->business_id
            ]);
        } else {
            // No guest token provided, just add location info (no brand)
            $request->merge([
                'current_location' => $location,
                'current_business_id' => $location->business_id
            ]);
        }
        
        return $next($request);
    }
}


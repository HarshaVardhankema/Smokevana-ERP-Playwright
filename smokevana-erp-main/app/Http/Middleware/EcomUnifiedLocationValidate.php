<?php

namespace App\Http\Middleware;

use App\BusinessLocation;
use Closure;
use Illuminate\Http\Request;

class EcomUnifiedLocationValidate
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
        // Extract location_id from route parameters
        $locationId = $request->route('location_id');
        
        // Validate location exists and is active
        $location = BusinessLocation::where('id', $locationId)
            ->whereNotNull('business_id')
            ->first();
            
        if (!$location) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid location.',
            ], 404);
        }
        
        // Add location info to request for use in controllers (no brand validation)
        $request->merge([
            'current_location' => $location,
            'current_business_id' => $location->business_id
        ]);
        
        return $next($request);
    }
}


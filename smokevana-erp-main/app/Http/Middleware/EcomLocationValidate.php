<?php

namespace App\Http\Middleware;

use App\BusinessLocation;
use App\Brands;
use Closure;
use Illuminate\Http\Request;

class EcomLocationValidate
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
        
        // Add location and brand info to request for use in controllers (which website/store)
        $request->merge([
            'current_location' => $location,
            'current_brand' => $brand ?? null,
            'location_id' => $location->id,
            'brand_id' => isset($brand) ? $brand->id : null,
            'current_business_id' => $location->business_id
        ]);
        
        return $next($request);
    }
}

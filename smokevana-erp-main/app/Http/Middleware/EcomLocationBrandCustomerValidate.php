<?php

namespace App\Http\Middleware;

use App\BusinessLocation;
use App\Brands;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Server\Exception\OAuthServerException;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;

class EcomLocationBrandCustomerValidate
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
        
        // Check if user is authenticated
        if (!$request->bearerToken()) {
            return response()->json([
                'status' => false,
                'message' => 'No API token provided.',
            ], 401);
        }
        
        try {
            $user = Auth::guard('api')->user();
            
            if (!$user || !$user->isApproved == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }
            
            // Check if customer has access to this specific location
            if ($user->location_id != $locationId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Access denied. You do not have permission to access this location.',
                ], 403);
            }
            
            // Check if customer has access to this specific brand
            if ($brand && $user->brand_id != $brand->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Access denied. You do not have permission to access this brand.',
                ], 403);
            }
            
            // Add location and brand info to request for use in controllers
            $request->merge([
                'current_location' => $location,
                'current_brand' => $brand,
                'current_business_id' => $location->business_id
            ]);
            
            return $next($request);
            
        } catch (OAuthServerException | RequiredConstraintsViolated $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired token.',
            ], 401);
        }
    }
}

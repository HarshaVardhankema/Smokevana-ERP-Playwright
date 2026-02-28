<?php

namespace App\Http\Middleware;

use App\GuestSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Server\Exception\OAuthServerException;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;

class EcomAuthOrGuest
{
    /**
     * Handle an incoming request.
     *
     * Allows either:
     * - Authenticated API customer (Bearer token), or
     * - Valid guest session (guest_session query or header).
     *
     * Used for routes like wishlist that support both logged-in and guest users
     * without location/brand in the path.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Try Bearer token (authenticated customer)
        if ($request->bearerToken()) {
            try {
                $user = Auth::guard('api')->user();
                if ($user && $user->isApproved == 1) {
                    return $next($request);
                }
            } catch (OAuthServerException | RequiredConstraintsViolated $e) {
                // Fall through to guest check or 401
            }
        }

        // 2. Try guest_session (query or header)
        $guestToken = $request->query('guest_session') ?: $request->header('guest_session');
        if ($guestToken) {
            $locationId = config('services.b2b.location_id', 1);
            $guestSession = GuestSession::where('uuid', $guestToken)
                ->where('location_id', $locationId)
                ->where('expires_at', '>', now())
                ->first();

            if ($guestSession) {
                $request->attributes->set('current_guest_session', $guestSession);
                return $next($request);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Authentication required. Please login or provide a valid guest_session.',
        ], 401);
    }
}

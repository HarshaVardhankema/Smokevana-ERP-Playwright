<?php

namespace App\Http\Middleware;

use App\GuestSession;
use Closure;
use Illuminate\Http\Request;

class EcomB2bGuestStrict
{
    /**
     * Handle an incoming request.
     *
     * For guest cart routes:
     * - guest_session is REQUIRED (query string or header)
     * - If missing or invalid, return 401
     * - If valid, attach current_guest_session to the request
     */
    public function handle(Request $request, Closure $next)
    {
        $locationId = config('services.b2b.location_id', 1);

        // Try to get guest token from query or header
        $guestToken = $request->query('guest_session') ?: $request->header('guest_session');

        if (!$guestToken) {
            return response()->json([
                'status' => false,
                'message' => 'guest_session is required for this endpoint.',
            ], 401);
        }

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

        // Attach validated guest session for controllers
        $request->merge([
            'current_guest_session' => $guestSession,
        ]);

        return $next($request);
    }
}


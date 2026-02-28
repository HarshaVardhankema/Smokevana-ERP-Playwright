<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictUserAgent
{
    private const ALLOWED_AGENT = 'Trivida-Labs-Corporation';
    private const SESSION_KEY = 'ua_override_allowed';
    private const UNLOCK_ROUTE = 'ua/unlock';

    /**
     * Routes that are excluded from user agent restriction.
     * Vendors can access these routes without the special user agent.
     */
    private const EXCLUDED_ROUTES = [
        '/',               // Root - redirects to vendor login
        'vendorlogin',
        'vendor-portal',
        'vendor-portal/*',
    ];

    /**
     * Allow only the approved User-Agent unless a valid override is set.
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
        $userAgent = $request->userAgent();

        // Whitelist: Allow vendor portal routes without user agent restriction
        if ($this->isVendorPortalRoute($request)) {
            return $next($request);
        }

        // Whitelist: Allow access to /invoice/{} paths on erp.smokevana.com
        if (
            $request->getHost() === 'erp.smokevana.com' && request()->segment(1) === 'invoice'
        ) {
            return $next($request);
        }

        // Already unlocked for this session.
        if ($request->session()->get(self::SESSION_KEY) === true) {
            return $next($request);
        }

        // Let unlock attempts run before blocking.
        if ($this->isUnlockAttempt($request)) {
            return $this->attemptUnlock($request);
        }

        // Allow the expected User-Agent.
        if ($userAgent !== null && stripos($userAgent, self::ALLOWED_AGENT) !== false) {
            return $next($request);
        }

        // Remember intended URL to return after unlock.
        $request->session()->put('url.intended', $request->fullUrl());

        return response()->view('errors.restricted', [
            'allowedAgent' => self::ALLOWED_AGENT,
            'userAgent' => $userAgent,
            'error' => null,
        ], 403);
    }

    /**
     * Check if the current request is for a vendor portal route.
     * These routes are excluded from user agent restriction.
     */
    private function isVendorPortalRoute(Request $request): bool
    {
        foreach (self::EXCLUDED_ROUTES as $route) {
            if ($request->is($route)) {
                return true;
            }
        }
        return false;
    }

    private function isUnlockAttempt(Request $request): bool
    {
        return $request->is(self::UNLOCK_ROUTE) && $request->isMethod('post');
    }

    private function attemptUnlock(Request $request)
    {
        $token = (string) $request->input('token');
        $expected = (string) env('UA_OVERRIDE_TOKEN', '');

        if ($expected !== '' && hash_equals($expected, $token)) {
            $request->session()->put(self::SESSION_KEY, true);
            $intended = $request->session()->pull('url.intended', url('/'));

            return redirect()->to($intended);
        }

        return response()->view('errors.restricted', [
            'allowedAgent' => self::ALLOWED_AGENT,
            'userAgent' => $request->userAgent(),
            'error' => 'Invalid unlock token.',
        ], 403);
    }
}


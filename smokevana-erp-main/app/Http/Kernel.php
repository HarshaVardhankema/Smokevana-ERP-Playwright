<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // Run after session is available so unlock token can be stored.
            \App\Http\Middleware\RestrictUserAgent::class,
            // Redirect vendor-only users to vendor portal
            \App\Http\Middleware\RedirectVendorToPortal::class,
            \App\Http\Middleware\LogUserAccess::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'language' => \App\Http\Middleware\Language::class,
        'timezone' => \App\Http\Middleware\Timezone::class,
        'SetSessionData' => \App\Http\Middleware\SetSessionData::class,
        'setData' => \App\Http\Middleware\IsInstalled::class,
        'authh' => \App\Http\Middleware\IsInstalled::class,
        'EcomApi' => \App\Http\Middleware\EcomApi::class,
        'AdminSidebarMenu' => \App\Http\Middleware\AdminSidebarMenu::class,
        'superadmin' => \App\Http\Middleware\Superadmin::class,
        'CheckUserLogin' => \App\Http\Middleware\CheckUserLogin::class,
        'ecom.customer.validate' => \App\Http\Middleware\EcomCustomerValidate::class,
        'ecom.auth.or.guest' => \App\Http\Middleware\EcomAuthOrGuest::class,
        'ecom.location.validate' => \App\Http\Middleware\EcomLocationValidate::class,
        'ecom.location.customer.validate' => \App\Http\Middleware\EcomLocationCustomerValidate::class,
        'ecom.location.brand.customer.validate' => \App\Http\Middleware\EcomLocationBrandCustomerValidate::class,
        'ecom.guest.validate' => \App\Http\Middleware\EcomGuestValidate::class,
        'ecom.unified.auth' => \App\Http\Middleware\EcomUnifiedAuth::class,
        'ecom.b2cunified.location.validate' => \App\Http\Middleware\EcomUnifiedLocationValidate::class,
        'ecom.b2cunified.guest.validate' => \App\Http\Middleware\EcomUnifiedGuestValidate::class,
        'ecom.b2b.guest.strict' => \App\Http\Middleware\EcomB2bGuestStrict::class,
        'user.access' => \App\Http\Middleware\UserAccessMiddleware::class,
        'restrict.useragent' => \App\Http\Middleware\RestrictUserAgent::class,
        'redirect.vendor' => \App\Http\Middleware\RedirectVendorToPortal::class,
        'vendor.auth' => \App\Http\Middleware\VendorAuth::class,
    ];
}

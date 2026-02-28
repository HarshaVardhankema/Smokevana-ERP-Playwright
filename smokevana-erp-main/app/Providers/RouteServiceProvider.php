<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            // Unified B2C routes at root level (no /api prefix)
            Route::prefix('b2c-api')
                ->middleware('api')
                ->group(base_path('routes/b2c-unified.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

                 Route::group([], function () {
                require base_path('routes/vendor_portal.php');
            });

        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(2000)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('b2c-api', function (Request $request) {
            return Limit::perMinute(2000)->by($request->user()?->id ?: $request->ip());
        });
        RateLimiter::for('throttle5pm', function ($request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'status' => false,
                        'message' => 'Too many requests!',
                    ]);
                });
        });
    }
    
}

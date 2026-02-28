<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Configure token expiry times
        Passport::personalAccessTokensExpireIn(Carbon::now()->addDays(3)); //->addMinutes(20));
        Passport::tokensExpireIn(Carbon::now()->addDays(3)); //->addMinutes(20));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(3));
        
        Gate::before(function ($user, $ability) {
            if (in_array($ability, ['backup', 'superadmin',
                'manage_modules', ])) {
                $administrator_list = config('constants.administrator_usernames');

                if (in_array(strtolower($user->username), explode(',', strtolower($administrator_list)))) {
                    return true;
                }
            } else {
                if ($user->hasRole('Admin#'.$user->business_id)) {
                    return true;
                }
            }
        });
    }
}

<?php

namespace Modules\Subscription\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Console\Scheduling\Schedule;

class SubscriptionServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Subscription';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'subscription';

    /**
     * Console commands to be registered
     */
    protected $commands = [
        \Modules\Subscription\Console\ExpireSubscriptionsCommand::class,
        \Modules\Subscription\Console\SendRenewalRemindersCommand::class,
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerCommands();
        $this->registerSchedule();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register console commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }
    }

    /**
     * Register the scheduled tasks.
     *
     * @return void
     */
    protected function registerSchedule()
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            
            // Run subscription expiry check daily at midnight
            $schedule->command('subscription:expire')
                ->daily()
                ->at('00:00')
                ->withoutOverlapping()
                ->runInBackground()
                ->appendOutputTo(storage_path('logs/subscription-expire.log'));

            // Send renewal reminders daily at 9 AM
            $schedule->command('subscription:send-reminders --days=7')
                ->daily()
                ->at('09:00')
                ->withoutOverlapping()
                ->runInBackground()
                ->appendOutputTo(storage_path('logs/subscription-reminders.log'));
        });
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/' . $this->moduleNameLower;
        }, config('view.paths')), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', $this->moduleNameLower);
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (!app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}

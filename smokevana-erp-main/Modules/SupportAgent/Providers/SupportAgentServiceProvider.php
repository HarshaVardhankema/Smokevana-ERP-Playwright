<?php

namespace Modules\SupportAgent\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\SupportAgent\Services\LLMService;
use Modules\SupportAgent\Services\FeatureDocumentationService;
use Modules\SupportAgent\Services\MCPService;

class SupportAgentServiceProvider extends ServiceProvider
{
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
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        
        // Register LLM Service as singleton
        $this->app->singleton(LLMService::class, function ($app) {
            return new LLMService(config('supportagent.llm'));
        });
        
        // Register Feature Documentation Service as singleton
        $this->app->singleton(FeatureDocumentationService::class, function ($app) {
            return new FeatureDocumentationService();
        });
        
        // Register MCP Service as singleton
        $this->app->singleton(MCPService::class, function ($app) {
            return new MCPService();
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
            __DIR__ . '/../Config/config.php' => config_path('supportagent.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            'supportagent'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/supportagent');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/supportagent';
        }, config('view.paths')), [$sourcePath]), 'supportagent');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/supportagent');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'supportagent');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'supportagent');
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
        return [
            LLMService::class,
            FeatureDocumentationService::class,
            MCPService::class,
        ];
    }
}

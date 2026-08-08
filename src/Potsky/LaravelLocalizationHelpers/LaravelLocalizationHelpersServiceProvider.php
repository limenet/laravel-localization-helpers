<?php

namespace Potsky\LaravelLocalizationHelpers;

use Illuminate\Support\ServiceProvider;
use Potsky\LaravelLocalizationHelpers\Command\LocalizationClear;
use Potsky\LaravelLocalizationHelpers\Command\LocalizationFind;
use Potsky\LaravelLocalizationHelpers\Command\LocalizationMissing;
use Potsky\LaravelLocalizationHelpers\Factory\Localization;

class LaravelLocalizationHelpersServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     *
     * @codeCoverageIgnore
     */
    public function boot(): void
    {
        if (function_exists('config_path')) {
            $this->publishes([
                __DIR__.'/../../config/config.php' => config_path('laravel-localization-helpers.php'),
            ]);
        }
    }

    /**
     * Register the service provider.
     *
     *
     * @codeCoverageIgnore
     */
    #[\Override]
    public function register(): void
    {
        $this->app->singleton('localization.command.missing', fn ($app): LocalizationMissing => new LocalizationMissing($app['config']));

        $this->app->singleton('localization.command.find', fn ($app): LocalizationFind => new LocalizationFind($app['config']));

        $this->app->singleton('localization.command.clear', fn ($app): LocalizationClear => new LocalizationClear($app['config']));

        $this->commands(
            'localization.command.missing',
            'localization.command.find',
            'localization.command.clear'
        );

        $this->app->singleton('localization.helpers', fn ($app): Localization => new Localization(new Factory\MessageBag));

        $this->mergeConfigFrom(
            __DIR__.'/../../config/config.php',
            'laravel-localization-helpers'
        );
    }
}

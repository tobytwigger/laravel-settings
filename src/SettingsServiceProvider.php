<?php

namespace Twigger\Translate;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * The service provider for loading Laravel Setting
 */
class SettingsServiceProvider extends ServiceProvider
{

    /**
     * Bind service classes into the container
     */
    public function register()
    {

    }

    /**
     * Boot the translation services
     *
     * - Allow assets to be published
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        $this->publishAssets();
    }

    /**
     * Publish any assets to allow the end user to customise the functionality of this package
     */
    private function publishAssets()
    {

        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-settings.php', 'laravel-translate'
        );

        $this->publishes([
            __DIR__ . '/../config/laravel-settings.php' => config_path('laravel-settings.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

}

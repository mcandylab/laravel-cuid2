<?php

namespace Mcandylab\LaravelCuid2;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ForeignIdColumnDefinition;
use Illuminate\Support\ServiceProvider;

class LaravelCuid2ServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->registerBlueprintMacros();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-cuid2.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-cuid2'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-cuid2'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-cuid2'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-cuid2');

        // Register the main class to use with the facade
        $this->app->singleton('laravel-cuid2', function () {
            return new LaravelCuid2;
        });
    }

    /**
     * Register schema macros for declaring cuid2 columns in migrations.
     *
     * Usage:
     *   $table->cuid2()->primary();
     *   $table->foreignCuid2('user_id')->constrained();
     *   $table->cuid2Morphs('tokenable');
     *   $table->nullableCuid2Morphs('tokenable');
     */
    protected function registerBlueprintMacros(): void
    {
        if (! Blueprint::hasMacro('cuid2')) {
            Blueprint::macro('cuid2', function (string $column = 'id') {
                /** @var Blueprint $this */
                return $this->char($column, (int) config('laravel-cuid2.length', 24));
            });
        }

        if (! Blueprint::hasMacro('foreignCuid2')) {
            Blueprint::macro('foreignCuid2', function (string $column) {
                /** @var Blueprint $this */
                return $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
                    'type' => 'char',
                    'name' => $column,
                    'length' => (int) config('laravel-cuid2.length', 24),
                ]));
            });
        }

        if (! Blueprint::hasMacro('cuid2Morphs')) {
            Blueprint::macro('cuid2Morphs', function (string $name, ?string $indexName = null, ?string $after = null) {
                /** @var Blueprint $this */
                $this->string("{$name}_type")
                    ->after($after);

                $this->cuid2("{$name}_id")
                    ->after(! is_null($after) ? "{$name}_type" : null);

                $this->index(["{$name}_type", "{$name}_id"], $indexName);
            });
        }

        if (! Blueprint::hasMacro('nullableCuid2Morphs')) {
            Blueprint::macro('nullableCuid2Morphs', function (string $name, ?string $indexName = null, ?string $after = null) {
                /** @var Blueprint $this */
                $this->string("{$name}_type")
                    ->nullable()
                    ->after($after);

                $this->cuid2("{$name}_id")
                    ->nullable()
                    ->after(! is_null($after) ? "{$name}_type" : null);

                $this->index(["{$name}_type", "{$name}_id"], $indexName);
            });
        }
    }
}

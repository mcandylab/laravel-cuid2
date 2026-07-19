<?php

namespace Mcandylab\LaravelCuid2;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\ForeignIdColumnDefinition;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mcandylab\LaravelCuid2\Faker\Cuid2Provider as Cuid2FakerProvider;
use Mcandylab\LaravelCuid2\Rules\Cuid2 as Cuid2Rule;

class LaravelCuid2ServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->registerBlueprintMacros();
        $this->registerValidationRules();
        $this->registerStrMacros();

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

        $this->registerFakerProvider();
    }

    /**
     * Register the Faker provider so `fake()->cuid2()` is available in
     * factories and seeders.
     *
     * The provider is only instantiated once a Faker\Generator is resolved,
     * so this adds no hard runtime dependency on fakerphp/faker.
     */
    protected function registerFakerProvider(): void
    {
        $this->app->afterResolving(\Faker\Generator::class, function (\Faker\Generator $faker): void {
            $faker->addProvider(new Cuid2FakerProvider($faker));
        });
    }

    /**
     * Register schema macros for declaring cuid2 columns in migrations.
     *
     * Every cuid2 column is a varchar (Schema::defaultStringLength): it is fully
     * indexable across databases, does not depend on config('laravel-cuid2.length')
     * — which only drives generation — and fits any Stripe-style
     * {prefix}_{cuid2} value. The prefix itself lives on the model
     * ($cuid2Prefix), so it is not a column concern.
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
                return $this->string($column);
            });
        }

        if (! Blueprint::hasMacro('foreignCuid2')) {
            Blueprint::macro('foreignCuid2', function (string $column) {
                /** @var Blueprint $this */
                return $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
                    'type' => 'string',
                    'name' => $column,
                    'length' => Builder::$defaultStringLength,
                ]));
            });
        }

        if (! Blueprint::hasMacro('cuid2Morphs')) {
            Blueprint::macro('cuid2Morphs', function (string $name, ?string $indexName = null, ?string $after = null) {
                /** @var Blueprint $this */
                $this->string("{$name}_type")
                    ->after($after);

                $this->string("{$name}_id")
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

                $this->string("{$name}_id")
                    ->nullable()
                    ->after(! is_null($after) ? "{$name}_type" : null);

                $this->index(["{$name}_type", "{$name}_id"], $indexName);
            });
        }
    }

    /**
     * Register the `cuid2` validation rule (string, Rule object and Rule macro forms).
     *
     * Usage:
     *   'id' => 'cuid2'                 // any valid CUID2
     *   'id' => 'cuid2:user'            // a `user_`-prefixed CUID2
     *   'id' => [new Cuid2]             // Rule object
     *   'id' => [new Cuid2('user')]
     *   'id' => [Rule::cuid2(prefix: 'user')]
     */
    protected function registerValidationRules(): void
    {
        Validator::extend('cuid2', function ($attribute, $value, $parameters) {
            $prefix = isset($parameters[0]) && $parameters[0] !== '' ? (string) $parameters[0] : null;

            return Cuid2Rule::isValid($value, $prefix);
        }, 'The :attribute must be a valid CUID2.');

        if (! Rule::hasMacro('cuid2')) {
            Rule::macro('cuid2', fn (?string $prefix = null) => new Cuid2Rule($prefix));
        }
    }

    /**
     * Register Str macros, aligning with the core Str::uuid() / Str::ulid() helpers.
     *
     * Usage:
     *   Str::cuid2();            // generate (respects the configured length)
     *   Str::cuid2(10);         // generate with an explicit length
     *   Str::cuid2(prefix: 'user'); // Stripe-style prefixed id
     *   Str::isCuid2($value);   // validate
     *   Str::isCuid2($value, 'user'); // validate a prefixed id
     */
    protected function registerStrMacros(): void
    {
        if (! Str::hasMacro('cuid2')) {
            Str::macro('cuid2', fn (?int $length = null, ?string $prefix = null): string => cuid2($length, $prefix));
        }

        if (! Str::hasMacro('isCuid2')) {
            Str::macro('isCuid2', fn (mixed $value, ?string $prefix = null): bool => Cuid2Rule::isValid($value, $prefix));
        }
    }
}

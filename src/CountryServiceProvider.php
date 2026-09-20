<?php

declare(strict_types=1);

namespace Nurbekjummayev\LaravelCountry;

use Illuminate\Support\ServiceProvider;
use Nurbekjummayev\LaravelCountry\Console\Commands\CountryFlagsDownloadCommand;
use Nurbekjummayev\LaravelCountry\Console\Commands\CountrySeedCommand;
use Nurbekjummayev\LaravelCountry\Database\Seeders\CountrySeeder;

class CountryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/country.php', 'country');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CountrySeedCommand::class,
                CountryFlagsDownloadCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/country.php' => config_path('country.php'),
            ], 'country-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'country-migrations');

            $this->publishes([
                CountrySeeder::dataPath() => database_path('data/country/countries.json'),
            ], 'country-data');

            // Flag images: 250 webp (h240), ~0.4 MB
            // php artisan vendor:publish --tag=country-flags
            $this->publishes([
                CountrySeeder::flagsPath() => public_path(
                    (string) config('country.flags.public_path', 'vendor/country/flags')
                ),
            ], 'country-flags');
        }
    }
}

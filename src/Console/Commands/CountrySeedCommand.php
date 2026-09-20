<?php

declare(strict_types=1);

namespace Nurbekjummayev\LaravelCountry\Console\Commands;

use Illuminate\Console\Command;
use Nurbekjummayev\LaravelCountry\Database\Seeders\CountrySeeder;
use Nurbekjummayev\LaravelCountry\Models\Country;

class CountrySeedCommand extends Command
{
    protected $signature = 'country:seed
        {--fresh : Truncate the table and re-seed}';

    protected $description = 'Seed countries classifier (ISO 3166-1) into the database';

    public function handle(CountrySeeder $seeder): int
    {
        if ($this->option('fresh')) {
            Country::query()->delete();
            $this->warn('Table truncated.');
        }

        $count = count($seeder->rows());

        $this->withProgressBar([1], function () use ($seeder): void {
            $seeder->run();
        });

        $this->newLine(2);
        $this->info("Countries seeded: {$count}.");

        return self::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace Nurbekjummayev\LaravelCountry\Console\Commands;

use Illuminate\Console\Command;
use Nurbekjummayev\LaravelCountry\Database\Seeders\CountrySeeder;
use Nurbekjummayev\LaravelCountry\Models\Country;

class CountrySeedCommand extends Command
{
    protected $signature = 'country:seed
        {--fresh : Jadvalni tozalab, qaytadan to\'ldirish}';

    protected $description = 'Davlatlar klassifikatorini (ISO 3166-1) bazaga yozadi';

    public function handle(CountrySeeder $seeder): int
    {
        if ($this->option('fresh')) {
            Country::query()->delete();
            $this->warn('Jadval tozalandi.');
        }

        $count = count($seeder->rows());

        $this->withProgressBar([1], function () use ($seeder): void {
            $seeder->run();
        });

        $this->newLine(2);
        $this->info("Davlatlar yozildi: {$count} ta.");

        return self::SUCCESS;
    }
}

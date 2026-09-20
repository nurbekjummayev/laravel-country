<?php

declare(strict_types=1);

namespace Nurbekjummayev\LaravelCountry\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Nurbekjummayev\LaravelCountry\Database\Seeders\CountrySeeder;

/**
 * Downloads flag images from source into the package.
 *
 * Usually not needed — images are bundled with the package. This command
 * is for updating the package when a new country is added or a flag changes.
 */
class CountryFlagsDownloadCommand extends Command
{
    protected $signature = 'country:flags:download
        {--force : Re-download existing files}
        {--only=* : Only these codes (e.g. --only=uz --only=kz)}';

    protected $description = 'Download flag images from source into the package';

    public function handle(CountrySeeder $seeder): int
    {
        $template = (string) config('country.flags.source', 'https://flagcdn.com/h240/{code}.webp');
        $dir = CountrySeeder::flagsPath();

        if (! is_dir($dir) && ! mkdir($dir, 0o755, true) && ! is_dir($dir)) {
            $this->error("Failed to create directory: {$dir}");

            return self::FAILURE;
        }

        /** @var list<string> $only */
        $only = array_map('strtolower', (array) $this->option('only'));

        $codes = array_map(
            static fn (array $row): string => strtolower((string) $row['code']),
            $seeder->rows()
        );

        if ($only !== []) {
            $codes = array_values(array_intersect($codes, $only));
        }

        $downloaded = $skipped = 0;
        $failed = [];

        $bar = $this->output->createProgressBar(count($codes));
        $bar->start();

        foreach ($codes as $code) {
            $target = "{$dir}/{$code}.webp";

            if (! $this->option('force') && is_file($target)) {
                $skipped++;
                $bar->advance();

                continue;
            }

            $response = Http::timeout(25)->get(str_replace('{code}', $code, $template));

            if ($response->successful() && str_starts_with($response->body(), 'RIFF')) {
                file_put_contents($target, $response->body());
                $downloaded++;
            } else {
                $failed[$code] = $response->status();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Downloaded: {$downloaded}, skipped: {$skipped}.");

        if ($failed !== []) {
            $this->warn('Failed ('.count($failed).') — codes may not exist in ISO 3166-1:');
            foreach ($failed as $code => $status) {
                $this->line("  {$code} → HTTP {$status}");
            }
        }

        $this->newLine();
        $this->comment('Note: Manually update flag_path in countries.json, then run country:seed.');

        return self::SUCCESS;
    }
}

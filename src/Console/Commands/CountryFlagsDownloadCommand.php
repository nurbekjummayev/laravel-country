<?php

declare(strict_types=1);

namespace Nurbekjummayev\LaravelCountry\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Nurbekjummayev\LaravelCountry\Database\Seeders\CountrySeeder;

/**
 * Paket ichidagi bayroq rasmlarini manbadan qayta yuklaydi.
 *
 * Odatda kerak emas — rasmlar paket bilan birga keladi. Bu buyruq yangi davlat
 * qo'shilganda yoki bayroq o'zgarganda paketni yangilash uchun.
 */
class CountryFlagsDownloadCommand extends Command
{
    protected $signature = 'country:flags:download
        {--force : Mavjud fayllarni ham qayta yuklash}
        {--only=* : Faqat shu kodlar (masalan --only=uz --only=kz)}';

    protected $description = 'Bayroq rasmlarini manbadan paket ichiga yuklaydi';

    public function handle(CountrySeeder $seeder): int
    {
        $template = (string) config('country.flags.source', 'https://flagcdn.com/h240/{code}.webp');
        $dir = CountrySeeder::flagsPath();

        if (! is_dir($dir) && ! mkdir($dir, 0o755, true) && ! is_dir($dir)) {
            $this->error("Papka yaratilmadi: {$dir}");

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

        $this->info("Yuklandi: {$downloaded}, o'tkazib yuborildi: {$skipped}.");

        if ($failed !== []) {
            $this->warn('Yuklanmadi ('.count($failed).') — ISO 3166-1 da yo\'q kodlar bo\'lishi mumkin:');
            foreach ($failed as $code => $status) {
                $this->line("  {$code} → HTTP {$status}");
            }
        }

        $this->newLine();
        $this->comment('Eslatma: countries.json dagi flag_path ni qo\'lda moslang, keyin country:seed.');

        return self::SUCCESS;
    }
}

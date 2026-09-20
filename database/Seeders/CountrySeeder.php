<?php

declare(strict_types=1);

namespace Nurbekjummayev\LaravelCountry\Database\Seeders;

use Illuminate\Database\Seeder;
use Nurbekjummayev\LaravelCountry\Models\Country;
use RuntimeException;

/**
 * Idempotent: `code` (ISO alpha-2) bo'yicha yangilaydi, dublikat yaratmaydi.
 * Qayta ishga tushirish xavfsiz.
 */
class CountrySeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->rows() as $row) {
            Country::query()->updateOrCreate(
                ['code' => strtoupper((string) $row['code'])],
                [
                    'code_alpha3' => strtoupper((string) $row['code_alpha3']),
                    'code_numeric' => $row['code_numeric'] ?? null,
                    'name_uz' => (string) $row['name_uz'],
                    'name_oz' => (string) $row['name_oz'],
                    'name_ru' => (string) $row['name_ru'],
                    'name_en' => (string) $row['name_en'],
                    'flag_path' => $row['flag_path'] ?? null,
                    'is_active' => (bool) ($row['is_active'] ?? true),
                ]
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function rows(): array
    {
        $path = self::dataPath();

        if (! is_file($path)) {
            throw new RuntimeException("Country ma'lumot fayli topilmadi: {$path}");
        }

        /** @var list<array<string, mixed>> $rows */
        $rows = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        return $rows;
    }

    public static function dataPath(): string
    {
        return __DIR__.'/../Data/countries.json';
    }

    /**
     * Paket ichidagi bayroq rasmlari papkasi.
     */
    public static function flagsPath(): string
    {
        return __DIR__.'/../Flags';
    }
}

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Nurbekjummayev\LaravelCountry\Database\Seeders\CountrySeeder;
use Nurbekjummayev\LaravelCountry\Models\Country;

beforeEach(fn () => (new CountrySeeder)->run());

it('seeds all countries', function () {
    expect(Country::count())->toBe(249);
});

it('does not create duplicates when re-run', function () {
    (new CountrySeeder)->run();

    expect(Country::count())->toBe(249);
});

it('finds by alpha-2, alpha-3, and numeric code', function () {
    expect(Country::findByCode('UZ')?->name_en)->toBe('Uzbekistan')
        ->and(Country::findByCode('UZB')?->code)->toBe('UZ')
        ->and(Country::findByCode('860')?->code)->toBe('UZ');
});

it('returns name in current locale', function () {
    $uz = Country::findByCode('UZ');

    expect($uz->translatedName('ru'))->toBe('Узбекистан')
        ->and($uz->translatedName('en'))->toBe('Uzbekistan')
        ->and($uz->translatedName('xx'))->toBe($uz->name_uz);
});

it('uses code as route key', function () {
    expect((new Country)->getRouteKeyName())->toBe('code');
});

it('assigns flag_path to every country', function () {
    expect(Country::query()->whereNotNull('flag_path')->count())->toBe(249);
});

it('has webp file for each flag_path', function () {
    $dir = CountrySeeder::flagsPath();

    $missing = Country::query()
        ->whereNotNull('flag_path')
        ->pluck('flag_path')
        ->reject(fn (string $path): bool => is_file($dir.'/'.basename($path)))
        ->values()
        ->all();

    expect($missing)->toBe([]);
});

it('returns flag_url, null for missing paths', function () {
    expect(Country::findByCode('UZ')->flag_url)
        ->toEndWith('vendor/country/flags/uz.webp')
        ->and(Country::findByCode('SS')->flag_url)
        ->toEndWith('vendor/country/flags/ss.webp');
});

it('uses base_url when configured', function () {
    config()->set('country.flags.base_url', 'https://cdn.example.com');

    expect(Country::findByCode('UZ')->flag_url)
        ->toBe('https://cdn.example.com/vendor/country/flags/uz.webp');
});

it('has complete ISO list — 249 countries, no non-ISO entries', function () {
    expect(Country::query()->count())->toBe(249)
        ->and(Country::query()->whereRaw("code GLOB '*[0-9]*'")->count())->toBe(0);
});

it('includes 8 added countries', function () {
    $added = Country::query()
        ->whereIn('code', ['BL', 'BQ', 'CW', 'IO', 'MF', 'SS', 'SX', 'UM'])
        ->pluck('code')->sort()->values()->all();

    expect($added)->toBe(['BL', 'BQ', 'CW', 'IO', 'MF', 'SS', 'SX', 'UM']);
});

it('excludes old non-ISO codes', function () {
    expect(Country::query()->whereIn('code', ['Z0', 'Z7', 'Z9', 'Y0'])->count())->toBe(0);
});

it('has only expected columns', function () {
    $columns = Schema::getColumnListing((new Country)->getTable());
    sort($columns);

    expect($columns)->toBe([
        'code', 'code_alpha3', 'code_numeric', 'created_at', 'flag_path',
        'id', 'is_active', 'name_en', 'name_oz', 'name_ru', 'name_uz', 'updated_at',
    ]);
});

it('scopeActive returns only active countries', function () {
    Country::query()->where('code', 'UZ')->update(['is_active' => false]);

    expect(Country::query()->active()->where('code', 'UZ')->exists())->toBeFalse()
        ->and(Country::query()->active()->count())->toBe(248);
});

it('casts is_active as boolean', function () {
    $country = Country::findByCode('UZ');

    expect($country->is_active)->toBeTrue()->toBeBool();
});

it('allows mass assignment for fillable fields', function () {
    $country = Country::query()->create([
        'code' => 'ZZ',
        'code_alpha3' => 'ZZZ',
        'code_numeric' => '999',
        'name_uz' => 'Test UZ',
        'name_oz' => 'Test OZ',
        'name_ru' => 'Test RU',
        'name_en' => 'Test EN',
        'flag_path' => 'test.webp',
        'is_active' => true,
    ]);

    expect($country->code)->toBe('ZZ')
        ->and($country->name_en)->toBe('Test EN');
});

it('appends name and flag_url to array', function () {
    $array = Country::findByCode('UZ')->toArray();

    expect($array)->toHaveKeys(['name', 'flag_url']);
});
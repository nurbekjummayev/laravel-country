<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Nurbekjummayev\LaravelCountry\Database\Seeders\CountrySeeder;
use Nurbekjummayev\LaravelCountry\Models\Country;

beforeEach(fn () => (new CountrySeeder)->run());

it('barcha davlatlarni yozadi', function () {
    expect(Country::count())->toBe(250);
});

it('qayta ishga tushirilganda dublikat yaratmaydi', function () {
    (new CountrySeeder)->run();

    expect(Country::count())->toBe(250);
});

it('alpha-2, alpha-3 va numeric kod bo\'yicha topadi', function () {
    expect(Country::findByCode('UZ')?->name_en)->toBe('Uzbekistan')
        ->and(Country::findByCode('UZB')?->code)->toBe('UZ')
        ->and(Country::findByCode('860')?->code)->toBe('UZ');
});

it('nomni joriy tilda qaytaradi', function () {
    $uz = Country::findByCode('UZ');

    expect($uz->translatedName('ru'))->toBe('Узбекистан')
        ->and($uz->translatedName('en'))->toBe('Uzbekistan')
        ->and($uz->translatedName('xx'))->toBe($uz->name_uz);
});

it('route kaliti sifatida kodni ishlatadi', function () {
    expect((new Country)->getRouteKeyName())->toBe('code');
});

it('har bir davlatga flag_path yozadi', function () {
    expect(Country::query()->whereNotNull('flag_path')->count())->toBe(250);
});

it('har bir flag_path uchun webp fayl mavjud', function () {
    $dir = CountrySeeder::flagsPath();

    $missing = Country::query()
        ->whereNotNull('flag_path')
        ->pluck('flag_path')
        ->reject(fn (string $path): bool => is_file($dir.'/'.basename($path)))
        ->values()
        ->all();

    expect($missing)->toBe([]);
});

it('flag_url manzil qaytaradi, kodsizlarda null', function () {
    expect(Country::findByCode('UZ')->flag_url)
        ->toEndWith('vendor/country/flags/uz.webp')
        ->and(Country::findByCode('SS')->flag_url)
        ->toEndWith('vendor/country/flags/ss.webp');
});

it('base_url sozlangan bo\'lsa o\'shani ishlatadi', function () {
    config()->set('country.flags.base_url', 'https://cdn.epauzb.uz');

    expect(Country::findByCode('UZ')->flag_url)
        ->toBe('https://cdn.epauzb.uz/vendor/country/flags/uz.webp');
});

it('ISO ro\'yxati to\'liq — 250 ta, ISO bo\'lmagan yozuv yo\'q', function () {
    expect(Country::query()->count())->toBe(250)
        ->and(Country::query()->whereRaw("code GLOB '*[0-9]*'")->count())->toBe(0);
});

it('yetishmayotgan 9 ta davlat qo\'shilgan', function () {
    $added = Country::query()
        ->whereIn('code', ['BL', 'BQ', 'CW', 'IO', 'MF', 'SS', 'SX', 'UM', 'XK'])
        ->pluck('code')->sort()->values()->all();

    expect($added)->toBe(['BL', 'BQ', 'CW', 'IO', 'MF', 'SS', 'SX', 'UM', 'XK']);
});

it('eski ISO bo\'lmagan kodlar o\'chirilgan', function () {
    expect(Country::query()->whereIn('code', ['Z0', 'Z7', 'Z9', 'Y0'])->count())->toBe(0);
});

it('Kosovo code_numeric siz saqlanadi', function () {
    expect(Country::findByCode('XK')?->code_numeric)->toBeNull();
});

it('faqat kelishilgan ustunlar bor', function () {
    $columns = Schema::getColumnListing((new Country)->getTable());
    sort($columns);

    expect($columns)->toBe([
        'code', 'code_alpha3', 'code_numeric', 'created_at', 'flag_path',
        'id', 'is_active', 'name_en', 'name_oz', 'name_ru', 'name_uz', 'updated_at',
    ]);
});

it('scopeActive faqat faol davlatlarni qaytaradi', function () {
    Country::query()->where('code', 'UZ')->update(['is_active' => false]);

    expect(Country::query()->active()->where('code', 'UZ')->exists())->toBeFalse()
        ->and(Country::query()->active()->count())->toBe(249);
});

it('is_active boolean sifatida qaytadi', function () {
    $country = Country::findByCode('UZ');

    expect($country->is_active)->toBeTrue()->toBeBool();
});

it('fillable maydonlar to\'g\'ri ishlaydi', function () {
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

it('toArray() da name va flag_url mavjud', function () {
    $array = Country::findByCode('UZ')->toArray();

    expect($array)->toHaveKeys(['name', 'flag_url']);
});

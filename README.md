# Laravel Country

[![Tests](https://github.com/nurbekjummayev/laravel-country/actions/workflows/tests.yml/badge.svg)](https://github.com/nurbekjummayev/laravel-country/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/nurbekjummayev/laravel-country.svg?style=flat-square)](https://packagist.org/packages/nurbekjummayev/laravel-country)
[![Total Downloads](https://img.shields.io/packagist/dt/nurbekjummayev/laravel-country.svg?style=flat-square)](https://packagist.org/packages/nurbekjummayev/laravel-country)
[![License](https://img.shields.io/packagist/l/nurbekjummayev/laravel-country.svg?style=flat-square)](https://packagist.org/packages/nurbekjummayev/laravel-country)

Davlatlar klassifikatori: migratsiya, seed ma'lumoti, **bayroq rasmlari** va Eloquent modeli.

**250 davlat · ISO 3166-1 · Laravel 13 · PHP 8.3+**

## O'rnatish

```bash
composer require nurbekjummayev/laravel-country
```

```bash
php artisan migrate
php artisan country:seed
php artisan vendor:publish --tag=country-flags
```

| Publish tagi | Nima chiqadi | Qayerga |
|---|---|---|
| `country-flags` | 250 ta `.webp` bayroq | `public/vendor/country/flags/` |
| `country-config` | `country.php` | `config/` |
| `country-migrations` | migratsiya | `database/migrations/` |
| `country-data` | `countries.json` | `database/data/country/` |

## Ustunlar

| Ustun | Turi | Izoh |
|---|---|---|
| `id` | `bigint` | Auto-increment |
| `code` | `char(2)` | ISO 3166-1 alpha-2, unique |
| `code_alpha3` | `char(3)` | ISO 3166-1 alpha-3, unique |
| `code_numeric` | `char(3)` | ISO 3166-1 numeric, nullable (Kosovo'da yo'q) |
| `name_uz` | `string` | O'zbekcha nomi |
| `name_oz` | `string` | O'zbekcha (kirill) nomi |
| `name_ru` | `string` | Ruscha nomi |
| `name_en` | `string` | Inglizcha nomi |
| `flag_path` | `string` | Bayroq fayl yo'li |
| `is_active` | `boolean` | Faol/nofaol |
| `timestamps` | | `created_at`, `updated_at` |

## Foydalanish

```php
use Nurbekjummayev\LaravelCountry\Models\Country;

// Kod bo'yicha topish (alpha-2, alpha-3, numeric)
Country::findByCode('UZ');    // 'UZB' va '860' ham ishlaydi

// Faqat faol davlatlar
Country::active()->orderBy('name_uz')->get();

// Bir nechta kod bo'yicha
Country::query()->code(['UZ', 'KZ', 'KG'])->get();
```

### Tarjima va bayroq

```php
$uz = Country::findByCode('UZ');

$uz->name;                   // joriy tilda: "O'zbekiston"
$uz->translatedName('ru');   // "Узбекистан"
$uz->flag_path;              // "vendor/country/flags/uz.webp"
$uz->flag_url;               // "https://example.com/vendor/country/flags/uz.webp"
```

### Blade'da

```blade
<img src="{{ $country->flag_url }}" alt="{{ $country->name }}" height="20" loading="lazy">
```

### JSON javobda

`name` va `flag_url` avtomatik qo'shiladi (`#[Appends]`):

```json
{
  "code": "UZ",
  "name": "O'zbekiston",
  "flag_url": "https://example.com/vendor/country/flags/uz.webp"
}
```

## Bayroqlar

| | |
|---|---|
| Format | **WebP**, balandligi 240px |
| Soni | **250** — har bir davlatga |
| Hajmi | **~0.44 MB** jami, o'rtacha 1.8 KB |
| Manba | `flagcdn.com/h240/{code}.webp` |
| Paketda | `database/Flags/{code}.webp` |

Rasmlar **paket ichida keladi** — ishlash paytida tashqi CDN'ga murojaat qilinmaydi.

### CDN orqali berish

Agar bayroqlarni alohida CDN'dan bersangiz:

```php
// config/country.php
'flags' => [
    'base_url' => 'https://cdn.example.com',
],
```

### Bayroqlarni yangilash

```bash
php artisan country:flags:download [--force] [--only=uz]
```

## Route-model binding

Model `code` bo'yicha route'ga bog'lanadi:

```php
// routes/web.php
Route::get('/countries/{country}', function (Country $country) {
    return $country;
});

// /countries/UZ → Country modeli
```

## Servislararo qoida

> **Boshqa servisga `id` emas, `code` yuboring.**

`id` — ichki avtoinkrement, har bir bazada boshqacha.
`code` (ISO alpha-2) — barcha joyda bir xil, o'zgarmas.

## Testlar

```bash
composer test    # 18 test
composer lint
```

## Litsenziya

MIT litsenziyasi. Batafsil [LICENSE](LICENSE) faylida.
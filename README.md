# Laravel Country

[![Tests](https://github.com/nurbekjummayev/laravel-country/actions/workflows/tests.yml/badge.svg)](https://github.com/nurbekjummayev/laravel-country/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/nurbekjummayev/laravel-country.svg?style=flat-square)](https://packagist.org/packages/nurbekjummayev/laravel-country)
[![Total Downloads](https://img.shields.io/packagist/dt/nurbekjummayev/laravel-country.svg?style=flat-square)](https://packagist.org/packages/nurbekjummayev/laravel-country)
[![License](https://img.shields.io/packagist/l/nurbekjummayev/laravel-country.svg?style=flat-square)](https://packagist.org/packages/nurbekjummayev/laravel-country)

Country classifier for Laravel: migration, seed data, **flag images**, and Eloquent model.

**249 countries · ISO 3166-1 · Laravel 13 · PHP 8.3+**

## Installation

```bash
composer require nurbekjummayev/laravel-country
```

```bash
php artisan migrate
php artisan country:seed
php artisan vendor:publish --tag=country-flags
```

| Publish Tag | Content | Destination |
|---|---|---|
| `country-flags` | 249 `.webp` flags | `public/vendor/country/flags/` |
| `country-config` | `country.php` | `config/` |
| `country-migrations` | migration | `database/migrations/` |
| `country-data` | `countries.json` | `database/data/country/` |

## Columns

| Column | Type | Description |
|---|---|---|
| `id` | `bigint` | Auto-increment |
| `code` | `char(2)` | ISO 3166-1 alpha-2, unique |
| `code_alpha3` | `char(3)` | ISO 3166-1 alpha-3, unique |
| `code_numeric` | `char(3)` | ISO 3166-1 numeric, nullable  |
| `name_uz` | `string` | Uzbek name (Latin) |
| `name_oz` | `string` | Uzbek name (Cyrillic) |
| `name_ru` | `string` | Russian name |
| `name_en` | `string` | English name |
| `flag_path` | `string` | Flag file path |
| `is_active` | `boolean` | Active/inactive |
| `timestamps` | | `created_at`, `updated_at` |

## Usage

```php
use Nurbekjummayev\LaravelCountry\Models\Country;

// Find by code (alpha-2, alpha-3, or numeric)
Country::findByCode('UZ');    // 'UZB' and '860' also work

// Only active countries
Country::active()->orderBy('name_en')->get();

// Multiple codes
Country::query()->code(['UZ', 'KZ', 'KG'])->get();
```

### Translation and Flags

```php
$uz = Country::findByCode('UZ');

$uz->name;                   // Current locale: "Uzbekistan"
$uz->translatedName('ru');   // "Узбекистан"
$uz->flag_path;              // "vendor/country/flags/uz.webp"
$uz->flag_url;               // "https://example.com/vendor/country/flags/uz.webp"
```

### In Blade

```blade
<img src="{{ $country->flag_url }}" alt="{{ $country->name }}" height="20" loading="lazy">
```

### JSON Response

`name` and `flag_url` are automatically appended (`#[Appends]`):

```json
{
  "code": "UZ",
  "name": "Uzbekistan",
  "flag_url": "https://example.com/vendor/country/flags/uz.webp"
}
```

## Flags

| | |
|---|---|
| Format | **WebP**, 240px height |
| Count | **249** — one per country |
| Size | **~0.44 MB** total, ~1.8 KB average |
| Source | `flagcdn.com/h240/{code}.webp` |
| In package | `database/Flags/{code}.webp` |

Flag images are **bundled with the package** — no external CDN calls at runtime.

### Serving from CDN

If you serve flags from a separate CDN:

```php
// config/country.php
'flags' => [
    'base_url' => 'https://cdn.example.com',
],
```

### Updating Flags

```bash
php artisan country:flags:download [--force] [--only=uz]
```

## Route Model Binding

The model binds to routes by `code`:

```php
// routes/web.php
Route::get('/countries/{country}', function (Country $country) {
    return $country;
});

// /countries/UZ → Country model
```

## Inter-service Convention

> **Send `code`, not `id`, to other services.**

`id` is an internal auto-increment, different in each database.
`code` (ISO alpha-2) is universal and immutable.

## Testing

```bash
composer test    # 18 tests
composer lint
```

## License

MIT License. See [LICENSE](LICENSE) for details.

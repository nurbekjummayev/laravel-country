# laravel-country

Davlatlar klassifikatori: migratsiya, seed ma'lumoti, **bayroq rasmlari** va Eloquent modeli.

**250 davlat · ISO 3166-1 · Laravel 13 · PHP 8.3+**

## O'rnatish

```json
{
  "repositories": [
    { "type": "path", "url": "../../packages/laravel-country" }
  ],
  "require": { "nurbekjummayev/laravel-country": "*" }
}
```

```bash
composer require nurbekjummayev/laravel-country
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

```
id
code          char(2)   unique     ISO 3166-1 alpha-2
code_alpha3   char(3)   unique     ISO 3166-1 alpha-3
code_numeric  char(3)   unique, nullable   (Kosovo XK da yo'q)
name_uz  name_oz  name_ru  name_en
flag_path     "vendor/country/flags/uz.webp"
is_active
timestamps
```

Ataylab shu bilan cheklangan. Rasmiy nom, poytaxt, qit'a, valyuta, telefon kodi —
kerak bo'lganda qo'shiladi.

## Foydalanish

```php
use Nurbekjummayev\LaravelCountry\Models\Country;

Country::findByCode('UZ');        // 'UZB' va '860' ham ishlaydi
Country::active()->orderBy('name_uz')->get();
Country::query()->code(['UZ', 'KZ', 'KG'])->get();

$uz = Country::findByCode('UZ');
$uz->name;                   // joriy tilda: "O'zbekiston"
$uz->translatedName('ru');   // "Узбекистан"
$uz->flag_path;              // "vendor/country/flags/uz.webp"
$uz->flag_url;               // "https://.../vendor/country/flags/uz.webp"
```

```blade
<img src="{{ $country->flag_url }}" alt="{{ $country->name }}" height="20" loading="lazy">
```

`name` va `flag_url` JSON javobga avtomatik qo'shiladi (`#[Appends]`).

## Bayroqlar

| | |
|---|---|
| Format | **WebP**, balandligi 240px |
| Soni | **250** — har bir davlatga |
| Hajmi | **~0.44 MB** jami, o'rtacha 1.8 KB |
| Manba | `flagcdn.com/h240/{code}.webp` |
| Paketda | `src/Database/Flags/{code}.webp` (kod kichik harfda) |

Rasmlar **paket ichida keladi** — ishlash paytida tashqi CDN'ga murojaat qilinmaydi.

CDN'dan berish uchun (public papkangiz alohida domendan berilsa):

```php
'flags' => ['base_url' => 'https://cdn.epauzb.uz'],   // config/country.php
```

Yangilash: `php artisan country:flags:download [--force] [--only=uz]`

## Ro'yxat qanday tuzatilgan

Dastlabki klassifikator **~2010-yilda muzlab qolgan** edi: 254 yozuvdan 241 tasi
to'g'ri ISO kodi, 13 tasi esa ISO bo'lmagan eski yozuv edi.

**Qo'shildi (9 ta):** `BL` `BQ` `CW` `IO` `MF` `SS` `SX` `UM` `XK`

`SS` — Janubiy Sudan, 2011-yildan BMT a'zosi. `BQ`/`CW`/`SX` — 2010-yilda tarqatilgan
Gollandiya Antil orollarining vorislari.

**O'chirildi (13 ta).** Eski bazadagi havolalarni ko'chirish kerak bo'lsa, moslik jadvali:

| Eski | ISO | | Eski | ISO |
|---|---|---|---|---|
| `Z0` Jonston atoll | `UM` | | `Z6` Buyuk Britaniya | `GB` |
| `Z1` Makao | `MO` | | `Z7` AQSh – Delaver | — *(davlat emas)* |
| `Z2` Svazilend | `SZ` *(Esvatini)* | | `Z8` Gollandiya Antil o. | `BQ`/`CW`/`SX` |
| `Z3` Wake orollari | `UM` | | `Z9` Yugoslaviya | — *(tarqalgan)* |
| `Z4` G'azo sektori | `PS` | | `Y0` Midway orollari | `UM` |
| `Z5` Kanal orollari | `JE`/`GG` | | `Y1` Kerguelen | `TF` |
| | | | `Y2` Britaniya Hind okeani hududi | `IO` |

Natija: **250** — [mledoze/countries](https://github.com/mledoze/countries) ro'yxati bilan
aynan mos. flagpedia'dagi 254 — 252 ta alpha-2 + `EU`/`UN` (tashkilot bayroqlari).

### ⚠️ Ko'rib chiqish kerak

9 ta yangi davlatning **uz/oz nomlari qo'lda tarjima qilingan** — tasdiqlash kerak:

```
BL Sen-Bartelemi              MF Sen-Marten          SS Janubiy Sudan
BQ Karib Niderlandiyasi       SX Sint-Marten         XK Kosovo
CW Kyurasao                   UM AQShning kichik chekka orollari
IO Britaniyaning Hind okeanidagi hududi
```

## Servislararo qoida

> **Boshqa servisga `id` emas, `code` yuboring.**

`id` — ichki avtoinkrement. `code` (ISO alpha-2) — o'zgarmas.
Route-model binding ham kod bo'yicha: `/countries/UZ`.

## API yo'llari (ixtiyoriy)

Sukut bo'yicha o'chirilgan. `config/country.php`:

```php
'routes' => ['enabled' => true, 'prefix' => 'api/countries', 'middleware' => ['api']],
```

```
GET /api/countries?q=uzb&only_active=1
GET /api/countries/UZ
```

## Testlar

```bash
composer test    # 14 test
composer lint
```

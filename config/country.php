<?php

declare(strict_types=1);

return [
    'table' => env('COUNTRY_TABLE', 'countries'),

    /*
    |--------------------------------------------------------------------------
    | Translation Columns
    |--------------------------------------------------------------------------
    | Maps app()->getLocale() to the corresponding column.
    | Falls back to `fallback` if locale is not found.
    */
    'locales' => [
        'uz' => 'name_uz',
        'oz' => 'name_oz',
        'uz_Cyrl' => 'name_oz',
        'ru' => 'name_ru',
        'en' => 'name_en',
    ],

    'fallback' => 'name_uz',

    /*
    |--------------------------------------------------------------------------
    | Flags
    |--------------------------------------------------------------------------
    | Flag images are bundled with the package (250 webp, h240, ~0.4 MB) and
    | published to public folder via `vendor:publish --tag=country-flags`.
    |
    | `public_path` — destination inside public folder.
    | `base_url`    — if null, asset() is used. Set this if your public
    |                 folder is served from a separate CDN.
    | `source`      — URL template for downloading flags.
    */
    'flags' => [
        'public_path' => env('COUNTRY_FLAGS_PATH', 'vendor/country/flags'),
        'base_url' => env('COUNTRY_FLAGS_BASE_URL'),
        'source' => env('COUNTRY_FLAGS_SOURCE', 'https://flagcdn.com/h240/{code}.webp'),
    ],
];

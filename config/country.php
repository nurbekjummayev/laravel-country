<?php

declare(strict_types=1);

return [
    'table' => env('COUNTRY_TABLE', 'countries'),

    /*
    |--------------------------------------------------------------------------
    | Tarjima ustunlari
    |--------------------------------------------------------------------------
    | app()->getLocale() qiymati shu xaritadan ustunga o'giriladi.
    | Topilmasa `fallback` ishlatiladi.
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
    | Bayroqlar
    |--------------------------------------------------------------------------
    | Bayroq rasmlari paket ichida keladi (241 ta webp, h240, ~0.4 MB) va
    | `vendor:publish --tag=country-flags` bilan public papkaga chiqariladi.
    |
    | `public_path`  — nashr qilinadigan joy (public ichida).
    | `base_url`     — null bo'lsa asset() ishlatiladi. Agar public papkangiz
    |                  alohida CDN'dan berilsa, shu yerga o'sha manzilni yozing.
    | `source`       — yangilash buyrug'i qaysi manbadan yuklaydi.
    */
    'flags' => [
        'public_path' => env('COUNTRY_FLAGS_PATH', 'vendor/country/flags'),
        'base_url' => env('COUNTRY_FLAGS_BASE_URL'),
        'source' => env('COUNTRY_FLAGS_SOURCE', 'https://flagcdn.com/h240/{code}.webp'),
    ],
];

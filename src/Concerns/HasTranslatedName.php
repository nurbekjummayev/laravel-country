<?php

declare(strict_types=1);

namespace Nurbekjummayev\LaravelCountry\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * `name` atributi joriy til bo'yicha name_uz/name_oz/name_ru/name_en dan tanlanadi.
 */
trait HasTranslatedName
{
    protected function name(): Attribute
    {
        return Attribute::get(fn (): string => $this->translatedName());
    }

    public function translatedName(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        $map = (array) config('country.locales', []);
        $fallback = (string) config('country.fallback', 'name_uz');
        $column = $map[$locale] ?? $fallback;

        $value = $this->getAttributeValue($column);

        if ($value === null || $value === '') {
            $value = $this->getAttributeValue($fallback);
        }

        return (string) ($value ?? '');
    }
}

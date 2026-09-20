<?php

declare(strict_types=1);

namespace Nurbekjummayev\LaravelCountry\Models;

use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Nurbekjummayev\LaravelCountry\Concerns\HasTranslatedName;

/**
 * @property int $id
 * @property string $code ISO 3166-1 alpha-2
 * @property string $code_alpha3 ISO 3166-1 alpha-3
 * @property string|null $code_numeric ISO 3166-1 numeric (Kosovo'da yo'q)
 * @property string $name_uz
 * @property string $name_oz
 * @property string $name_ru
 * @property string $name_en
 * @property string|null $flag_path
 * @property bool $is_active
 * @property-read string $name
 * @property-read string|null $flag_url
 */
#[Appends(['name', 'flag_url'])]
#[Fillable([
    'code',
    'code_alpha3',
    'code_numeric',
    'name_uz',
    'name_oz',
    'name_ru',
    'name_en',
    'flag_path',
    'is_active',
])]
class Country extends Model
{
    use HasTranslatedName;

    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value): bool => (bool) $value,
            set: fn (bool $value): bool => $value,
        );
    }

    public function getTable(): string
    {
        return (string) config('country.table', 'countries');
    }

    /**
     * Route-model binding tabiiy kalit bo'yicha: /countries/UZ
     */
    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /**
     * Nashr qilingan bayroqning to'liq manzili.
     */
    protected function flagUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $path = $this->getAttributeValue('flag_path');

            if ($path === null || $path === '') {
                return null;
            }

            $base = config('country.flags.base_url');

            return $base !== null && $base !== ''
                ? rtrim((string) $base, '/').'/'.ltrim($path, '/')
                : asset($path);
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  string|list<string>  $code  alpha-2, alpha-3 yoki numeric
     */
    public function scopeCode(Builder $query, string|array $code): Builder
    {
        $codes = array_map(
            static fn (string $c): string => strtoupper(trim($c)),
            (array) $code
        );

        return $query->where(function (Builder $q) use ($codes): void {
            $q->whereIn('code', $codes)
                ->orWhereIn('code_alpha3', $codes)
                ->orWhereIn('code_numeric', $codes);
        });
    }

    public static function findByCode(string $code): ?self
    {
        return static::query()->code($code)->first();
    }
}

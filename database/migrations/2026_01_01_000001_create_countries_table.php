<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create($this->table(), function (Blueprint $table): void {
            $table->id();

            // ISO 3166-1. `code` is the inter-service agreed key.
            $table->char('code', 2)->unique();
            $table->char('code_alpha3', 3)->unique();
            $table->char('code_numeric', 3)->unique();

            $table->string('name_uz', 120);
            $table->string('name_oz', 120);
            $table->string('name_ru', 120);
            $table->string('name_en', 120);

            // e.g. "vendor/country/flags/uz.webp"
            $table->string('flag_path', 191)->nullable();

            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table());
    }

    private function table(): string
    {
        return (string) config('country.table', 'countries');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('constituencies', function (Blueprint $table) {
            $table->id();

            // Parent district (nullable for flexibility, null-on-delete to preserve history)
            $table->foreignId('district_id')
                ->nullable()
                ->constrained('districts')
                ->nullOnDelete();

            $table->string('name', 160);
            $table->string('code', 20)->nullable(); // optional short code
            $table->timestamps();

            // Uniqueness: same constituency name should not repeat inside the same district
            $table->unique(['district_id', 'name'], 'constituencies_district_name_unique');

            // Helpful indexes
            $table->index('district_id');
            $table->unique(['code']); // safe even if null (PG allows many nulls)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('constituencies');
    }
};

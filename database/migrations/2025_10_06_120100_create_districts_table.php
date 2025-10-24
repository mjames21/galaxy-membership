<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();

            // Parent region (nullable to allow soft data loads, null-on-delete to keep historical rows)
            $table->foreignId('region_id')
                ->nullable()
                ->constrained('regions')
                ->nullOnDelete();

            $table->string('name', 160);
            $table->string('code', 20)->nullable(); // optional short code
            $table->timestamps();

            // Uniqueness: same district name should not repeat inside the same region
            $table->unique(['region_id', 'name'], 'districts_region_name_unique');

            // Helpful indexes
            $table->index('region_id');
            $table->unique(['code']); // safe even if null (PG allows many nulls)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};

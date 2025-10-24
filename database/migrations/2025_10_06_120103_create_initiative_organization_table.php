<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('initiative_organization', function (Blueprint $table) {
            $table->id();

            // FKs
            $table->foreignId('initiative_id')
                  ->constrained('initiatives')
                  ->cascadeOnDelete();

            $table->foreignId('organization_id')
                  ->constrained('organizations')
                  ->cascadeOnDelete();

            // Optional role/notes (keep or remove)
            $table->string('role', 100)->nullable();
            $table->timestamps();

            // Prevent duplicates
            $table->unique(['initiative_id','organization_id'], 'initiative_org_unique');

            // Helpful index for lookups by org
            $table->index('organization_id', 'initiative_org_org_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('initiative_organization');
    }
};

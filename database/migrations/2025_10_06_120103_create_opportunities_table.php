<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180);
            $table->string('prospect', 20)->nullable()->index(); // certain|high|medium|low|none
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('owner_person_id')->nullable()->constrained('people')->nullOnDelete();
            $table->decimal('value', 14, 2)->nullable();
            $table->timestamps();

            $table->index(['organization_id','owner_person_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('opportunities'); }
};

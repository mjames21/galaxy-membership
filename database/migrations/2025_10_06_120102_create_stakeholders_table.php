<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stakeholders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('willingness_level_id')->nullable()->constrained('willingness_levels')->nullOnDelete();
            $table->foreignId('influence_level_id')->nullable()->constrained('influence_levels')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['organization_id','willingness_level_id','influence_level_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('stakeholders'); }
};

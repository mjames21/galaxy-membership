<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('organization_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('person_id')->nullable()->constrained('people')->nullOnDelete();
            $table->string('title', 120)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 60)->nullable();
            $table->timestamps();

            $table->index(['organization_id','person_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('organization_contacts'); }
};

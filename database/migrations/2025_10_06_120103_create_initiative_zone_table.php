<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('initiative_zone', function (Blueprint $table) {
            $table->id();
            $table->foreignId('initiative_id')->constrained('initiatives')->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained('zones')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['initiative_id','zone_id'],'initiative_zone_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('initiative_zone'); }
};

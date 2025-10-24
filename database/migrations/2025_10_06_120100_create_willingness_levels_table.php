<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('willingness_levels', function (Blueprint $table) {
            $table->id();
            $table->string('label', 80);
            $table->integer('rank')->nullable();   // for ordering in charts
            $table->timestamps();

            $table->unique('label');
        });
    }
    public function down(): void { Schema::dropIfExists('willingness_levels'); }
};

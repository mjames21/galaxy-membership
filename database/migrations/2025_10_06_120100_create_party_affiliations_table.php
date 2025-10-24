<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('party_affiliations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('code', 20)->nullable();
            $table->timestamps();

            $table->unique('name');
            $table->unique(['code']);
        });
    }
    public function down(): void { Schema::dropIfExists('party_affiliations'); }
};

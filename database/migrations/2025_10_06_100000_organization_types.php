<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('organization_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('code', 40)->nullable(); // optional short code, e.g. GOV, NGO
            $table->timestamps();

            $table->unique('name');
            $table->unique(['code']); // safe with NULLs in Postgres
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_types');
    }
};

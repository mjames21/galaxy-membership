<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 120);
            $table->string('last_name', 120)->nullable();
            $table->string('other_names', 120)->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phone', 40)->nullable()->unique();
            $table->string('address')->nullable();
            $table->timestamps();

            $table->index('last_name');
            $table->index('first_name');
        });
    }
    public function down(): void { Schema::dropIfExists('people'); }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('initiatives', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180);
            $table->foreignId('status_id')->nullable()->constrained('initiative_statuses')->nullOnDelete();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('owner_org_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->timestamps();

            $table->index(['status_id','owner_org_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('initiatives'); }
};

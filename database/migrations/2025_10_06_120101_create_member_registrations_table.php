<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('member_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->string('member_no', 60)->nullable();
            $table->date('joined_at')->nullable();
            $table->timestamps();

            $table->index(['person_id','region_id']);
            $table->unique(['member_no'], 'member_registrations_member_no_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('member_registrations'); }
};

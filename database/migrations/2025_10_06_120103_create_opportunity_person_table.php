<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('opportunity_person', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opportunity_id')->constrained('opportunities')->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->string('role', 100)->nullable(); // e.g. contact/approver
            $table->timestamps();

            $table->unique(['opportunity_id','person_id','role'],'opp_person_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('opportunity_person'); }
};

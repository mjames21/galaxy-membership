<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('organization_affiliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('affiliation_level_id')->nullable()->constrained('affiliation_levels')->nullOnDelete();
            $table->foreignId('party_affiliation_id')->nullable()->constrained('party_affiliations')->nullOnDelete();
            $table->timestamps();

            $table->index(['organization_id','affiliation_level_id','party_affiliation_id'],'org_affiliation_idx');
        });
    }
    public function down(): void { Schema::dropIfExists('organization_affiliations'); }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('executive_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('executive_assignments', 'start_date')) {
                $table->date('start_date')->nullable()->after('scope_id');
            }
            if (!Schema::hasColumn('executive_assignments', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('executive_assignments', 'term_number')) {
                $table->unsignedTinyInteger('term_number')->default(1)->after('end_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('executive_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('executive_assignments', 'term_number')) {
                $table->dropColumn('term_number');
            }
            if (Schema::hasColumn('executive_assignments', 'end_date')) {
                $table->dropColumn('end_date');
            }
            if (Schema::hasColumn('executive_assignments', 'start_date')) {
                $table->dropColumn('start_date');
            }
        });
    }
};

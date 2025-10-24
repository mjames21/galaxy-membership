<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stakeholders', function (Blueprint $table) {
            if (!Schema::hasColumn('stakeholders', 'willingness_level_id')) {
                $table->foreignId('willingness_level_id')
                      ->nullable()
                      ->after('party_affiliation_id')
                      ->constrained('willingness_levels')
                      ->nullOnDelete();
                $table->index('willingness_level_id'); // for the dashboard GROUP BY
            }
        });
    }

    public function down(): void
    {
        Schema::table('stakeholders', function (Blueprint $table) {
            if (Schema::hasColumn('stakeholders', 'willingness_level_id')) {
                $table->dropConstrainedForeignId('willingness_level_id');
                // In case older Laravel drops FK separately:
                // $table->dropForeign(['willingness_level_id']);
                // $table->dropIndex(['willingness_level_id']);
                // $table->dropColumn('willingness_level_id');
            }
        });
    }
};

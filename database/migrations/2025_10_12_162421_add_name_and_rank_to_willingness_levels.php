<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('willingness_levels', function (Blueprint $table) {
            if (!Schema::hasColumn('willingness_levels', 'name')) {
                $table->string('name')->unique()->after('id');
            }
            if (!Schema::hasColumn('willingness_levels', 'rank')) {
                $table->unsignedTinyInteger('rank')->default(1)->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('willingness_levels', function (Blueprint $table) {
            if (Schema::hasColumn('willingness_levels', 'rank')) {
                $table->dropColumn('rank');
            }
            if (Schema::hasColumn('willingness_levels', 'name')) {
                $table->dropUnique(['name']);
                $table->dropColumn('name');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            if (!Schema::hasColumn('regions', 'name')) {
                $table->string('name')->unique()->after('id');
            }
            if (!Schema::hasColumn('regions', 'code')) {
                $table->string('code', 10)->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            if (Schema::hasColumn('regions', 'code')) $table->dropColumn('code');
            if (Schema::hasColumn('regions', 'name')) $table->dropUnique(['name']);
            if (Schema::hasColumn('regions', 'name')) $table->dropColumn('name');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('member_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('member_registrations','region_id')) {
                $table->foreignId('region_id')->nullable()->after('registration_year')
                      ->constrained('regions')->nullOnDelete()->cascadeOnUpdate();
                $table->index('region_id');
            }
            if (!Schema::hasColumn('member_registrations','district_id')) {
                $table->foreignId('district_id')->nullable()->after('region_id')
                      ->constrained('districts')->nullOnDelete()->cascadeOnUpdate();
                $table->index('district_id');
            }
            if (!Schema::hasColumn('member_registrations','constituency_id')) {
                $table->foreignId('constituency_id')->nullable()->after('district_id')
                      ->constrained('constituencies')->nullOnDelete()->cascadeOnUpdate();
                $table->index('constituency_id');
            }
            if (!Schema::hasColumn('member_registrations','zone_id')) {
                $table->foreignId('zone_id')->nullable()->after('constituency_id')
                      ->constrained('zones')->nullOnDelete()->cascadeOnUpdate();
                $table->index('zone_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('member_registrations', function (Blueprint $table) {
            // drop FKs then columns (handles partial existence safely)
            foreach (['zone_id','constituency_id','district_id','region_id'] as $col) {
                if (Schema::hasColumn('member_registrations', $col)) {
                    $table->dropConstrainedForeignId($col);
                    // $table->dropForeign([$col]); // for older setups
                    // $table->dropIndex([$col]);
                    // $table->dropColumn($col);
                }
            }
        });
    }
};

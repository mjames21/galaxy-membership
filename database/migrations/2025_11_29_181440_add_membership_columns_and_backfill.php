<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_registrations', function (Blueprint $table) {
            // add new fields if missing
            if (!Schema::hasColumn('member_registrations', 'registration_number')) {
                $table->string('registration_number', 60)->nullable()->after('region_id');
                $table->unique('registration_number', 'member_registrations_registration_number_unique');
            }

            if (!Schema::hasColumn('member_registrations', 'registration_year')) {
                $table->integer('registration_year')->nullable()->after('registration_number');
                $table->index('registration_year', 'member_registrations_registration_year_index');
            }

            if (!Schema::hasColumn('member_registrations', 'district_id')) {
                $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete()->after('region_id');
            }
            if (!Schema::hasColumn('member_registrations', 'constituency_id')) {
                $table->foreignId('constituency_id')->nullable()->constrained('constituencies')->nullOnDelete()->after('district_id');
            }
            if (!Schema::hasColumn('member_registrations', 'zone_id')) {
                $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete()->after('constituency_id');
            }
        });

        // Backfill registration_number from legacy member_no
        if (Schema::hasColumn('member_registrations', 'member_no')) {
            // null-safe copy; prefer SQL for speed
            // Works on Postgres & MySQL
            DB::statement("
                UPDATE member_registrations
                SET registration_number = COALESCE(registration_number, member_no)
            ");
        }

        // Backfill registration_year from joined_at (if available)
        if (Schema::hasColumn('member_registrations', 'joined_at')) {
            $driver = DB::getDriverName();
            if ($driver === 'pgsql') {
                DB::statement("
                    UPDATE member_registrations
                    SET registration_year = COALESCE(registration_year, EXTRACT(YEAR FROM joined_at)::int)
                    WHERE joined_at IS NOT NULL
                ");
            } else {
                // mysql/sqlite
                DB::statement("
                    UPDATE member_registrations
                    SET registration_year = COALESCE(registration_year, YEAR(joined_at))
                    WHERE joined_at IS NOT NULL
                ");
            }
        }

        // Drop old unique on member_no and the column itself
        Schema::table('member_registrations', function (Blueprint $table) {
            // drop legacy unique index if it exists
            // name from your migration: 'member_registrations_member_no_unique'
            try {
                $table->dropUnique('member_registrations_member_no_unique');
            } catch (\Throwable $e) {
                // ignore if it doesn't exist
            }

            if (Schema::hasColumn('member_registrations', 'member_no')) {
                $table->dropColumn('member_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('member_registrations', function (Blueprint $table) {
            // re-add legacy member_no
            if (!Schema::hasColumn('member_registrations', 'member_no')) {
                $table->string('member_no', 60)->nullable()->after('region_id');
                $table->unique('member_no', 'member_registrations_member_no_unique');
            }
        });

        // move data back from registration_number
        DB::statement("
            UPDATE member_registrations
            SET member_no = COALESCE(member_no, registration_number)
        ");

        Schema::table('member_registrations', function (Blueprint $table) {
            // drop new FKs/columns
            if (Schema::hasColumn('member_registrations', 'zone_id')) {
                $table->dropConstrainedForeignId('zone_id');
            }
            if (Schema::hasColumn('member_registrations', 'constituency_id')) {
                $table->dropConstrainedForeignId('constituency_id');
            }
            if (Schema::hasColumn('member_registrations', 'district_id')) {
                $table->dropConstrainedForeignId('district_id');
            }

            if (Schema::hasColumn('member_registrations', 'registration_year')) {
                $table->dropIndex('member_registrations_registration_year_index');
                $table->dropColumn('registration_year');
            }

            if (Schema::hasColumn('member_registrations', 'registration_number')) {
                $table->dropUnique('member_registrations_registration_number_unique');
                $table->dropColumn('registration_number');
            }
        });
    }
};

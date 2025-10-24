<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            if (!Schema::hasColumn('opportunities', 'prospect')) {
                // store as lowercase string: none|low|medium|high|certain
                $table->string('prospect', 16)->default('none')->index()->after('organization_id');
            }
        });

        // Optional: Postgres CHECK constraint to enforce allowed values
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                DO $$
                BEGIN
                    IF NOT EXISTS (
                        SELECT 1 FROM pg_constraint WHERE conname = 'opportunities_prospect_check'
                    ) THEN
                        ALTER TABLE opportunities
                        ADD CONSTRAINT opportunities_prospect_check
                        CHECK (prospect IN ('none','low','medium','high','certain'));
                    END IF;
                END$$;
            ");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE opportunities DROP CONSTRAINT IF EXISTS opportunities_prospect_check;");
        }
        Schema::table('opportunities', function (Blueprint $table) {
            if (Schema::hasColumn('opportunities', 'prospect')) {
                $table->dropIndex(['prospect']);
                $table->dropColumn('prospect');
            }
        });
    }
};

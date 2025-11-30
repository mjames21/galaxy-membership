<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();

            $table->string('name', 180);
            $table->string('code', 40)->nullable();

            // Type ( Council / Chapter / Youth Wing / Executive Council )
            $table->foreignId('organization_type_id')
                  ->constrained('organization_types')
                  ->cascadeOnDelete();

            // Optional hierarchy (e.g., a chapter under a council)
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('organizations')
                  ->nullOnDelete();

            // Optional quick contacts
            $table->string('contact_email', 190)->nullable();
            $table->string('contact_phone', 40)->nullable();

            $table->timestamps();

            $table->unique('name', 'org_name_unique');
            $table->unique('code', 'org_code_unique'); // Postgres allows multiple NULLs
            $table->index('organization_type_id', 'org_type_idx');
            $table->index('parent_id', 'org_parent_idx');
        });

        // ---- Seed the organizations here ----
        $now = now();

        // Helper to get type IDs by code (seeded in organization_types migration)
        $idByCode = function (string $code) {
            $id = DB::table('organization_types')->where('code', $code)->value('id');
            if (!$id) {
                throw new RuntimeException("organization_types.code '{$code}' not found. Ensure organization_types migration ran first.");
            }
            return $id;
        };

        $rows = [
            // Youth Wing
            ['name' => 'National Young Generation (NYG)', 'code'=> 'NYG-NAT', 'organization_type_id' => $idByCode('NYG')],
            // Councils
            ["name" => "Women's Council",                  'code'=> 'W-CNSL',  'organization_type_id' => $idByCode('COUNCIL')],
            ['name' => 'Student Council',                  'code'=> 'S-CNSL',  'organization_type_id' => $idByCode('COUNCIL')],
            // Chapters
            ['name' => 'UK & Ireland Chapter',             'code'=> 'UKI-CH',  'organization_type_id' => $idByCode('CHAPTER')],
            ['name' => 'North America Chapter',            'code'=> 'NA-CH',   'organization_type_id' => $idByCode('CHAPTER')],
            // Executive
            ['name' => 'National Executive Council',       'code'=> 'NEC',     'organization_type_id' => $idByCode('EXCO')],
        ];

        foreach ($rows as $r) {
            DB::table('organizations')->updateOrInsert(
                ['name' => $r['name']],
                $r + ['created_at'=>$now, 'updated_at'=>$now]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};

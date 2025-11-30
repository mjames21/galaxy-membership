<?php
// database/migrations/2025_11_30_000300_create_and_seed_zones_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();

            // Parent constituency (chiefdom/ward). Keep nullable to preserve history on parent delete.
            $table->foreignId('constituency_id')
                ->nullable()
                ->constrained('constituencies')
                ->nullOnDelete();

            $table->string('name', 120);        // "Zone 1", "Zone 2", ...
            $table->string('code', 20)->nullable(); // short, stable code
            $table->timestamps();

            // A zone name should not repeat within the same constituency
            $table->unique(['constituency_id','name'], 'zones_constituency_name_unique');
            $table->index('constituency_id', 'zones_constituency_id_index');
            $table->unique(['code'], 'zones_code_unique');
        });

        // ----- Seed: create 5 zones per constituency -----
        $now = now();

        // Preload district codes keyed by district_id for code generation
        $districtCodes = DB::table('districts')->pluck('code', 'id'); // [district_id => 'KAI'|'WAR'|'WAU'...]
        $consts = DB::table('constituencies')->select('id','district_id','name','code')->get();

        $toInsert = [];
        $usedCodes = []; // batch-level uniqueness guard

        $makeCode = function (?string $districtCode, ?string $constCode, int $z) use (&$usedCodes): string {
            // Prefer provided short codes; fall back to tiny slug if missing
            $d = strtoupper($districtCode ?: 'XX');
            $c = strtoupper($constCode ?: 'CST');

            $base = Str::limit("{$d}_{$c}_Z{$z}", 20, ''); // fit VARCHAR(20)
            $code = $base; $i = 2;
            while (isset($usedCodes[$code])) {
                $suffix = "_{$i}";
                $code = Str::limit($base, 20 - strlen($suffix), '') . $suffix;
                $i++;
            }
            $usedCodes[$code] = true;
            return $code;
        };

        foreach ($consts as $c) {
            $districtCode = $districtCodes[$c->district_id] ?? 'XX';
            $constCode    = $c->code ?: Str::upper(Str::slug($c->name, '_'));
            for ($z = 1; $z <= 5; $z++) {
                $toInsert[] = [
                    'constituency_id' => $c->id,
                    'name'            => "Zone {$z}",
                    'code'            => $makeCode($districtCode, $constCode, $z),
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }
        }

        if ($toInsert) {
            DB::table('zones')->insert($toInsert);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};

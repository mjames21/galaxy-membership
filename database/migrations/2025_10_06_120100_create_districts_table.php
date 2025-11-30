<?php
// database/migrations/2025_11_30_000100_create_districts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('region_id')
                ->constrained('regions')
                ->cascadeOnUpdate()
                ->restrictOnDelete();              // keep referential integrity

            $table->string('name', 160);
            $table->string('code', 10);           // alpha code: KAI, KEN, KON, ...

            $table->timestamps();

            $table->unique(['region_id','name'], 'districts_region_name_unique');
            $table->unique('code', 'districts_code_unique');
            $table->index('region_id', 'districts_region_id_index');
        });

        // --- seed districts (lookup region by its short code) ---
        $regions = DB::table('regions')->pluck('id','code'); // ['SR'=>id, ...]

        $now = now();
        $data = [];

        // Eastern (ER)
        foreach ([['Kailahun','KAI'], ['Kenema','KEN'], ['Kono','KON']] as [$n,$c]) {
            $data[] = ['region_id'=>$regions['ER'] ?? null,'name'=>$n,'code'=>$c,'created_at'=>$now,'updated_at'=>$now];
        }
        // Northern (NR)
        foreach ([['Bombali','BOM'], ['Falaba','FAL'], ['Koinadugu','KOI'], ['Tonkolili','TON']] as [$n,$c]) {
            $data[] = ['region_id'=>$regions['NR'] ?? null,'name'=>$n,'code'=>$c,'created_at'=>$now,'updated_at'=>$now];
        }
        // North West (NW)
        foreach ([['Karene','KAR'], ['Kambia','KAM'], ['Port Loko','PLD']] as [$n,$c]) {
            $data[] = ['region_id'=>$regions['NW'] ?? null,'name'=>$n,'code'=>$c,'created_at'=>$now,'updated_at'=>$now];
        }
        // Southern (SR)
        foreach ([['Bo','BOD'], ['Bonthe','BON'], ['Moyamba','MOY'], ['Pujehun','PUJ']] as [$n,$c]) {
            $data[] = ['region_id'=>$regions['SR'] ?? null,'name'=>$n,'code'=>$c,'created_at'=>$now,'updated_at'=>$now];
        }
        // Western Area (WA)
        foreach ([['Western Area Rural','WAR'], ['Western Area Urban','WAU']] as [$n,$c]) {
            $data[] = ['region_id'=>$regions['WA'] ?? null,'name'=>$n,'code'=>$c,'created_at'=>$now,'updated_at'=>$now];
        }

        // Insert in one go (fresh DB)
        DB::table('districts')->insert($data);
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};

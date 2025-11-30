<?php
// database/migrations/2025_11_30_000000_create_regions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('code', 10);            // short region code: SR, ER, NW, NR, WA
            $table->timestamps();

            $table->unique('name', 'regions_name_unique');
            $table->unique('code', 'regions_code_unique');
        });

        // seed regions (idempotent for fresh DB)
        $now = now();
        $rows = [
            ['name'=>'Southern',     'code'=>'SR', 'created_at'=>$now, 'updated_at'=>$now],
            ['name'=>'Eastern',      'code'=>'ER', 'created_at'=>$now, 'updated_at'=>$now],
            ['name'=>'North West',   'code'=>'NW', 'created_at'=>$now, 'updated_at'=>$now],
            ['name'=>'Northern',     'code'=>'NR', 'created_at'=>$now, 'updated_at'=>$now],
            ['name'=>'Western Area', 'code'=>'WA', 'created_at'=>$now, 'updated_at'=>$now],
        ];
        DB::table('regions')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('membership_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('code', 40)->nullable();   // short code for internal use
            $table->timestamps();

            $table->unique('name', 'membership_types_name_unique');
            $table->unique('code', 'membership_types_code_unique');
        });

        $now = now();
        DB::table('membership_types')->insert([
            ['name'=>'Ordinary',           'code'=>'ORD',   'created_at'=>$now, 'updated_at'=>$now],
            ['name'=>'Patron',             'code'=>'PATR',  'created_at'=>$now, 'updated_at'=>$now],
            ['name'=>'Chief Patron',       'code'=>'CPATR', 'created_at'=>$now, 'updated_at'=>$now],
            ['name'=>'Grand Chief Patron', 'code'=>'GCP',   'created_at'=>$now, 'updated_at'=>$now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_types');
    }
};

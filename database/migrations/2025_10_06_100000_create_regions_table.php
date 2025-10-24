<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->timestamps();
            $table->unique('name');
        });

        DB::table('regions')->insert([
            ['name'=>'Western Area','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Northern','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Southern','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Eastern','created_at'=>now(),'updated_at'=>now()],
        ]);
    }
    public function down(): void { Schema::dropIfExists('regions'); }
};

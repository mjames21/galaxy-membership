<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('influence_levels', function (Blueprint $table) {
            $table->id();
            $table->string('label', 60);
            $table->timestamps();
            $table->unique('label');
        });

        // Optional seed
        DB::table('influence_levels')->insert([
            ['label'=>'Very High','created_at'=>now(),'updated_at'=>now()],
            ['label'=>'High','created_at'=>now(),'updated_at'=>now()],
            ['label'=>'Medium','created_at'=>now(),'updated_at'=>now()],
            ['label'=>'Low','created_at'=>now(),'updated_at'=>now()],
            ['label'=>'Very Low','created_at'=>now(),'updated_at'=>now()],
        ]);
    }

    public function down(): void {
        Schema::dropIfExists('influence_levels');
    }
};

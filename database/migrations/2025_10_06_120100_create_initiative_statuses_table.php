<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('initiative_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('label', 80);
            $table->timestamps();
            $table->unique('label');
        });

        DB::table('initiative_statuses')->insert([
            ['label'=>'Planned','created_at'=>now(),'updated_at'=>now()],
            ['label'=>'In Progress','created_at'=>now(),'updated_at'=>now()],
            ['label'=>'On Hold','created_at'=>now(),'updated_at'=>now()],
            ['label'=>'Completed','created_at'=>now(),'updated_at'=>now()],
            ['label'=>'Cancelled','created_at'=>now(),'updated_at'=>now()],
        ]);
    }
    public function down(): void { Schema::dropIfExists('initiative_statuses'); }
};

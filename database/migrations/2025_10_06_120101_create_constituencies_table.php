<?php
// database/migrations/2025_11_30_000200_create_and_seed_constituencies_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('constituencies', function (Blueprint $table) {
            $table->id();

            // Parent district (nullable to preserve history if parent is deleted)
            $table->foreignId('district_id')
                ->nullable()
                ->constrained('districts')
                ->nullOnDelete();

            $table->string('name', 160);
            $table->string('code', 20)->nullable(); // short stable code
            $table->timestamps();

            $table->unique(['district_id','name'], 'constituencies_district_name_unique');
            $table->index('district_id');
            $table->unique(['code'], 'constituencies_code_unique');
        });

        // ---- Seed chiefdoms/wards ----
        // district_code => [ names ... ]
        $map = [

            // ============== EASTERN ==============
            'KAI' => [ // Kailahun
                'Dea','Njaluahun','Jawie','Kissi Kama','Kissi Teng','Kissi Tongi','Luawa',
                'Malema','Mandu','Kpeje Bongre','Kpeje West','Penguia','Upper Bambara','Yawei',
            ],
            'KEN' => [ // Kenema
                'Dama','Dodo','Gaura','Gorama Mende','Kandu Leppiama','Koya-Kenema',
                'Langrama','Lower Bambara','Malegohun','Niawa','Nomo','Nongowa',
                'Simbaru','Small Bo','Tunkia','Wandor',
            ],
            'KON' => [ // Kono
                'Fiama','Gbane','Gbane Kandor','Gbense','Gorama Kono','Kamara','Lei',
                'Mafindor','Nimikoro','Nimiyama','Sandor','Soa','Tankoro','Toli',
            ],

            // ============== NORTHERN ==============
            'BOM' => [
                'Biriwa','Bombali Sebora','Bombali Siari','Gbanti','Gbendembu','Kamaranka',
                'Magbaimba Ndgowahun','Makari','Mara','Ngowahun','Paki Masabong','Safroko Limba',
                'Wara Wara Bafodia','Wara Wara Yagala',
            ],
            'FAL' => [
                'Delemandugu','Dembelia','Dembelia-Sinkunia','Folosaba','Kamadu Yiraia',
                'Kasunko Kakellian','Kebelia','Kulor Saradu','Mongo','Morifindugu',
                'Neya','Nyedu','Sulima','Wollay Barawa',
            ],
            'KOI' => [
                'Diang','Gbonkobon Kayaka','Kalian','Kamukeh','Kasunko','Mongo',
                'Nieni','Sengbe','Tamiso','Wara Wara Bafodia','Wara Wara Yagala',
            ],
            'TON' => [
                'Dansogoia','Gbonkolenken Masankong','Kafe','Kalanthuba','Kholifa Mabang',
                'Kholifa Mamuntha Mayosso','Kholifa Rowala','Kunike Barina','Kunike Folawusu',
                'Kunike Sanda','Malal','Mayeppoh','Poli','Sambaia','Simiria','Tane',
                'Yele','Yoni Mabanta','Yoni Mamaila',
            ],

            // ============== NORTH WEST ==============
            'KAR' => [
                'Buya','Dibia','Gbanti','Libeisaygahun Gbombahun','Makama','Romende',
                'Safroko','Sanda Loko','Sanda Magbolontor','Tambakha Simibungie','Tambakha Yobangie',
            ],
            'KAM' => [
                'Bramaia','Dixon','Gbinle','Khonimaka','Magbema','Mambolo','Masungbala',
                'Muna Thalla','Samu','Tonko Limba',
            ],
            'PLD' => [
                'Bureh','Kaffu Bullom','Kamasondo','Kasseh','Koya','Lokomasama',
                'Maconteh','Maforki','Marampa','Masimera','Thainkatopa','Port Loko City',
            ],

            // ============== SOUTHERN ==============
            'BOD' => [
                'Badjia','Bagbo','Bagbwe (Bagbe)','Baoma','Bongor','Bumpe Ngao','Gbo','Jaiama',
                'Kakua','Komboya','Lugbu','Niawa Lenga','Selenga','Tikonko','Valunia','Wunde','Bo City',
            ],
            'BON' => [
                'Bendu-Cha','Bum','Dema','Imperri','Jong','Kpanda Kemo','Kwamebai Krim',
                'Nongoba Bullom','Sittia','Sogbeni','Yawbeko',
            ],
            'MOY' => [
                'Bagruwa','Bumpeh','Dasse','Fakunya','Gbangbaya','Kaiyamba','Kagboro','Kori',
                'Kowa','Lower Banta','Mono','Ribbi','Timdale','Upper Banta',
            ],
            'PUJ' => [
                'Barri','Gallinas','Kabonde','Kpaka','Kpanga Krim','Malen','Makpele',
                'Mano Sakrim','Panga','Panga Krim','Soro Gbema','Yakemu Kpukumu',
            ],

            // ============== WESTERN (URBAN WARDS) ==============
            'WAU' => [ // Western Area Urban — ward groups
                'East 1','East 2','Central 1','Central 2','Central 3','West 1','West 2','West 3',
            ],

            // ============== WESTERN (RURAL — villages/towns) ==============
            'WAR' => [
                // Replaced generic groups with villages/towns as requested
                'Rokel','Jui','Kossoh Town','Tokeh','Sussex','Number 2','Lakka',
                'Adonkia','Regent','Bathurst','Tombo','Kent','York','Goderich','Waterloo',
            ],
        ];

        // district_code -> id
        $districtIds = DB::table('districts')->pluck('id', 'code'); // ['KAI'=>1, ..., 'WAU'=>?, 'WAR'=>?]

        $now = now();
        $toInsert = [];
        $usedCodes = []; // prevent duplicate codes inside this batch

        $makeCode = function (string $dCode, string $name) use (&$usedCodes): string {
            // Codes: WAR_KOSSOH_TOWN, WAR_NUMBER_2, etc. Max 20 chars.
            $base = strtoupper($dCode.'_'.Str::slug($name, '_'));
            $base = Str::limit($base, 20, '');
            $code = $base; $i = 2;
            while (isset($usedCodes[$code])) {
                $suffix = '_'.$i;
                $code = Str::limit($base, 20 - strlen($suffix), '').$suffix;
                $i++;
            }
            $usedCodes[$code] = true;
            return $code;
        };

        foreach ($map as $dCode => $names) {
            $districtId = $districtIds[$dCode] ?? null;
            foreach ($names as $name) {
                $toInsert[] = [
                    'district_id' => $districtId,
                    'name'        => $name,
                    'code'        => $makeCode($dCode, $name),
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }

        if ($toInsert) {
            DB::table('constituencies')->insert($toInsert);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('constituencies');
    }
};

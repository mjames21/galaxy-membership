<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{District, Constituency};
use Illuminate\Support\Str;

/**
 * Seeds chiefdoms (stored in the Constituencies table).
 * This file includes FULL chiefdom lists for EASTERN region.
 * Add more districts by appending to $chiefdoms[...] with the real names.
 *
 * Reference for names: see "Chiefdoms of Sierra Leone". :contentReference[oaicite:1]{index=1}
 */
class ChiefdomsSeeder extends Seeder
{
    public function run(): void
    {
        // --- Helper closure: upsert chiefdom by parent district code + name ---
        $upsert = function (string $districtCode, array $names): void {
            $district = District::firstWhere('code', $districtCode);
            if (!$district) return;
            foreach ($names as $name) {
                Constituency::updateOrCreate(
                    ['code' => strtoupper($districtCode).'_'.Str::of($name)->upper()->slug('_')],
                    ['name' => $name, 'district_id' => $district->id]
                );
            }
        };

        // ===================== EASTERN REGION =====================
        // Kailahun
        $upsert('KAILAHUN', [
            'Dea',
            'Jahn',          // (Jaluahun de-amalgamation; post-2017)
            'Jaluahun',
            'Jawei',
            'Kissi Kama',
            'Kissi Teng',
            'Kissi Tongi',
            'Luawa',
            'Malema',
            'Mandu',
            'Peje Bongre',
            'Peje West',
            'Penguia',
            'Upper Bambara',
            'Yawei',
        ]);

        // Kenema
        $upsert('KENEMA', [
            'Dama',
            'Dodo',
            'Gaura',
            'Gorama Mende',
            'Kandu Leppiam',
            'Koya',
            'Langurama Ya',
            'Lower Bambara',
            'Malehgohun',
            'Niawa',
            'Nomo',
            'Nongowa',
            'Simbaru',
            'Small Bo',
            'Tunkia',
            'Wandor',
        ]);

        // Kono
        $upsert('KONO', [
            'Fiama',
            'Gbane',
            'Gbane Kandor',
            'Gbense',
            'Gorama Kono',
            'Kamara',
            'Lei',
            'Mafindor',
            'Nimikoro',
            'Nimiyama',
            'Sandor',
            'Soa',
            'Tankoro',
            'Toli',
        ]);

        // ===================== ADD THE REST LATER =====================
        // Example stubs (uncomment and fill with official names when ready):
      
        // Northern — Bombali
        $upsert('BOMBALI', [
            'Biriwa','Bombali Shebora','Gbanti Kamaranka','Gbendembu Ngowahun','Libeisaygahun',
            'Magbaiamba Ndowahun','Makari Gbanti','Paki Massabong','Safroko Limba',
            'Sanda Loko','Sanda Tenraren','Sella Limba','Tambakha',
            // Post-2017 additions/changes (split chiefdoms) — verify names on the reference page
            // 'Bombali Siari','Gbanti','Gbendembu','Kamaranka','Makari','Mara','Ngowahun',
        ]);

        // Northern — Koinadugu
        $upsert('KOINADUGU', [
            'Diang','Gbonkobon Kayaka','Kalian','Kamukeh','Kasunko KaKellian',
            'Nieni','Sengbe','Tamiso','Wara-Wara Bafodea','Wara-Wara Yagala',
        ]);

        // Northern — Tonkolili
        $upsert('TONKOLILI', [
            'Gbonkolenken','Kafe Simiria','Kalanthuba','Dansogoia','Kholifa Mabang',
            'Kholifa Rowalla','Kunike','Kunike Barina','Malal Mara','Sambaia','Tane','Yoni',
        ]);

        // North West — Kambia
        $upsert('KAMBIA', [
            'Briama','Dixing','Gbinle','Khonimakha','Magbema','Mambolo','Masungbala',
            'Munu Thala','Samu','Tonko Limba',
        ]);

        // North West — Karene
        $upsert('KARENE', [
            'Buya','Debia','Gbanti','Libeisaygahun-Gbombahun','Sella Limba', // verify final set
        ]);

        // North West — Port Loko
        $upsert('PORT_LOKO', [
            'Bureh Kasseh Maconteh','Buya Romende','Dibia','Kaffu Bullom','Koya',
            'Lokomasama','Maforki','Marampa','Masimera','Tinkatupa Makama Safroko',
        ]);

        // Southern — Bo
        $upsert('BO', [
            'Badjia','Bagbo','Bagbwe','Baoma','Bumpe-Gao','Gbo','Jaiama Bongor',
            'Kakua','Komboya','Lugbu','Niawa Lenga','Selenga','Tikonko','Valunia','Wonde',
            // Post-2017: 'Bongor','Jaiama','Bo City'
        ]);

        // Southern — Bonthe
        $upsert('BONTHE', [
            'Bendu','Bum','Dema','Imperri','Jong','Kpanda Kemoh','Kwamebai Krim',
            'Nongoba Bullom','Sittia','Sogbeni','Yawbeko',
        ]);

        // Southern — Moyamba
        $upsert('MOYAMBA', [
            'Bagruwa','Bumpeh','Dasse','Fakunya','Gbangbaya','Kaiyamba','Kori','Kowa',
            'Lower Banta','Mono','Ribbi','Timidale','Upper Banta','Yawbeko',
        ]);

        // Southern — Pujehun
        $upsert('PUJEHUN', [
            'Barri','Gallinas','Gallinas Perri','Kpanga Kabonde','Kpanga Krim','Malen',
            'Makpele','Panga Krim','Pejeh','Sowa','Soro Gbema','Yekomo Kpukumu Krim',
        ]);
        
        'WESTERN_AREA_URBAN' => [
        'East 1', 'East 2', 'Central 1', 'Central 2', 'Central 3', 'West 1', 'West 2', 'West 3',
    ],
    'WESTERN_AREA_RURAL' => [
        'York', 'Mountain Rural', 'Waterloo', 'Goderich','Tombo', 'Kent', 'Tokeh', 'Adonkia', 'Sussex', 'Bureh',
    ],
    }
}

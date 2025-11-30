<?php

namespace App\Livewire\Locations;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\{Region, District, Constituency, Zone};
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class Import extends Component
{
    use WithFileUploads;

    #[Validate('required|in:regions,districts,constituencies,zones')]
    public string $type = 'regions';

    #[Validate('required|file|mimes:csv,txt|max:2048')]
    public $file;

    public array $preview = []; // first 10 rows

    public function updatedFile(): void
    {
        $this->reset('preview');
        $this->validateOnly('file');
        $rows = $this->readCsv($this->file->getRealPath(), 10);
        $this->preview = $rows;
    }

    public function import(): void
    {
        $this->validate();

        $rows = $this->readCsv($this->file->getRealPath());
        if (!$rows) { $this->addError('file','CSV empty'); return; }

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Expected headers per type
                switch ($this->type) {
                    case 'regions':
                        // code,name
                        Region::updateOrCreate(
                            ['code' => $row['code']],
                            ['name' => $row['name']]
                        );
                        break;

                    case 'districts':
                        // parent_code,code,name
                        $region = Region::firstWhere('code', $row['parent_code']);
                        if (!$region) continue;
                        District::updateOrCreate(
                            ['code' => $row['code']],
                            ['name' => $row['name'], 'region_id' => $region->id]
                        );
                        break;

                    case 'constituencies':
                        // parent_code,code,name  (parent = district code)  NOTE: "constituencies" = chiefdoms
                        $district = District::firstWhere('code', $row['parent_code']);
                        if (!$district) continue;
                        Constituency::updateOrCreate(
                            ['code' => $row['code']],
                            ['name' => $row['name'], 'district_id' => $district->id]
                        );
                        break;

                    case 'zones':
                        // parent_code,code,name (parent = constituency code)
                        $const = Constituency::firstWhere('code', $row['parent_code']);
                        if (!$const) continue;
                        Zone::updateOrCreate(
                            ['code' => $row['code']],
                            ['name' => $row['name'], 'constituency_id' => $const->id]
                        );
                        break;
                }
            }
        });

        session()->flash('ok', 'Import complete.');
        $this->reset(['file','preview']);
    }

    private function readCsv(string $path, int $limit = 0): array
    {
        $fh = fopen($path, 'r');
        if (!$fh) return [];
        $header = null; $rows = []; $count = 0;
        while (($data = fgetcsv($fh)) !== false) {
            if ($header === null) { $header = array_map('trim', $data); continue; }
            $row = [];
            foreach ($header as $i => $col) { $row[$col] = $data[$i] ?? null; }
            $rows[] = $row; $count++;
            if ($limit && $count >= $limit) break;
        }
        fclose($fh);
        return $rows;
    }

    public function render()
    {
        return view('livewire.locations.import')
            ->title('Locations Import');
    }
}

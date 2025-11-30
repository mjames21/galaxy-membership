<?php
// file: app/Livewire/Locations/Manage.php

namespace App\Livewire\Locations;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\{Region, District, Constituency, Zone};

#[Layout('layouts.app')]
class Manage extends Component
{
    use WithPagination, WithFileUploads;

    #[Url] public string $tab = 'regions'; // regions|districts|constituencies|zones
    #[Url] public ?string $q = null;

    public ?int $editingId = null;

    // Parent filters (authoritative)
    public ?int $region_id = null;
    public ?int $district_id = null;
    public ?int $constituency_id = null;

    // Form fields
    #[Validate('required|string|max:160')] public ?string $name = null;
    #[Validate('nullable|string|max:20')]  public ?string $code = null;

    // Auto-code control
    public bool $autoCode = true; // why: keep generating code from name until user edits code

    // Dropdown lists bound to filters
    public array $regions = [];
    public array $districts = [];
    public array $constituencies = [];

    // CSV upload
    #[Validate('nullable|file|mimes:csv,txt|max:5120')]
    public $csvFile = null;

    public function mount(): void
    {
        $this->loadParents();
    }

    /* ---------- Filters cascade ---------- */
    public function updatedTab(): void
    {
        $this->resetPage();
        $this->resetForm();
        $this->loadParents();
    }

    public function updatedRegionId(): void
    {
        $this->district_id = $this->constituency_id = null;
        $this->loadDistricts();
        $this->constituencies = [];
        $this->resetPage();
    }

    public function updatedDistrictId(): void
    {
        $this->constituency_id = null;
        $this->loadConstituencies();
        $this->resetPage();
    }

    public function clearRegion(): void
    {
        $this->region_id = $this->district_id = $this->constituency_id = null;
        $this->loadDistricts();
        $this->constituencies = [];
        $this->resetPage();
    }

    public function clearDistrict(): void
    {
        $this->district_id = $this->constituency_id = null;
        $this->loadConstituencies();
        $this->resetPage();
    }

    public function clearConstituency(): void
    {
        $this->constituency_id = null;
        $this->resetPage();
    }

    /* ---------- Form helpers ---------- */
    public function createNew(): void
    {
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $this->resetForm();
        $this->editingId = $id;

        switch ($this->tab) {
            case 'regions':
                $m = Region::findOrFail($id);
                $this->name = $m->name; $this->code = $m->code; $this->autoCode = false;
                break;

            case 'districts':
                $m = District::findOrFail($id);
                $this->name = $m->name; $this->code = $m->code; $this->autoCode = false;
                $this->region_id = $m->region_id; $this->loadDistricts();
                break;

            case 'constituencies':
                $m = Constituency::with('district')->findOrFail($id);
                $this->name = $m->name; $this->code = $m->code; $this->autoCode = false;
                $this->region_id = optional($m->district)->region_id; $this->loadDistricts();
                $this->district_id = $m->district_id; $this->loadConstituencies();
                break;

            case 'zones':
                $m = Zone::with('constituency.district')->findOrFail($id);
                $this->name = $m->name; $this->code = $m->code; $this->autoCode = false;
                $this->region_id = optional(optional($m->constituency)->district)->region_id; $this->loadDistricts();
                $this->district_id = optional($m->constituency)->district_id; $this->loadConstituencies();
                $this->constituency_id = $m->constituency_id;
                break;
        }
    }

    public function updatedName(): void
    {
        if ($this->autoCode) {
            [$table, $max] = $this->codeMeta();
            $this->code = $this->dedupeCode($table, $this->makeCode($this->name, $max), $max, $this->editingId);
        }
    }

    public function updatedCode(): void
    {
        // user touched code; stop auto-sync
        $this->autoCode = false;
    }

    public function save(): void
    {
        // parents required by tab
        if ($this->tab === 'districts' && !$this->region_id) {
            $this->addError('region_id', 'Select a Region in the filters above.'); return;
        }
        if ($this->tab === 'constituencies' && !$this->district_id) {
            $this->addError('district_id', 'Select a District in the filters above.'); return;
        }
        if ($this->tab === 'zones' && !$this->constituency_id) {
            $this->addError('constituency_id', 'Select a Constituency in the filters above.'); return;
        }

        [$table, $codeMax] = $this->codeMeta();

        $uniqueCode = Rule::unique($table, 'code');
        if ($this->editingId) $uniqueCode = $uniqueCode->ignore($this->editingId);

        $this->validate([
            'name' => ['required','string','max:160'],
            'code' => ['nullable','string',"max:{$codeMax}", $uniqueCode],
        ]);

        $code = $this->code ?: $this->makeCode($this->name, $codeMax);
        $code = $this->dedupeCode($table, $code, $codeMax, $this->editingId);

        switch ($this->tab) {
            case 'regions':
                $data = ['name'=>$this->name,'code'=>$code];
                $this->editingId ? Region::findOrFail($this->editingId)->update($data) : Region::create($data);
                break;

            case 'districts':
                $data = ['region_id'=>$this->region_id,'name'=>$this->name,'code'=>$code];
                $this->editingId ? District::findOrFail($this->editingId)->update($data) : District::create($data);
                break;

            case 'constituencies':
                $data = ['district_id'=>$this->district_id,'name'=>$this->name,'code'=>$code];
                $this->editingId ? Constituency::findOrFail($this->editingId)->update($data) : Constituency::create($data);
                break;

            case 'zones':
                $data = ['constituency_id'=>$this->constituency_id,'name'=>$this->name,'code'=>$code];
                $this->editingId ? Zone::findOrFail($this->editingId)->update($data) : Zone::create($data);
                break;
        }

        session()->flash('ok','Saved.');
        $this->resetForm();
        $this->loadParents();
    }

    public function delete(int $id): void
    {
        match ($this->tab) {
            'regions'        => Region::findOrFail($id)->delete(),
            'districts'      => District::findOrFail($id)->delete(),
            'constituencies' => Constituency::findOrFail($id)->delete(),
            'zones'          => Zone::findOrFail($id)->delete(),
            default          => null,
        };
        session()->flash('ok','Deleted.');
        $this->resetForm();
        $this->loadParents();
    }

    /* ---------- Data ---------- */
    public function getRowsProperty()
    {
        $like = '%'.trim((string)$this->q).'%';

        return match ($this->tab) {
            'regions' => Region::when($this->q, fn($q)=>$q->where(fn($qq)=>$qq->where('name','like',$like)->orWhere('code','like',$like)))
                               ->orderBy('name')->paginate(12),

            'districts' => District::with('region')
                               ->when($this->region_id, fn($q)=>$q->where('region_id',$this->region_id))
                               ->when($this->q, fn($q)=>$q->where(fn($qq)=>$qq->where('name','like',$like)->orWhere('code','like',$like)))
                               ->orderBy('name')->paginate(12),

            'constituencies' => Constituency::with('district.region')
                               ->when($this->district_id, fn($q)=>$q->where('district_id',$this->district_id))
                               ->when(!$this->district_id && $this->region_id, function ($q) {
                                   $q->whereIn('district_id', District::where('region_id',$this->region_id)->pluck('id'));
                               })
                               ->when($this->q, fn($q)=>$q->where(fn($qq)=>$qq->where('name','like',$like)->orWhere('code','like',$like)))
                               ->orderBy('name')->paginate(12),

            'zones' => Zone::with('constituency.district.region')
                               ->when($this->constituency_id, fn($q)=>$q->where('constituency_id',$this->constituency_id))
                               ->when(!$this->constituency_id && $this->district_id, function ($q) {
                                   $q->whereIn('constituency_id', Constituency::where('district_id',$this->district_id)->pluck('id'));
                               })
                               ->when(!$this->constituency_id && !$this->district_id && $this->region_id, function ($q) {
                                   $districtIds = District::where('region_id',$this->region_id)->pluck('id');
                                   $q->whereIn('constituency_id', Constituency::whereIn('district_id',$districtIds)->pluck('id'));
                               })
                               ->when($this->q, fn($q)=>$q->where(fn($qq)=>$qq->where('name','like',$like)->orWhere('code','like',$like)))
                               ->orderBy('name')->paginate(12),

            default => Region::orderBy('name')->paginate(12),
        };
    }

    /* ---------- CSV ---------- */
    public function exportCsv(): StreamedResponse
    {
        $filename = $this->tab.'-'.now()->format('Ymd_His').'.csv';
        $headers = [
            'regions'        => ['code','name'],
            'districts'      => ['code','name','region_code'],
            'constituencies' => ['code','name','district_code'],
            'zones'          => ['code','name','constituency_code'],
        ][$this->tab];

        return response()->streamDownload(function () use ($headers) {
            $out = fopen('php://output','w'); fputcsv($out, $headers);

            switch ($this->tab) {
                case 'regions':
                    $this->rows->getCollection()->each(fn($r)=>fputcsv($out, [$r->code, $r->name])); break;
                case 'districts':
                    $this->rows->load('region')->getCollection()->each(fn($d)=>fputcsv($out, [$d->code, $d->name, optional($d->region)->code])); break;
                case 'constituencies':
                    $this->rows->load('district')->getCollection()->each(fn($c)=>fputcsv($out, [$c->code, $c->name, optional($c->district)->code])); break;
                case 'zones':
                    $this->rows->load('constituency')->getCollection()->each(fn($z)=>fputcsv($out, [$z->code, $z->name, optional($z->constituency)->code])); break;
            }
            fclose($out);
        }, $filename, ['Content-Type'=>'text/csv']);
    }

    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'regions'        => ['code','name'],
            'districts'      => ['code','name','region_code'],
            'constituencies' => ['code','name','district_code'],
            'zones'          => ['code','name','constituency_code'],
        ][$this->tab];

        $filename = $this->tab.'-template.csv';
        return response()->streamDownload(function () use ($headers) {
            $out = fopen('php://output','w'); fputcsv($out, $headers); fclose($out);
        }, $filename, ['Content-Type'=>'text/csv']);
    }

    public function importCsv(): void
    {
        $this->validateOnly('csvFile'); if (!$this->csvFile) return;

        $fh = fopen($this->csvFile->getRealPath(), 'r'); if (!$fh) { session()->flash('ok','Could not open file.'); return; }
        $header = fgetcsv($fh) ?: []; $norm = fn($v)=>strtolower(trim((string)$v)); $idx = array_flip(array_map($norm,$header));

        $require = match ($this->tab) {
            'regions'        => ['code','name'],
            'districts'      => ['code','name','region_code'],
            'constituencies' => ['code','name','district_code'],
            'zones'          => ['code','name','constituency_code'],
        };
        foreach ($require as $col) if (!array_key_exists($col,$idx)) { fclose($fh); $this->addError('csvFile',"Missing column: {$col}"); return; }

        $count = 0;
        while (($row = fgetcsv($fh)) !== false) {
            $get = fn($col) => $row[$idx[$col]] ?? null;

            switch ($this->tab) {
                case 'regions':
                    $code = strtoupper(trim((string)$get('code'))); $name = trim((string)$get('name'));
                    if (!$code || !$name) continue;
                    Region::updateOrCreate(['code'=>$code], ['name'=>$name]); $count++; break;

                case 'districts':
                    $code = strtoupper(trim((string)$get('code'))); $name = trim((string)$get('name'));
                    $region = Region::firstWhere('code', strtoupper(trim((string)$get('region_code'))));
                    if (!$code || !$name || !$region) continue;
                    District::updateOrCreate(['code'=>$code], ['name'=>$name,'region_id'=>$region->id]); $count++; break;

                case 'constituencies':
                    $code = strtoupper(trim((string)$get('code'))); $name = trim((string)$get('name'));
                    $district = District::firstWhere('code', strtoupper(trim((string)$get('district_code'))));
                    if (!$code || !$name || !$district) continue;
                    Constituency::updateOrCreate(['code'=>$code], ['name'=>$name,'district_id'=>$district->id]); $count++; break;

                case 'zones':
                    $code = strtoupper(trim((string)$get('code'))); $name = trim((string)$get('name'));
                    $const = Constituency::firstWhere('code', strtoupper(trim((string)$get('constituency_code'))));
                    if (!$code || !$name || !$const) continue;
                    Zone::updateOrCreate(['code'=>$code], ['name'=>$name,'constituency_id'=>$const->id]); $count++; break;
            }
        }
        fclose($fh);

        $this->csvFile = null;
        session()->flash('ok', "Imported {$count} row(s).");
        $this->resetPage(); $this->loadParents();
    }

    /* ---------- Zones helper ---------- */
    public function regenerateZones(bool $replace = true): void
    {
        if ($this->tab !== 'zones') return;
        if (!$this->constituency_id) { $this->addError('constituency_id','Select a constituency first.'); return; }

        $const = Constituency::with('district')->find($this->constituency_id);
        if (!$const) { $this->addError('constituency_id','Invalid constituency.'); return; }

        $districtCode = optional($const->district)->code ?: 'XX';
        $constCode    = $const->code ?: Str::upper(Str::slug($const->name, '_'));

        $kept = [];
        for ($i=1; $i<=5; $i++) {
            $name = "Zone {$i}";
            $code = $this->buildZoneCode($districtCode, $constCode, $i, 20);
            $zone = Zone::updateOrCreate(
                ['constituency_id'=>$const->id, 'name'=>$name],
                ['code'=>$code]
            );
            $kept[] = $zone->id;
        }
        if ($replace) {
            Zone::where('constituency_id',$const->id)->whereNotIn('id',$kept)->delete();
        }

        session()->flash('ok','Zones 1–5 regenerated.');
        $this->resetPage();
    }

    /* ---------- Utilities ---------- */
    private function codeMeta(): array
    {
        return match ($this->tab) {
            'regions'        => ['regions', 10],
            'districts'      => ['districts', 10],
            'constituencies' => ['constituencies', 20],
            'zones'          => ['zones', 20],
            default          => ['regions', 10],
        };
    }

    private function buildZoneCode(string $districtCode, string $constCode, int $z, int $maxLen = 20): string
    {
        $base = Str::limit(strtoupper("{$districtCode}_{$constCode}_Z{$z}"), $maxLen, '');
        $final = $base; $i = 2;
        while (Zone::where('code', $final)->exists()) {
            $suffix = "_{$i}";
            $final = Str::limit($base, $maxLen - strlen($suffix), '') . $suffix;
            $i++;
        }
        return $final;
    }

    public function render()
    {
        $rows = $this->rows;
        return view('livewire.locations.manage', compact('rows'))->title('Locations');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = null;
        $this->code = null;
        $this->autoCode = true;
        $this->csvFile = null;
    }

    private function loadParents(): void
    {
        $this->regions = Region::orderBy('name')->get(['id','name','code'])->toArray();
        $this->loadDistricts();
        $this->loadConstituencies();
    }

    private function loadDistricts(): void
    {
        $this->districts = $this->region_id
            ? District::where('region_id',$this->region_id)->orderBy('name')->get(['id','name','code'])->toArray()
            : [];
    }

    private function loadConstituencies(): void
    {
        $this->constituencies = $this->district_id
            ? Constituency::where('district_id',$this->district_id)->orderBy('name')->get(['id','name','code'])->toArray()
            : [];
    }

    private function makeCode(?string $name, int $maxLen): string
    {
        return Str::limit(Str::of($name ?? 'ITEM')->upper()->slug('_'), $maxLen, '');
    }

    private function dedupeCode(string $table, string $code, int $maxLen, ?int $ignoreId = null): string
    {
        $base = $code; $i = 2; $final = $base;
        while ($this->codeExists($table, $final, $ignoreId)) {
            $suffix = '_'.$i;
            $final = Str::limit($base, $maxLen - strlen($suffix), '') . $suffix;
            $i++;
        }
        return $final;
    }

    private function codeExists(string $table, string $code, ?int $ignoreId = null): bool
    {
        return match ($table) {
            'regions'        => Region::where('code',$code)->when($ignoreId, fn($q)=>$q->where('id','!=',$ignoreId))->exists(),
            'districts'      => District::where('code',$code)->when($ignoreId, fn($q)=>$q->where('id','!=',$ignoreId))->exists(),
            'constituencies' => Constituency::where('code',$code)->when($ignoreId, fn($q)=>$q->where('id','!=',$ignoreId))->exists(),
            'zones'          => Zone::where('code',$code)->when($ignoreId, fn($q)=>$q->where('id','!=',$ignoreId))->exists(),
            default          => false,
        };
    }
}

<?php

namespace App\Livewire\Executives;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\{
    ExecutiveAssignment, ExecutivePosition, Person,
    Region, District, Constituency, Zone
};

#[Layout('layouts.app')]
class Form extends Component
{
    public ?ExecutiveAssignment $executiveAssignment = null;

    #[Validate('required|exists:people,id')] public ?string $person_id = null;
    #[Validate('required|exists:executive_positions,id')] public ?string $position_id = null;

    // Polymorphic scope
    #[Validate('nullable|in:region,district,constituency,zone')] public ?string $scope_level = null;
    public ?string $region_id = null; public ?string $district_id = null; public ?string $constituency_id = null; public ?string $zone_id = null;

    #[Validate('nullable|date')] public ?string $start_date = null;
    #[Validate('nullable|date|after_or_equal:start_date')] public ?string $end_date = null;
    #[Validate('nullable|integer|min:1|max:10')] public ?int $term_number = 1;

    public array $people=[]; public array $positions=[];
    public array $regions=[]; public array $districts=[]; public array $constituencies=[]; public array $zones=[];

    public function mount(?ExecutiveAssignment $executiveAssignment = null)
    {
        $this->executiveAssignment = $executiveAssignment;
        $this->people    = Person::orderBy('last_name')->limit(300)->get(['id','first_name','last_name'])->toArray();
        $this->positions = ExecutivePosition::orderBy('name')->get(['id','name'])->toArray();
        $this->regions   = Region::orderBy('name')->get(['id','name'])->toArray();

        if ($executiveAssignment){
            $this->person_id   = $executiveAssignment->person_id;
            $this->position_id = $executiveAssignment->position_id;
            $this->term_number = $executiveAssignment->term_number ?? 1;
            $this->start_date  = $executiveAssignment->start_date;
            $this->end_date    = $executiveAssignment->end_date;

            if ($executiveAssignment->scope_type === Region::class) { $this->scope_level='region'; $this->region_id=$executiveAssignment->scope_id; }
            if ($executiveAssignment->scope_type === District::class) { $this->scope_level='district'; $this->district_id=$executiveAssignment->scope_id; }
            if ($executiveAssignment->scope_type === Constituency::class) { $this->scope_level='constituency'; $this->constituency_id=$executiveAssignment->scope_id; }
            if ($executiveAssignment->scope_type === Zone::class) { $this->scope_level='zone'; $this->zone_id=$executiveAssignment->scope_id; }

            if ($this->region_id) $this->districts = District::where('region_id',$this->region_id)->orderBy('name')->get(['id','name'])->toArray();
            if ($this->district_id) $this->constituencies = Constituency::where('district_id',$this->district_id)->orderBy('name')->get(['id','name'])->toArray();
            if ($this->constituency_id) $this->zones = Zone::where('constituency_id',$this->constituency_id)->orderBy('name')->get(['id','name'])->toArray();
        }
    }

    public function updatedRegionId(){ $this->districts = $this->region_id? District::where('region_id',$this->region_id)->orderBy('name')->get(['id','name'])->toArray():[]; $this->district_id=$this->constituency_id=$this->zone_id=null; $this->constituencies=$this->zones=[]; }
    public function updatedDistrictId(){ $this->constituencies = $this->district_id? Constituency::where('district_id',$this->district_id)->orderBy('name')->get(['id','name'])->toArray():[]; $this->constituency_id=$this->zone_id=null; $this->zones=[]; }
    public function updatedConstituencyId(){ $this->zones = $this->constituency_id? Zone::where('constituency_id',$this->constituency_id)->orderBy('name')->get(['id','name'])->toArray():[]; $this->zone_id=null; }

    public function save()
    {
        $data = $this->validate();

        [$type,$id] = [null,null];
        if ($this->scope_level==='region')       { $type=Region::class;       $id=$this->region_id; }
        elseif ($this->scope_level==='district') { $type=District::class;     $id=$this->district_id; }
        elseif ($this->scope_level==='constituency'){ $type=Constituency::class; $id=$this->constituency_id; }
        elseif ($this->scope_level==='zone')     { $type=Zone::class;         $id=$this->zone_id; }

        $payload = [
            'person_id'=>$this->person_id,
            'position_id'=>$this->position_id,
            'scope_type'=>$type, 'scope_id'=>$id,
            'start_date'=>$this->start_date, 'end_date'=>$this->end_date,
            'term_number'=>$this->term_number ?? 1,
        ];

        if ($this->executiveAssignment) $this->executiveAssignment->update($payload);
        else $this->executiveAssignment = ExecutiveAssignment::create($payload);

        session()->flash('ok','Executive assignment saved.');
        return redirect()->route('executives.index');
    }

    public function render(){ return view('livewire.executives.form')->title(($this->executiveAssignment?'Edit':'Create').' Executive'); }
}

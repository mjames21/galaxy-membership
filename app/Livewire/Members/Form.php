<?php

namespace App\Livewire\Members;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\{Person, MemberRegistration, Region, District, Constituency, Zone};

#[Layout('layouts.app')]
class Form extends Component
{
    public Person $person;
    public ?MemberRegistration $memberRegistration = null;

    #[Validate('nullable|string|max:60|unique:member_registrations,registration_number')]
    public ?string $registration_number = null;
    #[Validate('nullable|integer|min:1900|max:2100')] public ?int $registration_year = null;

    public ?string $region_id = null; public ?string $district_id = null;
    public ?string $constituency_id = null; public ?string $zone_id = null;

    public array $regions=[]; public array $districts=[]; public array $constituencies=[]; public array $zones=[];

    public function mount(Person $person, ?MemberRegistration $memberRegistration=null)
    {
        $this->person = $person; $this->memberRegistration = $memberRegistration;
        $this->regions = Region::orderBy('name')->get(['id','name'])->toArray();

        if ($memberRegistration){
            $this->registration_number = $memberRegistration->registration_number;
            $this->registration_year   = $memberRegistration->registration_year;
            $this->region_id           = $memberRegistration->region_id;
            $this->district_id         = $memberRegistration->district_id;
            $this->constituency_id     = $memberRegistration->constituency_id;
            $this->zone_id             = $memberRegistration->zone_id;
            $this->districts      = District::where('region_id',$this->region_id)->orderBy('name')->get(['id','name'])->toArray();
            $this->constituencies = Constituency::where('district_id',$this->district_id)->orderBy('name')->get(['id','name'])->toArray();
            $this->zones          = Zone::where('constituency_id',$this->constituency_id)->orderBy('name')->get(['id','name'])->toArray();
        }
    }

    public function updatedRegionId(){ $this->districts = $this->region_id ? District::where('region_id',$this->region_id)->orderBy('name')->get(['id','name'])->toArray() : []; $this->district_id=$this->constituency_id=$this->zone_id=null; $this->constituencies=$this->zones=[]; }
    public function updatedDistrictId(){ $this->constituencies = $this->district_id ? Constituency::where('district_id',$this->district_id)->orderBy('name')->get(['id','name'])->toArray() : []; $this->constituency_id=$this->zone_id=null; $this->zones=[]; }
    public function updatedConstituencyId(){ $this->zones = $this->constituency_id ? Zone::where('constituency_id',$this->constituency_id)->orderBy('name')->get(['id','name'])->toArray() : []; $this->zone_id=null; }

    protected array $rules = []; // allows dynamic unique rule override

    public function save()
    {
        // relax unique on update
        if ($this->memberRegistration) {
            $this->rules['registration_number'] = 'nullable|string|max:60|unique:member_registrations,registration_number,'.$this->memberRegistration->id.',id';
        }
        $this->validate();

        $payload = [
            'person_id' => $this->person->id,
            'registration_number' => $this->registration_number,
            'registration_year'   => $this->registration_year,
            'region_id' => $this->region_id, 'district_id'=>$this->district_id,
            'constituency_id'=>$this->constituency_id, 'zone_id'=>$this->zone_id,
        ];

        if ($this->memberRegistration) $this->memberRegistration->update($payload);
        else $this->memberRegistration = MemberRegistration::create($payload);

        session()->flash('ok','Membership saved.');
        return redirect()->route('members.index', $this->person);
    }

    public function render(){ return view('livewire.members.form')->title(($this->memberRegistration?'Edit':'Create').' Membership'); }
}

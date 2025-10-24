<?php

namespace App\Livewire\Stakeholders;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\{
    Stakeholder, Person, StakeholderCategory, PartyAffiliation, WillingnessLevel,
    SupportType, Organization, Region, District, Constituency, Zone
};

#[Layout('layouts.app')]
class Form extends Component
{
    public ?Stakeholder $stakeholder = null;

    #[Validate('required|exists:people,id')] public ?string $person_id = null;
    #[Validate('required|exists:stakeholder_categories,id')] public ?string $stakeholder_category_id = null;
    #[Validate('nullable|exists:organizations,id')] public ?string $organization_id = null;
    #[Validate('nullable|exists:party_affiliations,id')] public ?string $party_affiliation_id = null;
    #[Validate('nullable|exists:willingness_levels,id')] public ?string $willingness_level_id = null;

    public array $support_type_ids = []; // M:M

    public ?string $region_id=null; public ?string $district_id=null; public ?string $constituency_id=null; public ?string $zone_id=null;

    public array $people=[]; public array $categories=[]; public array $orgs=[]; public array $affils=[]; public array $levels=[];
    public array $supportTypes=[]; public array $regions=[]; public array $districts=[]; public array $constituencies=[]; public array $zones=[];

    public function mount(?Stakeholder $stakeholder = null)
    {
        $this->stakeholder = $stakeholder;
        $this->people = Person::orderBy('last_name')->limit(300)->get(['id','first_name','last_name'])->toArray();
        $this->categories = StakeholderCategory::orderBy('name')->get(['id','name'])->toArray();
        $this->orgs = Organization::orderBy('name')->get(['id','name'])->toArray();
        $this->affils = PartyAffiliation::orderBy('name')->get(['id','name'])->toArray();
        $this->levels = WillingnessLevel::orderBy('name')->get(['id','name'])->toArray();
        $this->supportTypes = SupportType::orderBy('name')->get(['id','name'])->toArray();
        $this->regions = Region::orderBy('name')->get(['id','name'])->toArray();

        if ($stakeholder){
            $this->fill($stakeholder->only([
                'person_id','stakeholder_category_id','organization_id','party_affiliation_id','willingness_level_id',
                'region_id','district_id','constituency_id','zone_id'
            ]));
            $this->support_type_ids = $stakeholder->supportTypes()->pluck('support_types.id')->toArray();
            if ($this->region_id) $this->districts = District::where('region_id',$this->region_id)->orderBy('name')->get(['id','name'])->toArray();
            if ($this->district_id) $this->constituencies = Constituency::where('district_id',$this->district_id)->orderBy('name')->get(['id','name'])->toArray();
            if ($this->constituency_id) $this->zones = Zone::where('constituency_id',$this->constituency_id)->orderBy('name')->get(['id','name'])->toArray();
        }
    }

    public function updatedRegionId(){ $this->districts=$this->region_id?District::where('region_id',$this->region_id)->orderBy('name')->get(['id','name'])->toArray():[]; $this->district_id=$this->constituency_id=$this->zone_id=null; $this->constituencies=$this->zones=[]; }
    public function updatedDistrictId(){ $this->constituencies=$this->district_id?Constituency::where('district_id',$this->district_id)->orderBy('name')->get(['id','name'])->toArray():[]; $this->constituency_id=$this->zone_id=null; $this->zones=[]; }
    public function updatedConstituencyId(){ $this->zones=$this->constituency_id?Zone::where('constituency_id',$this->constituency_id)->orderBy('name')->get(['id','name'])->toArray():[]; $this->zone_id=null; }

    public function save()
    {
        $data = $this->validate();

        $payload = [
            'person_id'=>$this->person_id,
            'stakeholder_category_id'=>$this->stakeholder_category_id,
            'organization_id'=>$this->organization_id,
            'party_affiliation_id'=>$this->party_affiliation_id,
            'willingness_level_id'=>$this->willingness_level_id,
            'region_id'=>$this->region_id, 'district_id'=>$this->district_id,
            'constituency_id'=>$this->constituency_id, 'zone_id'=>$this->zone_id,
        ];

        if ($this->stakeholder) $this->stakeholder->update($payload);
        else $this->stakeholder = Stakeholder::create($payload);

        $this->stakeholder->supportTypes()->sync($this->support_type_ids ?? []);

        session()->flash('ok','Stakeholder saved.');
        return redirect()->route('stakeholders.index');
    }

    public function render(){ return view('livewire.stakeholders.form')->title(($this->stakeholder?'Edit':'Create').' Stakeholder'); }
}

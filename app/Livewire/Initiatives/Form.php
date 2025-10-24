<?php

namespace App\Livewire\Initiatives;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\{
    Initiative, InitiativeCategory, InitiativeStatus, Person, Zone, Organization
};

#[Layout('layouts.app')]
class Form extends Component
{
    public ?Initiative $initiative = null;

    #[Validate('required|string|max:255')] public string $name = '';
    #[Validate('nullable|string|max:5000')] public ?string $brief_description = null;
    #[Validate('nullable|exists:initiative_categories,id')] public ?string $initiative_category_id = null;
    #[Validate('nullable|exists:initiative_statuses,id')] public ?string $status_id = null;
    #[Validate('nullable|exists:people,id')] public ?string $lead_id = null;

    public array $zone_ids = []; // M:M zones
    public array $sponsors_targeted = []; // org ids
    public array $sponsors_secured  = []; // org ids

    public array $categories=[]; public array $statuses=[]; public array $people=[];
    public array $zones=[]; public array $orgs=[];

    public function mount(?Initiative $initiative = null)
    {
        $this->initiative = $initiative;
        $this->categories = InitiativeCategory::orderBy('name')->get(['id','name'])->toArray();
        $this->statuses   = InitiativeStatus::orderBy('name')->get(['id','name'])->toArray();
        $this->people     = Person::orderBy('last_name')->limit(300)->get(['id','first_name','last_name'])->toArray();
        $this->zones      = Zone::orderBy('name')->limit(1000)->get(['id','name'])->toArray();
        $this->orgs       = Organization::orderBy('name')->get(['id','name'])->toArray();

        if ($initiative){
            $this->fill($initiative->only(['name','brief_description','initiative_category_id','status_id','lead_id']));
            $this->zone_ids = $initiative->zones()->pluck('zones.id')->toArray();
            $this->sponsors_targeted = $initiative->sponsors()->wherePivot('sponsor_status','targeted')->pluck('organizations.id')->toArray();
            $this->sponsors_secured  = $initiative->sponsors()->wherePivot('sponsor_status','secured')->pluck('organizations.id')->toArray();
        }
    }

    public function save()
    {
        $this->validate();
        $payload = [
            'name'=>$this->name,
            'brief_description'=>$this->brief_description,
            'initiative_category_id'=>$this->initiative_category_id,
            'status_id'=>$this->status_id,
            'lead_id'=>$this->lead_id,
        ];

        if ($this->initiative) $this->initiative->update($payload);
        else $this->initiative = Initiative::create($payload);

        $this->initiative->zones()->sync($this->zone_ids ?? []);

        $sync = [];
        foreach ($this->sponsors_targeted as $oid) if ($oid) $sync[$oid] = ['sponsor_status'=>'targeted'];
        foreach ($this->sponsors_secured as $oid) if ($oid) $sync[$oid]  = ['sponsor_status'=>'secured'];
        $this->initiative->sponsors()->sync($sync);

        session()->flash('ok','Initiative saved.');
        return redirect()->route('initiatives.index');
    }

    public function render(){ return view('livewire.initiatives.form')->title(($this->initiative?'Edit':'Create').' Initiative'); }
}

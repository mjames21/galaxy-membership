<?php

namespace App\Livewire\Opportunities;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\{Opportunity, OpportunityType, Organization, Person};

#[Layout('layouts.app')]
class Form extends Component
{
    public ?Opportunity $opportunity = null;

    #[Validate('required|string|max:255')] public string $name = '';
    #[Validate('nullable|exists:opportunity_types,id')] public ?string $opportunity_type_id = null;
    #[Validate('nullable|exists:organizations,id')] public ?string $organization_id = null;
    #[Validate('nullable|in:certain,high,medium,low,none')] public ?string $prospect = null;
    #[Validate('nullable|string|max:5000')] public ?string $eligibility_criteria = null;

    public array $contact_ids = []; // M:M people

    public array $types=[]; public array $orgs=[]; public array $people=[];

    public function mount(?Opportunity $opportunity = null)
    {
        $this->opportunity = $opportunity;
        $this->types  = OpportunityType::orderBy('name')->get(['id','name'])->toArray();
        $this->orgs   = Organization::orderBy('name')->get(['id','name'])->toArray();
        $this->people = Person::orderBy('last_name')->limit(300)->get(['id','first_name','last_name'])->toArray();

        if ($opportunity){
            $this->fill($opportunity->only(['name','opportunity_type_id','organization_id','prospect','eligibility_criteria']));
            $this->contact_ids = $opportunity->contacts()->pluck('people.id')->toArray();
        }
    }

    public function save()
    {
        $this->validate();
        $payload = [
            'name'=>$this->name,
            'opportunity_type_id'=>$this->opportunity_type_id,
            'organization_id'=>$this->organization_id,
            'prospect'=>$this->prospect,
            'eligibility_criteria'=>$this->eligibility_criteria,
        ];

        if ($this->opportunity) $this->opportunity->update($payload);
        else $this->opportunity = Opportunity::create($payload);

        // sync contacts with designation kept if previously set
        $sync = [];
        foreach ($this->contact_ids as $pid) if ($pid) $sync[$pid] = ['designation'=>null];
        $this->opportunity->contacts()->sync($sync);

        session()->flash('ok','Opportunity saved.');
        return redirect()->route('opportunities.index');
    }

    public function render(){ return view('livewire.opportunities.form')->title(($this->opportunity?'Edit':'Create').' Opportunity'); }
}

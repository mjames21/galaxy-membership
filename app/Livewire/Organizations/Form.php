<?php

namespace App\Livewire\Organizations;

use App\Models\{Organization, Person, AffiliationLevel, OrganizationAffiliation};
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?Organization $organization = null;

    #[Validate('required|string|max:255')] public string $name = '';
    #[Validate('nullable|string|max:120')] public ?string $type = null;
    #[Validate('nullable|email|max:255')] public ?string $email = null;
    #[Validate('nullable|string|max:40')] public ?string $phone = null;
    #[Validate('nullable|string|max:255')] public ?string $address = null;
    #[Validate('nullable|string|max:5000')] public ?string $description = null;

    #[Validate('array')] public array $contacts = []; // [ ['person_id'=>..., 'designation'=>...], ... ]

    #[Validate('nullable|exists:affiliation_levels,id')] public ?string $affiliation_level_id = null;
    #[Validate('nullable|string|max:5000')] public ?string $affiliation_description = null;
    #[Validate('nullable|string|max:5000')] public ?string $affiliation_notes = null;

    public array $people = []; public array $levels = [];

    public function mount(?Organization $organization = null)
    {
        $this->organization = $organization;
        $this->people = Person::orderBy('last_name')->limit(300)->get(['id','first_name','last_name'])->toArray();
        $this->levels = AffiliationLevel::orderBy('name')->get(['id','name'])->toArray();

        if ($organization) {
            $this->fill($organization->only(['name','type','email','phone','address','description']));
            $this->contacts = $organization->contacts()->get()->map(fn($p)=>[
                'person_id'=>$p->id,'designation'=>$p->pivot->designation,
            ])->toArray();
            $latest = $organization->latestAffiliation()->first();
            if ($latest){
                $this->affiliation_level_id = $latest->affiliation_level_id;
                $this->affiliation_description = $latest->description;
                $this->affiliation_notes = $latest->notes;
            }
        } else {
            $this->contacts = [['person_id'=>null,'designation'=>null]];
        }
    }

    public function addContactRow(){ $this->contacts[] = ['person_id'=>null,'designation'=>null]; }
    public function removeContactRow($i){ if(isset($this->contacts[$i])) array_splice($this->contacts,$i,1); }

    public function save()
    {
        $this->validate();

        $payload = [
            'name'=>$this->name,'type'=>$this->type,'email'=>$this->email,'phone'=>$this->phone,
            'address'=>$this->address,'description'=>$this->description,
        ];

        if ($this->organization) $this->organization->update($payload);
        else $this->organization = Organization::create($payload);

        $sync = [];
        foreach ($this->contacts as $row) if (!empty($row['person_id'])) $sync[$row['person_id']] = ['designation'=>$row['designation'] ?? null];
        $this->organization->contacts()->sync($sync);

        if ($this->affiliation_level_id){
            $latest = $this->organization->latestAffiliation()->first();
            $changed = !$latest
                || $latest->affiliation_level_id !== $this->affiliation_level_id
                || (string)($latest->description ?? '') !== (string)($this->affiliation_description ?? '')
                || (string)($latest->notes ?? '') !== (string)($this->affiliation_notes ?? '');
            if ($changed){
                OrganizationAffiliation::create([
                    'organization_id'=>$this->organization->id,
                    'affiliation_level_id'=>$this->affiliation_level_id,
                    'description'=>$this->affiliation_description,
                    'notes'=>$this->affiliation_notes,
                ]);
            }
        }

        session()->flash('ok','Organization saved.');
        return redirect()->route('organizations.index');
    }

    public function render(){ return view('livewire.organizations.form')->title($this->organization?'Edit Organization':'Create Organization'); }
}

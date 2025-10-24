<?php

namespace App\Livewire\Stakeholders;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Stakeholder;

class Index extends Component
{
    use WithPagination;

    #[Url] public ?string $q = null;
    #[Url] public ?string $category_id = null;
    #[Url] public ?string $affiliation_id = null;
    #[Url] public ?string $willingness_level_id = null;
    #[Url] public ?string $support_type_id = null;

    #[Url] public ?string $region_id = null;
    #[Url] public ?string $district_id = null;
    #[Url] public ?string $constituency_id = null;
    #[Url] public ?string $zone_id = null;

    #[Url] public ?string $created_from = null;
    #[Url] public ?string $created_to   = null;

    public function updatingQ(){ $this->resetPage(); }

    public function render()
    {
        $like = '%'.($this->q ?? '').'%';

        $rows = Stakeholder::with(['person','category','partyAffiliation','willingness','organization','supportTypes','district.region','constituency','zone'])
            ->when($this->q, function($q) use ($like){
                $q->whereHas('person', fn($p)=>$p->where('first_name','like',$like)->orWhere('last_name','like',$like)
                    ->orWhere('other_names','like',$like)->orWhere('phone','like',$like)->orWhere('email','like',$like))
                  ->orWhereHas('organization', fn($o)=>$o->where('name','like',$like));
            })
            ->when($this->category_id, fn($q)=>$q->where('stakeholder_category_id',$this->category_id))
            ->when($this->affiliation_id, fn($q)=>$q->where('party_affiliation_id',$this->affiliation_id))
            ->when($this->willingness_level_id, fn($q)=>$q->where('willingness_level_id',$this->willingness_level_id))
            ->when($this->support_type_id, fn($q)=>$q->whereHas('supportTypes', fn($s)=>$s->where('support_types.id',$this->support_type_id)))
            ->when($this->region_id, fn($q)=>$q->whereHas('district', fn($d)=>$d->where('region_id',$this->region_id)))
            ->when($this->district_id, fn($q)=>$q->where('district_id',$this->district_id))
            ->when($this->constituency_id, fn($q)=>$q->where('constituency_id',$this->constituency_id))
            ->when($this->zone_id, fn($q)=>$q->where('zone_id',$this->zone_id))
            ->when($this->created_from && $this->created_to, fn($q)=>
                $q->whereBetween('stakeholders.created_at', [
                    \Illuminate\Support\Carbon::parse($this->created_from)->startOfDay(),
                    \Illuminate\Support\Carbon::parse($this->created_to)->endOfDay()
                ])
            )
            ->latest()->paginate(15);

        return view('livewire.stakeholders.index', compact('rows'))->title('Stakeholders')->layout('layouts.app');
    }
}

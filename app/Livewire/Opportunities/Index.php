<?php

namespace App\Livewire\Opportunities;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Opportunity;

class Index extends Component
{
    use WithPagination;

    #[Url] public ?string $q = null;
    #[Url] public ?string $type_id = null;
    #[Url] public ?string $org_id  = null;
    #[Url] public ?string $prospect = null; // certain|high|medium|low|none

    #[Url] public ?string $updated_from = null;
    #[Url] public ?string $updated_to   = null;

    public function updatingQ(){ $this->resetPage(); }

    public function render()
    {
        $like = '%'.($this->q ?? '').'%';
        $rows = Opportunity::with(['type','organization','contacts'])
            ->when($this->q, fn($q)=>$q->where('name','like',$like)->orWhere('eligibility_criteria','like',$like))
            ->when($this->type_id, fn($q)=>$q->where('opportunity_type_id',$this->type_id))
            ->when($this->org_id,  fn($q)=>$q->where('organization_id',$this->org_id))
            ->when($this->prospect !== null && $this->prospect!=='', fn($q)=>$q->where('prospect',$this->prospect))
            ->when($this->updated_from && $this->updated_to, fn($q)=>
                $q->whereBetween('opportunities.updated_at', [
                    \Illuminate\Support\Carbon::parse($this->updated_from)->startOfDay(),
                    \Illuminate\Support\Carbon::parse($this->updated_to)->endOfDay()
                ])
            )
            ->latest()->paginate(15);

        return view('livewire.opportunities.index', compact('rows'))->title('Opportunities')->layout('layouts.app');
    }
}

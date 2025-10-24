<?php

namespace App\Livewire\Organizations;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Organization;

class Index extends Component
{
    use WithPagination;

    #[Url] public ?string $q = null;
    #[Url] public ?string $type = null;
    #[Url] public ?string $affiliation_level_id = null;

    public function updatingQ(){ $this->resetPage(); }

    public function render()
    {
        $like = '%'.($this->q ?? '').'%';
        $rows = Organization::with(['latestAffiliation.level','contacts'])
            ->when($this->q, function($q) use ($like){
                $q->where('name','like',$like)->orWhere('email','like',$like)
                  ->orWhere('phone','like',$like)->orWhere('address','like',$like);
            })
            ->when($this->type !== null && $this->type!=='', fn($q)=>$q->where('type',$this->type))
            ->when($this->affiliation_level_id, fn($q)=>$q->whereHas('latestAffiliation', fn($a)=>$a->where('affiliation_level_id',$this->affiliation_level_id)))
            ->orderBy('name')->paginate(15);

        return view('livewire.organizations.index', compact('rows'))->title('Organizations')->layout('layouts.app');
    }
}

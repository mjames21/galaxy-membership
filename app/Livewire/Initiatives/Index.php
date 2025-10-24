<?php

namespace App\Livewire\Initiatives;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Initiative;

class Index extends Component
{
    use WithPagination;

    #[Url] public ?string $q = null;
    #[Url] public ?string $category_id = null;
    #[Url] public ?string $status_id = null;

    #[Url] public ?string $region_id = null;
    #[Url] public ?string $district_id = null;
    #[Url] public ?string $constituency_id = null;
    #[Url] public ?string $zone_id = null;

    #[Url] public ?string $created_from = null;
    #[Url] public ?string $created_to   = null;

    public function render()
    {
        $like = '%'.($this->q ?? '').'%';

        $rows = Initiative::with(['category','status','lead','zones.constituency.district.region','sponsors'])
            ->when($this->q, fn($q)=>$q->where('name','like',$like)->orWhere('brief_description','like',$like))
            ->when($this->category_id, fn($q)=>$q->where('initiative_category_id',$this->category_id))
            ->when($this->status_id, fn($q)=>$q->where('status_id',$this->status_id))
            ->when($this->zone_id, fn($q)=>$q->whereHas('zones', fn($z)=>$z->where('zones.id',$this->zone_id)))
            ->when($this->constituency_id && !$this->zone_id, fn($q)=>$q->whereHas('zones', fn($z)=>$z->where('constituency_id',$this->constituency_id)))
            ->when($this->district_id && !$this->constituency_id, fn($q)=>$q->whereHas('zones.constituency', fn($c)=>$c->where('district_id',$this->district_id)))
            ->when($this->region_id && !$this->district_id, fn($q)=>$q->whereHas('zones.constituency.district', fn($d)=>$d->where('region_id',$this->region_id)))
            ->when($this->created_from && $this->created_to, fn($q)=>
                $q->whereBetween('initiatives.created_at', [
                    \Illuminate\Support\Carbon::parse($this->created_from)->startOfDay(),
                    \Illuminate\Support\Carbon::parse($this->created_to)->endOfDay()
                ])
            )
            ->latest()->paginate(15);

        return view('livewire.initiatives.index', compact('rows'))->title('Initiatives')->layout('layouts.app');
    }
}

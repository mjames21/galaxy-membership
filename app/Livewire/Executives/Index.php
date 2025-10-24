<?php

namespace App\Livewire\Executives;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{ExecutiveAssignment, Region, District, Constituency, Zone};

class Index extends Component
{
    use WithPagination;

    #[Url] public ?string $q = null;
    #[Url] public string $status = 'active'; // active|ended|all
    #[Url] public ?string $scope_level = null; // region|district|constituency|zone
    #[Url] public ?string $region_id = null;
    #[Url] public ?string $district_id = null;
    #[Url] public ?string $constituency_id = null;
    #[Url] public ?string $zone_id = null;

    public function render()
    {
        $like = '%'.($this->q ?? '').'%';
        $today = now()->toDateString();

        $rows = ExecutiveAssignment::with(['person','position','scope'])
            ->when($this->q, function($qq) use ($like){
                $qq->whereHas('person', fn($p)=>$p->where('first_name','like',$like)->orWhere('last_name','like',$like)->orWhere('other_names','like',$like))
                   ->orWhereHas('position', fn($pos)=>$pos->where('name','like',$like));
            })
            ->when($this->status!=='all', function($qq) use ($today){
                if ($this->status==='active') {
                    $qq->where(fn($w)=>$w->whereNull('start_date')->orWhere('start_date','<=',$today))
                       ->where(fn($w)=>$w->whereNull('end_date')->orWhere('end_date','>=',$today));
                } else {
                    $qq->whereNotNull('end_date')->where('end_date','<',$today);
                }
            })
            ->when($this->scope_level==='region' && $this->region_id, fn($q)=>$q->where('scope_type', Region::class)->where('scope_id',$this->region_id))
            ->when($this->scope_level==='district' && $this->district_id, fn($q)=>$q->where('scope_type', District::class)->where('scope_id',$this->district_id))
            ->when($this->scope_level==='constituency' && $this->constituency_id, fn($q)=>$q->where('scope_type', Constituency::class)->where('scope_id',$this->constituency_id))
            ->when($this->scope_level==='zone' && $this->zone_id, fn($q)=>$q->where('scope_type', Zone::class)->where('scope_id',$this->zone_id))
            ->latest('start_date')
            ->paginate(15);

        return view('livewire.executives.index', compact('rows'))->title('Executives')->layout('layouts.app');
    }
}

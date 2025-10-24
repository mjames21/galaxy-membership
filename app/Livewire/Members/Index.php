<?php

namespace App\Livewire\Members;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Person, MemberRegistration};

class Index extends Component
{
    use WithPagination;

    public Person $person;

    #[Url] public ?string $q = null;
    #[Url] public ?string $created_from = null;
    #[Url] public ?string $created_to   = null;

    public function mount(Person $person){ $this->person = $person; }

    public function render()
    {
        $like = '%'.($this->q ?? '').'%';
        $rows = MemberRegistration::where('person_id',$this->person->id)
            ->when($this->q, fn($q)=>$q->where('registration_number','like',$like))
            ->when($this->created_from && $this->created_to, fn($q)=>
                $q->whereBetween('created_at', [
                    \Illuminate\Support\Carbon::parse($this->created_from)->startOfDay(),
                    \Illuminate\Support\Carbon::parse($this->created_to)->endOfDay()
                ])
            )
            ->latest()->paginate(10);

        return view('livewire.members.index', compact('rows'))->title('Member Registrations')->layout('layouts.app');
    }
}

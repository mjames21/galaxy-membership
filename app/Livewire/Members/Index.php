<?php

namespace App\Livewire\Members;

use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Person, MemberRegistration};
use Illuminate\Support\Carbon;

class Index extends Component
{
    use WithPagination;

    public Person $person;
    public bool $showFormModal = false;
    public ?int $editingId = null;

    #[Url] public ?string $q = null;
    #[Url] public ?string $created_from = null;
    #[Url] public ?string $created_to   = null;

    public function mount(Person $person): void
    {
        $this->person = $person;
    }

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->editingId = $id;
        $this->showFormModal = true;
    }

    public function closeModal(): void
    {
        $this->showFormModal = false;
        $this->editingId = null;
    }

    #[On('membership-saved')] // Livewire v3 event listener
    public function onMembershipSaved(): void
    {
        $this->resetPage();
        $this->closeModal();
    }

    public function getRowsProperty()
    {
        $like = '%'.($this->q ?? '').'%';

        return MemberRegistration::with(['region','district','constituency','zone'])
            ->where('person_id', $this->person->id)
            ->when($this->q, fn($q) => $q->where('registration_number','like',$like))
            ->when($this->created_from && $this->created_to, fn($q) =>
                $q->whereBetween('created_at', [
                    Carbon::parse($this->created_from)->startOfDay(),
                    Carbon::parse($this->created_to)->endOfDay(),
                ])
            )
            ->latest()
            ->paginate(10);
    }

    public function render()
    {
        $rows = $this->rows;
        return view('livewire.members.index', compact('rows'))
            ->title('Member Registrations')
            ->layout('layouts.app');
    }
}

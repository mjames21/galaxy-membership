<?php

namespace App\Livewire\People;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Person;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;

#[Layout('layouts.app')]
#[Title('People')]
class Index extends Component
{
    use WithPagination;

    // Filters
    #[Url] public ?string $q = null;
    #[Url] public ?string $sort = 'last_name';
    #[Url] public ?string $dir  = 'asc';
    #[Url] public ?string $created_from = null;
    #[Url] public ?string $created_to   = null;
    #[Url] public ?string $has_members = null; // '1'
    #[Url] public ?string $region_id   = null;

    // Popup modal state (inline in this component)
    public bool $modalOpen = false;
    public ?int  $editingId = null;

    // Form fields for the popup
    public string  $first_name  = '';
    public ?string $last_name   = null;
    public ?string $other_names = null;
    public ?string $email       = null;
    public ?string $phone       = null;
    public ?string $address     = null;

    // Pagination resets on filter change
    public function updatingQ(){ $this->resetPage(); }
    public function updatingSort(){ $this->resetPage(); }
    public function updatingDir(){ $this->resetPage(); }
    public function updatingCreatedFrom(){ $this->resetPage(); }
    public function updatingCreatedTo(){ $this->resetPage(); }
    public function updatingHasMembers(){ $this->resetPage(); }
    public function updatingRegionId(){ $this->resetPage(); }

    /** Open popup for creating a new person */
    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->modalOpen = true;
    }

    /** Open popup for editing an existing person */
    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->editingId = $id;

        $person = Person::findOrFail($id);
        $this->first_name  = $person->first_name ?? '';
        $this->last_name   = $person->last_name;
        $this->other_names = $person->other_names;
        $this->email       = $person->email;
        $this->phone       = $person->phone;
        $this->address     = $person->address;

        $this->modalOpen = true;
    }

    /** Close popup */
    public function closeModal(): void
    {
        $this->modalOpen = false;
    }

    /** Save (create or update) from the popup */
    public function savePerson(): void
    {
        $data = $this->validate($this->rules());

        if ($this->editingId) {
            Person::whereKey($this->editingId)->update($data);
            session()->flash('ok', 'Person updated.');
        } else {
            Person::create($data);
            session()->flash('ok', 'Person created.');
        }

        // Refresh list and close
        $this->resetPage();
        $this->closeModal();
        $this->resetForm();
    }

    /** Validation rules (unique with ignore when editing) */
    protected function rules(): array
    {
        return [
            'first_name'  => ['required','string','max:120'],
            'last_name'   => ['nullable','string','max:120'],
            'other_names' => ['nullable','string','max:120'],
            'email'       => [
                'nullable','email','max:255',
                Rule::unique('people','email')->ignore($this->editingId),
            ],
            'phone'       => [
                'nullable','string','max:40',
                Rule::unique('people','phone')->ignore($this->editingId),
            ],
            'address'     => ['nullable','string','max:255'],
        ];
    }

    /** Reset popup form fields */
    private function resetForm(): void
    {
        $this->resetValidation();
        $this->first_name = '';
        $this->last_name = $this->other_names = $this->email = $this->phone = $this->address = null;
    }

    public function render()
    {
        $driver = (new Person)->getConnection()->getDriverName();
        $op = $driver === 'pgsql' ? 'ilike' : 'like';

        $rows = Person::withCount('memberRegistrations')
            ->when($this->q, function ($q) use ($op) {
                $like = '%'.$this->q.'%';
                $q->where(function ($w) use ($like, $op) {
                    $w->where('first_name', $op, $like)
                      ->orWhere('last_name',  $op, $like)
                      ->orWhere('other_names',$op, $like)
                      ->orWhere('email',      $op, $like)
                      ->orWhere('phone',      $op, $like);
                });
            })
            ->when($this->created_from && $this->created_to, function ($q) {
                $from = Carbon::parse($this->created_from)->startOfDay();
                $to   = Carbon::parse($this->created_to)->endOfDay();
                $q->whereBetween('people.created_at', [$from, $to]);
            })
            ->when($this->has_members === '1', function ($q) {
                $q->whereHas('memberRegistrations', function ($m) {
                    if ($this->region_id) $m->where('region_id', $this->region_id);
                });
            })
            ->when(in_array($this->sort ?? 'last_name', ['last_name','first_name','email','phone','created_at'], true),
                fn($q) => $q->orderBy($this->sort ?? 'last_name', ($this->dir === 'desc') ? 'desc' : 'asc'),
                fn($q) => $q->orderBy('last_name')
            )
            ->paginate(15);

        return view('livewire.people.index', compact('rows'));
    }
}

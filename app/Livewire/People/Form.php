<?php
namespace App\Livewire\People;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Person;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
#[Title('Person')]
class Form extends Component
{
    public ?Person $person = null;

    public string  $first_name  = '';
    public ?string $last_name   = null;
    public ?string $other_names = null;
    public ?string $email       = null;
    public ?string $phone       = null;
    public ?string $address     = null;

    public function mount(?Person $person = null): void
    {
        $this->person = $person;
        if ($person) {
            $this->first_name  = $person->first_name ?? '';
            $this->last_name   = $person->last_name;
            $this->other_names = $person->other_names;
            $this->email       = $person->email;
            $this->phone       = $person->phone;
            $this->address     = $person->address;
        }
    }

    protected function rules(): array
    {
        return [
            'first_name'  => ['required','string','max:120'],
            'last_name'   => ['nullable','string','max:120'],
            'other_names' => ['nullable','string','max:120'],
            'email'       => ['nullable','email','max:255', Rule::unique('people','email')->ignore($this->person?->id)],
            'phone'       => ['nullable','string','max:40', Rule::unique('people','phone')->ignore($this->person?->id)],
            'address'     => ['nullable','string','max:255'],
        ];
    }

    public function render()
    {
        return view('livewire.people.form')
            ->title($this->person ? 'Edit Person' : 'Create Person');
    }
}

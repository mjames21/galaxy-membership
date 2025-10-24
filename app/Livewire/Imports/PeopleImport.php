<?php

namespace App\Livewire\Imports;

use Livewire\Component;
use Livewire\WithFileUploads;

class PeopleImport extends Component
{
    use WithFileUploads;

    public $file;
    public array $report = [];

    public function import()
    {
        $this->validate(['file'=>'required|file|mimes:csv,txt']);
        $path = $this->file->store('imports');

        // TODO: call your PeopleCsvImporter::run($path)
        // simulate:
        $this->report = ['ok'=>true,'created'=>0,'updated'=>0,'errors'=>[]];

        session()->flash('ok','People import queued/processed.');
    }

    public function render(){ return view('livewire.imports.people')->title('Import: People')->layout('layouts.app'); }
}

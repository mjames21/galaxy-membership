<?php

namespace App\Livewire\Imports;

use Livewire\Component;
use Livewire\WithFileUploads;

class StakeholdersImport extends Component
{
    use WithFileUploads;

    public $file; public array $report=[];

    public function import()
    {
        $this->validate(['file'=>'required|file|mimes:csv,txt']);
        $path = $this->file->store('imports');
        // TODO: StakeholderCsvImporter::run($path)
        $this->report = ['ok'=>true,'created'=>0,'updated'=>0,'errors'=>[]];
        session()->flash('ok','Stakeholders import queued/processed.');
    }

    public function render(){ return view('livewire.imports.stakeholders')->title('Import: Stakeholders')->layout('layouts.app'); }
}

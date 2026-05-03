<?php

namespace App\Livewire;

use App\Exports\InitialStockTemplate;
use App\Imports\AutoSetInitialStockImport;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ImportCentre extends Component
{
    use WithFileUploads;

    public $file;

    public function uploadFile()
    {
        $this->validate([
            'file' => 'required|file|mimes:csv,xlsx|max:10240',
        ]);

        // Example: store file
        $path = $this->file->store('imports');

        Excel::import(new AutoSetInitialStockImport, $path);

        Flux::toast('File successfully uploaded!', 'success');
    }

    public function getTemplate()
    {
        Flux::toast('Template download started!', 'success');

        return Excel::download(new InitialStockTemplate, Str::slug(config('app.name').'-stock-template-'.date('ymdHis')).'.xlsx');
    }

    public function render()
    {
        return view('livewire.import-centre');
    }
}

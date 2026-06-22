<?php

namespace App\Livewire;

use App\Exports\CategoriesTemplate;
use App\Exports\InitialStockTemplate;
use App\Exports\ItemsTemplate;
use App\Exports\VariantTypesTemplate;
use App\Exports\VariantTypeValuesTemplate;
use App\Exports\VariationsTemplate;
use App\Imports\AutoSetInitialStockImport;
use App\Imports\CategoriesImport;
use App\Imports\ItemsImport;
use App\Imports\VariantTypesImport;
use App\Imports\VariantTypeValuesImport;
use App\Imports\VariationsImport;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ImportCentre extends Component
{
    use WithFileUploads;

    public $file;

    public string $importType = 'stock';

    public function uploadFile()
    {
        $this->validate([
            'file' => 'required|file|mimes:csv,xlsx|max:10240',
            'importType' => 'required|in:stock,variant-types,variant-values,categories,items,variations',
        ]);

        $path = $this->file->store('imports');

        $importer = match ($this->importType) {
            'variant-types' => new VariantTypesImport,
            'variant-values' => new VariantTypeValuesImport,
            'categories' => new CategoriesImport,
            'items' => new ItemsImport,
            'variations' => new VariationsImport,
            default => new AutoSetInitialStockImport,
        };

        Excel::import($importer, $path);

        $labels = [
            'stock' => 'Combined Stock Template',
            'variant-types' => 'Variant Types',
            'variant-values' => 'Variant Values',
            'categories' => 'Categories',
            'items' => 'Inventory Items',
            'variations' => 'Product Variations',
        ];

        Flux::toast(($labels[$this->importType] ?? 'File').' imported successfully!', 'success');
    }

    public function getTemplate()
    {
        Flux::toast('Template download started!', 'success');

        return Excel::download(
            new InitialStockTemplate,
            Str::slug(config('app.name').'-stock-template-'.date('ymdHis')).'.xlsx'
        );
    }

    public function downloadVariantTypes()
    {
        Flux::toast('Template download started!', 'success');

        return Excel::download(
            new VariantTypesTemplate,
            Str::slug(config('app.name').'-variant-types-'.date('ymdHis')).'.xlsx'
        );
    }

    public function downloadVariantTypeValues()
    {
        Flux::toast('Template download started!', 'success');

        return Excel::download(
            new VariantTypeValuesTemplate,
            Str::slug(config('app.name').'-variant-type-values-'.date('ymdHis')).'.xlsx'
        );
    }

    public function downloadCategories()
    {
        Flux::toast('Template download started!', 'success');

        return Excel::download(
            new CategoriesTemplate,
            Str::slug(config('app.name').'-categories-'.date('ymdHis')).'.xlsx'
        );
    }

    public function downloadItems()
    {
        Flux::toast('Template download started!', 'success');

        return Excel::download(
            new ItemsTemplate,
            Str::slug(config('app.name').'-items-'.date('ymdHis')).'.xlsx'
        );
    }

    public function downloadVariations()
    {
        Flux::toast('Template download started!', 'success');

        return Excel::download(
            new VariationsTemplate,
            Str::slug(config('app.name').'-variations-'.date('ymdHis')).'.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.import-centre');
    }
}

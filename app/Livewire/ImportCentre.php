<?php

namespace App\Livewire;

use App\Exports\InitialStockTemplate;
use App\Imports\AutoSetInitialStockImport;
use App\Models\InventoryItem;
use App\Models\InventoryVariant;
use App\Models\StockImport;
use App\Models\StockImportItem;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ImportCentre extends Component
{
    use WithFileUploads;

    public $file;

    #[Computed]
    public function recentImports()
    {
        return StockImport::with('user')->latest()->take(10)->get();
    }

    public function uploadFile()
    {
        abort_unless(auth()->user()->can('imports.create'), 403);

        $this->validate([
            'file' => 'required|file|mimes:csv,xlsx|max:10240',
        ]);

        $path = $this->file->store('imports');

        $importBatch = StockImport::create([
            'user_id' => auth()->id(),
            'filename' => $this->file->getClientOriginalName(),
            'status' => 'completed',
        ]);

        try {
            Excel::import(new AutoSetInitialStockImport($importBatch), $path);
            Flux::toast('File successfully imported!', 'success');
        } catch (\Exception $e) {
            $importBatch->delete(); // Cleanup if failed
            Flux::toast('Import failed: ' . $e->getMessage(), 'danger');
        }

        $this->reset('file');
    }

    public function reverseImport(int $id)
    {
        abort_unless(auth()->user()->can('imports.revert'), 403);

        $import = StockImport::findOrFail($id);

        if ($import->status === 'reversed') {
            Flux::toast('Import already reversed.', 'warning');
            return;
        }

        DB::transaction(function () use ($import) {
            $items = $import->items()->orderBy('id', 'desc')->get();

            foreach ($items as $importItem) {
                $model = $importItem->importable;

                if ($model) {
                    if ($model instanceof InventoryVariant) {
                        // Decement stock before deleting
                        $item = $model->item;
                        if ($item) {
                            $item->decrement('stock_quantity');
                            $item->decrement('available_quantity');
                        }
                        $model->delete();
                    } else {
                        // For Categories, Items, VariantTypes, VariantTypeValues
                        // Only delete if they don't have dependencies created AFTER the import
                        // Actually the requirement said "delete items and units created"
                        $model->delete();
                    }
                }
                $importItem->delete();
            }

            $import->update([
                'status' => 'reversed',
                'reversed_at' => now(),
                'reversed_by' => auth()->id(),
            ]);
        });

        Flux::toast('Import successfully reversed.', 'success');
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

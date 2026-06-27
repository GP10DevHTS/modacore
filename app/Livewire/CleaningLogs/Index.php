<?php

namespace App\Livewire\CleaningLogs;

use App\Models\CleaningLog;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public string $statusFilter = 'all'; // all, in_cleaning, returned

    #[Computed]
    public function logs()
    {
        return CleaningLog::query()
            ->with(['inventoryItem', 'variant', 'booking.customer', 'creator'])
            ->when($this->search, fn ($q) => $q->whereHas('inventoryItem', fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->orWhereHas('variant', fn ($q) => $q->where('label', 'like', "%{$this->search}%"))
                ->orWhereHas('booking', fn ($q) => $q->where('booking_number', 'like', "%{$this->search}%"))
            )
            ->when($this->dateFrom, fn ($q) => $q->whereDate('sent_to_cleaning_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('sent_to_cleaning_at', '<=', $this->dateTo))
            ->when($this->statusFilter === 'in_cleaning', fn ($q) => $q->whereNull('returned_from_cleaning_at'))
            ->when($this->statusFilter === 'returned', fn ($q) => $q->whereNotNull('returned_from_cleaning_at'))
            ->latest('sent_to_cleaning_at')
            ->paginate(20);
    }

    #[Computed]
    public function stats(): array
    {
        $base = CleaningLog::query();

        if ($this->dateFrom) {
            $base->whereDate('sent_to_cleaning_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $base->whereDate('sent_to_cleaning_at', '<=', $this->dateTo);
        }

        return [
            'total_sent' => (clone $base)->count(),
            'total_returned' => (clone $base)->whereNotNull('returned_from_cleaning_at')->count(),
            'still_out' => (clone $base)->whereNull('returned_from_cleaning_at')->count(),
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.cleaning-logs.index');
    }
}

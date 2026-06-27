<?php

namespace App\Livewire\RentalOperations;

use App\Models\BookingItem;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    /** @var list<string> */
    public array $statusFilter = ['checked_out', 'in_cleaning'];

    public function mount(): void
    {
        $route = request()->route();
        if ($route && $route->getName() === 'rental-operations.checked-out') {
            $this->statusFilter = ['checked_out'];
        } elseif ($route && $route->getName() === 'rental-operations.in-cleaning') {
            $this->statusFilter = ['in_cleaning'];
        }
    }

    #[Computed]
    public function checkedOutCount(): int
    {
        return BookingItem::where('status', 'checked_out')->count();
    }

    #[Computed]
    public function inCleaningCount(): int
    {
        return BookingItem::where('status', 'in_cleaning')->count();
    }

    #[Computed]
    public function items()
    {
        return BookingItem::query()
            ->with(['booking.customer', 'inventoryItem', 'variant'])
            ->whereIn('status', $this->statusFilter)
            ->when($this->search, fn ($q) => $q->whereHas('booking.customer', fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->orWhereHas('inventoryItem', fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            )
            ->latest()
            ->paginate(15);
    }

    public function toggleStatus(string $status): void
    {
        if (in_array($status, $this->statusFilter)) {
            $this->statusFilter = array_values(array_diff($this->statusFilter, [$status]));
        } else {
            $this->statusFilter[] = $status;
        }
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.rental-operations.index');
    }
}

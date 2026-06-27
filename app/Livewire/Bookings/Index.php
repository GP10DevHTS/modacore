<?php

namespace App\Livewire\Bookings;

use App\Models\Booking;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    /** @var list<string> */
    public array $statusFilter = ['confirmed', 'active'];

    public ?int $confirmingId = null;

    public ?int $cancellingId = null;

    #[Computed]
    public function bookings()
    {
        return Booking::query()
            ->with(['customer'])
            ->search($this->search)
            ->when($this->statusFilter, fn ($q) => $q->whereIn('status', $this->statusFilter))
            ->latest()
            ->paginate(15);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
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

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openConfirm(int $id): void
    {
        $this->confirmingId = $id;
        $this->js('$flux.modal("confirm-booking").show()');
    }

    public function confirmBooking(): void
    {
        abort_unless(auth()->user()->can('bookings.edit'), 403);

        $booking = Booking::findOrFail($this->confirmingId);

        if ($booking->items()->count() === 0) {
            Flux::toast(text: 'Cannot confirm a booking with no items.', variant: 'danger');
            $this->js('$flux.modal("confirm-booking").close()');

            return;
        }

        $booking->update(['status' => 'confirmed']);
        unset($this->bookings);
        $this->js('$flux.modal("confirm-booking").close()');
        Flux::toast('Booking confirmed.');
        $this->confirmingId = null;
    }

    public function openCancel(int $id): void
    {
        $this->cancellingId = $id;
        $this->js('$flux.modal("cancel-booking").show()');
    }

    public function cancelBooking(): void
    {
        abort_unless(auth()->user()->can('bookings.edit'), 403);

        $booking = Booking::findOrFail($this->cancellingId);

        if (! in_array($booking->status, ['draft', 'confirmed'])) {
            Flux::toast(text: 'This booking cannot be cancelled.', variant: 'danger');
            $this->js('$flux.modal("cancel-booking").close()');

            return;
        }

        $booking->load('items');
        $checkedOut = $booking->items->filter(fn ($item) => in_array($item->status, ['checked_out', 'in_cleaning']))->count();

        if ($checkedOut > 0) {
            Flux::toast(text: 'Cannot cancel a booking with checked-out items.', variant: 'danger');
            $this->js('$flux.modal("cancel-booking").close()');

            return;
        }

        $booking->update(['status' => 'cancelled']);
        unset($this->bookings);
        $this->js('$flux.modal("cancel-booking").close()');
        Flux::toast('Booking cancelled.');
        $this->cancellingId = null;
    }

    public function markActive(int $id): void
    {
        abort_unless(auth()->user()->can('bookings.edit'), 403);

        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'confirmed') {
            Flux::toast(text: 'Only confirmed bookings can be marked as active.', variant: 'danger');

            return;
        }

        $booking->update(['status' => 'active']);
        unset($this->bookings);
        Flux::toast('Booking marked as active.');
    }

    public function markCompleted(int $id): void
    {
        abort_unless(auth()->user()->can('bookings.edit'), 403);

        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'active') {
            Flux::toast(text: 'Only active bookings can be marked as completed.', variant: 'danger');

            return;
        }

        $booking->load('items');
        $unreturned = $booking->items->filter(fn ($item) => $item->status !== 'returned')->count();

        if ($unreturned > 0) {
            Flux::toast(text: 'Cannot complete booking — '.$unreturned.' '.Str::plural('item', $unreturned).' not yet returned.', variant: 'danger');

            return;
        }

        $booking->update(['status' => 'completed']);
        unset($this->bookings);
        Flux::toast('Booking completed.');
    }

    public function render()
    {
        return view('livewire.bookings.index');
    }
}

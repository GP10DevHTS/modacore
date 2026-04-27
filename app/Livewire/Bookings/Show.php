<?php

namespace App\Livewire\Bookings;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Payment;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public Booking $booking;

    public string $paymentAmount = '';

    public string $paymentMethod = 'cash';

    public string $paymentReference = '';

    public bool $paymentIsDeposit = false;

    public string $paymentNotes = '';

    public string $paymentPaidAt = '';

    #[Computed]
    public function amountPaid(): float
    {
        return (float) $this->booking->payments()->sum('amount');
    }

    #[Computed]
    public function depositPaid(): float
    {
        return (float) $this->booking->payments()->where('is_deposit', true)->sum('amount');
    }

    #[Computed]
    public function balanceDue(): float
    {
        return max(0, (float) $this->booking->total_amount - $this->amountPaid);
    }

    public function mount(Booking $booking): void
    {
        $this->booking = $booking->load(['customer', 'items.inventoryItem', 'items.variant', 'payments.createdBy']);
        $this->paymentPaidAt = now()->format('Y-m-d');
    }

    public function checkOutItem(int $itemId): void
    {
        abort_unless(auth()->user()->can('bookings.edit'), 403);

        $item = BookingItem::findOrFail($itemId);

        if ($item->status !== 'pending') {
            Flux::toast(text: 'Item is not in pending state.', variant: 'danger');

            return;
        }

        $item->update([
            'status' => 'checked_out',
            'checked_out_at' => now(),
        ]);

        $this->booking->refresh()->load(['items.inventoryItem', 'items.variant']);
        Flux::toast('Item marked as checked out.');
    }

    public function markItemInCleaning(int $itemId): void
    {
        abort_unless(auth()->user()->can('bookings.edit'), 403);

        $item = BookingItem::findOrFail($itemId);

        if ($item->status !== 'checked_out') {
            Flux::toast(text: 'Item must be checked out first.', variant: 'danger');

            return;
        }

        $item->update(['status' => 'in_cleaning']);

        $this->booking->refresh()->load(['items.inventoryItem', 'items.variant']);
        Flux::toast('Item sent for cleaning.');
    }

    public function returnItem(int $itemId): void
    {
        abort_unless(auth()->user()->can('bookings.edit'), 403);

        $item = BookingItem::findOrFail($itemId);

        if (! in_array($item->status, ['checked_out', 'in_cleaning'])) {
            Flux::toast(text: 'Item cannot be returned from its current state.', variant: 'danger');

            return;
        }

        $item->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        $this->booking->refresh()->load(['items.inventoryItem', 'items.variant']);
        Flux::toast('Item returned to inventory.');
    }

    public function openPaymentForm(): void
    {
        $this->paymentAmount = '';
        $this->paymentMethod = 'cash';
        $this->paymentReference = '';
        $this->paymentIsDeposit = false;
        $this->paymentNotes = '';
        $this->paymentPaidAt = now()->format('Y-m-d');
        $this->resetValidation();
        $this->js('$flux.modal("record-payment").show()');
    }

    public function recordPayment(): void
    {
        abort_unless(auth()->user()->can('payments.create'), 403);

        $this->validate([
            'paymentAmount' => ['required', 'numeric', 'min:0.01'],
            'paymentMethod' => ['required', 'in:cash,card,mobile_money'],
            'paymentReference' => ['nullable', 'string', 'max:255'],
            'paymentIsDeposit' => ['boolean'],
            'paymentNotes' => ['nullable', 'string'],
            'paymentPaidAt' => ['required', 'date'],
        ]);

        Payment::create([
            'booking_id' => $this->booking->id,
            'amount' => $this->paymentAmount,
            'payment_method' => $this->paymentMethod,
            'reference' => $this->paymentReference ?: null,
            'is_deposit' => $this->paymentIsDeposit,
            'notes' => $this->paymentNotes ?: null,
            'paid_at' => $this->paymentPaidAt,
            'created_by' => auth()->id(),
        ]);

        $this->booking->refresh();
        unset($this->amountPaid, $this->depositPaid, $this->balanceDue);

        $this->js('$flux.modal("record-payment").close()');
        Flux::toast('Payment recorded.');
    }

    public function transitionStatus(string $status): void
    {
        abort_unless(auth()->user()->can('bookings.edit'), 403);

        $allowed = [
            'draft' => ['confirmed', 'cancelled'],
            'confirmed' => ['active', 'cancelled'],
            'active' => ['completed', 'cancelled'],
            'completed' => ['cancelled'],
        ];

        $current = $this->booking->status;

        if (! in_array($status, $allowed[$current] ?? [])) {
            Flux::toast(text: "Cannot transition from {$current} to {$status}.", variant: 'danger');

            return;
        }

        $this->booking->update(['status' => $status]);
        $this->booking->refresh();

        Flux::toast('Booking status updated to '.$status.'.');
    }

    public function render()
    {
        return view('livewire.bookings.show');
    }
}

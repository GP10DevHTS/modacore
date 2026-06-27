<?php

namespace App\Livewire\Bookings;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\CleaningLog;
use App\Models\DepositRefund;
use App\Models\Payment;
use App\Notifications\WhatsAppReturnReminder;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

class Show extends Component
{
    public Booking $booking;

    public string $paymentAmount = '';

    public string $paymentMethod = 'cash';

    public string $paymentReference = '';

    public bool $paymentIsDeposit = false;

    public string $paymentNotes = '';

    public string $paymentPaidAt = '';

    // Deposit refund state
    public ?int $refundDepositId = null;

    public string $refundAmount = '';

    public string $refundReason = '';

    public string $refundDate = '';

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
        $this->booking = $booking->load(['customer', 'items.inventoryItem', 'items.variant', 'payments.refunds', 'depositRefunds']);
        $this->paymentPaidAt = now()->format('Y-m-d');
        $this->refundDate = now()->format('Y-m-d');
    }

    public function checkOutItem(int $itemId): void
    {
        abort_unless(auth()->user()->can('bookings.edit'), 403);

        $item = BookingItem::findOrFail($itemId);

        if ($item->status !== 'pending') {
            Flux::toast(text: 'Item is not in pending state.', variant: 'danger');

            return;
        }

        $checkedOut = $item->inventory_variant_id
            ? $item->variant?->newQuery()
                ->where('id', $item->inventory_variant_id)
                ->where('available_quantity', '>=', $item->quantity)
                ->decrement('available_quantity', $item->quantity)
            : $item->inventoryItem?->newQuery()
                ->where('id', $item->inventory_item_id)
                ->where('available_quantity', '>=', $item->quantity)
                ->decrement('available_quantity', $item->quantity);

        if (! $checkedOut) {
            Flux::toast(text: 'Not enough available stock to check out this item.', variant: 'danger');

            return;
        }

        $item->update([
            'status' => 'checked_out',
            'checked_out_at' => now(),
        ]);

        $this->booking->update([
            'status' => 'active',
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

        CleaningLog::create([
            'booking_item_id' => $item->id,
            'inventory_item_id' => $item->inventory_item_id,
            'inventory_variant_id' => $item->inventory_variant_id,
            'booking_id' => $item->booking_id,
            'quantity' => $item->quantity,
            'sent_to_cleaning_at' => now(),
            'created_by' => auth()->id(),
        ]);

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

        $wasInCleaning = $item->status === 'in_cleaning';

        $item->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        if ($wasInCleaning) {
            CleaningLog::where('booking_item_id', $item->id)
                ->whereNull('returned_from_cleaning_at')
                ->update(['returned_from_cleaning_at' => now()]);
        }

        if ($item->inventory_variant_id) {
            $item->variant?->increment('available_quantity', $item->quantity);
        } else {
            $item->inventoryItem?->increment('available_quantity', $item->quantity);
        }

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

        $this->paymentAmount = (string) floatval(str_replace(',', '', trim($this->paymentAmount)));

        $this->validate([
            'paymentAmount' => ['required', 'numeric', 'min:0.01'],
            'paymentMethod' => ['required', 'in:cash,card,mobile_money'],
            'paymentReference' => ['nullable', 'string', 'max:255'],
            'paymentIsDeposit' => ['boolean'],
            'paymentNotes' => ['nullable', 'string'],
            'paymentPaidAt' => ['required', 'date'],
        ]);

        Payment::create([
            'receipt_number' => Payment::generateReceiptNumber(),
            'booking_id' => $this->booking->id,
            'amount' => $this->paymentAmount,
            'payment_method' => $this->paymentMethod,
            'reference' => $this->paymentReference ?: null,
            'is_deposit' => $this->paymentIsDeposit,
            'notes' => $this->paymentNotes ?: null,
            'paid_at' => $this->paymentPaidAt,
            'created_by' => auth()->id(),
        ]);

        $this->booking->refresh()->load(['payments.refunds', 'depositRefunds']);
        unset($this->amountPaid, $this->depositPaid, $this->balanceDue);

        $this->js('$flux.modal("record-payment").close()');
        Flux::toast('Payment recorded.');
    }

    public function openRefundForm(int $paymentId): void
    {
        $payment = $this->booking->payments->find($paymentId);

        if (! $payment || ! $payment->is_deposit) {
            Flux::toast(text: 'Only deposit payments can be refunded.', variant: 'danger');

            return;
        }

        $available = $payment->availableForRefund();

        if ($available <= 0) {
            Flux::toast(text: 'This deposit has already been fully refunded.', variant: 'danger');

            return;
        }

        $this->refundDepositId = $paymentId;
        $this->refundAmount = (string) $available;
        $this->refundReason = '';
        $this->refundDate = now()->format('Y-m-d');
        $this->resetValidation(['refundAmount', 'refundReason', 'refundDate']);
        $this->js('$flux.modal("refund-deposit").show()');
    }

    public function processRefund(): void
    {
        abort_unless(auth()->user()->can('payments.create'), 403);

        $payment = Payment::find($this->refundDepositId);

        abort_if(! $payment || $payment->booking_id !== $this->booking->id, 404);

        $this->refundAmount = (string) floatval(str_replace(',', '', trim($this->refundAmount)));

        $maxRefund = $payment->availableForRefund();

        $this->validate([
            'refundAmount' => ['required', 'numeric', 'min:0.01', "max:{$maxRefund}"],
            'refundDate' => ['required', 'date'],
            'refundReason' => ['nullable', 'string', 'max:500'],
        ]);

        DepositRefund::create([
            'refund_number' => DepositRefund::generateRefundNumber(),
            'booking_id' => $this->booking->id,
            'payment_id' => $payment->id,
            'amount' => $this->refundAmount,
            'refunded_at' => $this->refundDate,
            'reason' => $this->refundReason ?: null,
            'created_by' => auth()->id(),
        ]);

        $this->booking->refresh()->load(['payments.refunds', 'depositRefunds']);
        unset($this->amountPaid, $this->depositPaid, $this->balanceDue);
        $this->refundDepositId = null;

        $this->js('$flux.modal("refund-deposit").close()');
        Flux::toast('Deposit refund recorded.');
    }

    public function sendWhatsAppReturnReminder(): void
    {
        abort_unless(auth()->user()->can('bookings.edit'), 403);

        $this->booking->loadMissing('customer', 'items.inventoryItem', 'items.variant');

        $checkedOutItems = $this->booking->items->filter(fn ($i) => $i->status === 'checked_out');

        if ($checkedOutItems->isEmpty()) {
            Flux::toast(text: 'No checked-out items to remind about.', variant: 'danger');

            return;
        }

        $customer = $this->booking->customer;
        $balance = max(0, (float) $this->booking->total_amount - (float) $this->booking->payments()->sum('amount'));

        // Build WhatsApp message
        $itemsList = $checkedOutItems->map(fn ($i) => ($i->variant ? $i->inventoryItem->name.' ('.$i->variant->name.')' : $i->inventoryItem->name)
            .' x'.$i->quantity
        )->implode("\n");

        $message = "*Reminder – Return Due*\n\n"
            ."Hi {$customer->name},\n\n"
            .'This is a reminder that your hired item(s) from '.config('app.name')." *({$this->booking->booking_number})* are due for return.\n\n"
            ."*Hire Period:* {$this->booking->hire_from->format('d M Y')} → {$this->booking->hire_to->format('d M Y')}\n\n"
            ."*Items:*\n{$itemsList}\n\n";

        if ($balance > 0) {
            $formattedBalance = number_format($balance, 0);
            $message .= "*Outstanding Balance:* UGX {$formattedBalance}\n\n";
        }

        $message .= "Please return the items on time.\nThank you!";

//        // Log the reminder
//        $recipients = Permission::where('name', 'bookings.view')
//            ->first()?->users()
//            ->get()
//            ->merge(Permission::where('name', 'bookings.view')
//                ->first()?->roles()->with('users')->get()->flatMap->users)
//            ->unique('id');
//
//        if ($recipients && $recipients->isNotEmpty()) {
//            $notification = new WhatsAppReturnReminder(
//                booking: $this->booking,
//                messagePreview: Str::limit($message, 200),
//            );
//
//            foreach ($recipients as $user) {
//                $user->notify($notification);
//            }
//        }

        $this->booking->update(['alert_sent_at' => now()]);

        Flux::toast(text: 'WhatsApp reminder logged and opened.', variant: 'success');

        // Redirect to WhatsApp
        $phone = preg_replace('/[^0-9]/', '', $customer->phone);
        if (! str_starts_with($phone, '256') && strlen($phone) === 9) {
            $phone = '256'.ltrim($phone, '0');
        } elseif (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            $phone = '256'.substr($phone, 1);
        }

        $waUrl = 'https://wa.me/'.$phone.'?text='.rawurlencode($message);

        $this->js("window.open('{$waUrl}', '_blank')");
    }

    public function transitionStatus(string $status): void
    {
        abort_unless(auth()->user()->can('bookings.edit'), 403);

        $allowed = [
            'draft' => ['confirmed', 'cancelled'],
            'confirmed' => ['active', 'cancelled'],
            'active' => ['completed'],
            'completed' => [],
        ];

        $current = $this->booking->status;

        if (! in_array($status, $allowed[$current] ?? [])) {
            Flux::toast(text: "Cannot transition from {$current} to {$status}.", variant: 'danger');

            return;
        }

        if ($status === 'completed') {
            $unreturned = $this->booking->items->filter(fn ($item) => $item->status !== 'returned')->count();
            if ($unreturned > 0) {
                Flux::toast(text: "Cannot complete booking — {$unreturned} ".Str::plural('item', $unreturned).' not yet returned.', variant: 'danger');

                return;
            }
        }

        if ($status === 'cancelled') {
            $checkedOut = $this->booking->items->filter(fn ($item) => in_array($item->status, ['checked_out', 'in_cleaning']))->count();
            if ($checkedOut > 0) {
                Flux::toast(text: 'Cannot cancel a booking with checked-out items.', variant: 'danger');

                return;
            }
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

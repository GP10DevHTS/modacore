<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\SupplierInvoice;
use App\Notifications\BookingAlertNotification;
use App\Notifications\OverdueInvoiceNotification;
use App\Notifications\WhatsAppReturnReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class GenerateSystemAlertsCommand extends Command
{
    protected $signature = 'app:generate-alerts {--fresh : Clear all existing notifications first}';

    protected $description = 'Generate system alert notifications based on current application data';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            DB::table('notifications')->delete();
            $this->info('Cleared existing notifications.');
        }

        $this->generateOverdueInvoiceAlerts();
        $this->generateDraftBookingAlerts();
        $this->generateDueReturnAlerts();

        $this->info('System alerts generated successfully.');

        return Command::SUCCESS;
    }

    protected function generateOverdueInvoiceAlerts(): void
    {
        $permission = Permission::where('name', 'inventory.view')->first();

        if (! $permission) {
            return;
        }

        $recipients = $permission->users()
            ->get()
            ->merge($permission->roles()->with('users')->get()->flatMap->users)
            ->unique('id');

        if ($recipients->isEmpty()) {
            return;
        }

        $overdueInvoices = SupplierInvoice::query()
            ->with('purchaseOrder.supplier')
            ->whereNull('deleted_at')
            ->whereNotIn('payment_status', [PaymentStatus::FullyPaid->value])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->toDateString())
            ->get();

        $count = 0;
        foreach ($overdueInvoices as $invoice) {
            $daysOverdue = (int) now()->diffInDays($invoice->due_date);
            $notification = new OverdueInvoiceNotification(
                invoiceNumber: $invoice->invoice_number,
                supplierName: $invoice->purchaseOrder?->supplier?->name ?? 'Unknown Supplier',
                amount: (float) $invoice->total_amount,
                daysOverdue: $daysOverdue,
                invoiceId: $invoice->id,
            );

            foreach ($recipients as $user) {
                $alreadySent = DB::table('notifications')
                    ->where('notifiable_id', $user->id)
                    ->where('type', OverdueInvoiceNotification::class)
                    ->whereDate('created_at', today())
                    ->whereRaw("json_extract(data, '$.invoice_id') = ?", [$invoice->id])
                    ->exists();

                if (! $alreadySent) {
                    $user->notify($notification);
                    $count++;
                }
            }
        }

        $this->line("  → {$count} overdue invoice alert(s) generated.");
    }

    protected function generateDraftBookingAlerts(): void
    {
        $permission = Permission::where('name', 'bookings.view')->first();

        if (! $permission) {
            return;
        }

        $recipients = $permission->users()
            ->get()
            ->merge($permission->roles()->with('users')->get()->flatMap->users)
            ->unique('id');

        if ($recipients->isEmpty()) {
            return;
        }

        $draftBookings = Booking::query()
            ->with('customer')
            ->where('status', 'draft')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        $count = 0;
        foreach ($draftBookings as $booking) {
            $notification = new BookingAlertNotification(
                bookingNumber: $booking->booking_number,
                message: "Booking {$booking->booking_number} for {$booking->customer?->name} has been in draft for over 24 hours.",
                bookingId: $booking->id,
                iconType: 'info',
            );

            foreach ($recipients as $user) {
                $alreadySent = DB::table('notifications')
                    ->where('notifiable_id', $user->id)
                    ->where('type', BookingAlertNotification::class)
                    ->whereDate('created_at', today())
                    ->whereRaw("json_extract(data, '$.booking_id') = ?", [$booking->id])
                    ->exists();

                if (! $alreadySent) {
                    $user->notify($notification);
                    $count++;
                }
            }
        }

        $this->line("  → {$count} draft booking alert(s) generated.");
    }

    protected function generateDueReturnAlerts(): void
    {
        $permission = Permission::where('name', 'bookings.view')->first();

        if (! $permission) {
            return;
        }

        $recipients = $permission->users()
            ->get()
            ->merge($permission->roles()->with('users')->get()->flatMap->users)
            ->unique('id');

        if ($recipients->isEmpty()) {
            return;
        }

        $dueTomorrow = now()->addDay()->startOfDay();

        // Bookings with checked_out items whose hire_to is tomorrow
        $bookingIds = BookingItem::query()
            ->where('status', 'checked_out')
            ->whereHas('booking', fn ($q) => $q
                ->where('hire_to', '>=', $dueTomorrow)
                ->where('hire_to', '<', $dueTomorrow->copy()->addDay())
            )
            ->distinct()
            ->pluck('booking_id');

        if ($bookingIds->isEmpty()) {
            $this->line('  → 0 due-return alerts (none due tomorrow).');

            return;
        }

        $bookings = Booking::with('customer', 'items.inventoryItem', 'items.variant')
            ->whereIn('id', $bookingIds)
            ->get();

        $count = 0;
        foreach ($bookings as $booking) {
            $checkedOutCount = $booking->items->filter(fn ($i) => $i->status === 'checked_out')->count();
            $pendingReminder = DB::table('notifications')
                ->where('type', WhatsAppReturnReminder::class)
                ->whereDate('created_at', today())
                ->whereRaw("json_extract(data, '$.booking_id') = ?", [$booking->id])
                ->exists();

            if ($pendingReminder) {
                continue;
            }

            $balance = max(0, (float) $booking->total_amount - (float) $booking->payments()->sum('amount'));
            $balanceText = $balance > 0 ? ' Balance: UGX '.number_format($balance, 0).'.' : '';

            $notification = new BookingAlertNotification(
                bookingNumber: $booking->booking_number,
                message: "{$booking->customer?->name} has {$checkedOutCount} item(s) due for return tomorrow ({$booking->hire_to->format('d M')}). Use WhatsApp to send a reminder.{$balanceText}",
                bookingId: $booking->id,
                iconType: 'warning',
            );

            foreach ($recipients as $user) {
                $alreadySent = DB::table('notifications')
                    ->where('notifiable_id', $user->id)
                    ->where('type', BookingAlertNotification::class)
                    ->whereDate('created_at', today())
                    ->whereRaw("json_extract(data, '$.booking_id') = ?", [$booking->id])
                    ->exists();

                if (! $alreadySent) {
                    $user->notify($notification);
                    $count++;
                }
            }
        }

        $this->line("  → {$count} due-return alert(s) generated.");
    }
}

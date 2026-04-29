<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\SupplierInvoice;
use App\Notifications\BookingAlertNotification;
use App\Notifications\OverdueInvoiceNotification;
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
}

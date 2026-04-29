<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OverdueInvoiceNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $invoiceNumber,
        public readonly string $supplierName,
        public readonly float $amount,
        public readonly int $daysOverdue,
        public readonly int $invoiceId,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Overdue Invoice',
            'message' => "{$this->invoiceNumber} from {$this->supplierName} is {$this->daysOverdue} day(s) overdue. Amount: UGX ".number_format($this->amount, 0),
            'action_url' => route('invoices.show', $this->invoiceId),
            'icon_type' => 'warning',
            'invoice_id' => $this->invoiceId,
        ];
    }
}

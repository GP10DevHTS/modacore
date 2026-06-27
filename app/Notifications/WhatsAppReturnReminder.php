<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WhatsAppReturnReminder extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Booking $booking,
        public readonly string $messagePreview,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $balance = (float) $this->booking->total_amount - (float) $this->booking->payments()->sum('amount');

        return [
            'title' => 'WhatsApp Reminder Sent',
            'message' => "Reminder sent to {$this->booking->customer->name} for {$this->booking->booking_number}. Balance: UGX ".number_format(max(0, $balance), 0).'.',
            'action_url' => route('bookings.show', $this->booking->id),
            'icon_type' => 'success',
            'booking_id' => $this->booking->id,
            'type' => 'whatsapp_reminder',
        ];
    }
}

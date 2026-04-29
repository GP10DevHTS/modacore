<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $bookingNumber,
        public readonly string $message,
        public readonly int $bookingId,
        public readonly string $iconType = 'info',
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Booking Alert',
            'message' => $this->message,
            'action_url' => route('bookings.show', $this->bookingId),
            'icon_type' => $this->iconType,
            'booking_id' => $this->bookingId,
        ];
    }
}

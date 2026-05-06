<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification
{

    public function __construct(public readonly Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Cancelled – Labourix')
            ->line("Your booking for {$this->booking->jobListing->title} has been cancelled.")
            ->line("Reason: {$this->booking->cancellation_reason}");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id'          => $this->booking->id,
            'job_title'           => $this->booking->jobListing->title,
            'cancellation_reason' => $this->booking->cancellation_reason,
            'type'                => 'booking_cancelled',
        ];
    }
}

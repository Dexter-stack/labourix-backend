<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification
{

    public function __construct(public readonly Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Confirmed – Labourix')
            ->line("Great news! Your booking for **{$this->booking->jobListing->title}** has been confirmed by the employer.")
            ->line("Start date: {$this->booking->start_date->format('d M Y H:i')}")
            ->line("Agreed rate: £{$this->booking->agreed_hourly_rate}/hr")
            ->line('See you on the job!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id'   => $this->booking->id,
            'job_title'    => $this->booking->jobListing->title,
            'start_date'   => $this->booking->start_date,
            'confirmed_at' => $this->booking->confirmed_at,
            'type'         => 'booking_confirmed',
        ];
    }
}

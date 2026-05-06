<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingReceivedNotification extends Notification
{

    public function __construct(public readonly Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You\'ve Been Booked – Labourix')
            ->line("You have been selected for **{$this->booking->jobListing->title}**.")
            ->line("Start date: {$this->booking->start_date->format('d M Y H:i')}")
            ->line("Agreed rate: £{$this->booking->agreed_hourly_rate}/hr")
            ->line('The booking is pending confirmation from the employer. You will be notified once confirmed.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'job_title'  => $this->booking->jobListing->title,
            'start_date' => $this->booking->start_date,
            'type'       => 'booking_received',
        ];
    }
}

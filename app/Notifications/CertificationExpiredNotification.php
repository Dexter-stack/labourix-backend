<?php

namespace App\Notifications;

use App\Models\Certification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificationExpiredNotification extends Notification
{

    public function __construct(public readonly Certification $certification) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Certification Expired – Action Required')
            ->line("Your {$this->certification->name} certification has expired.")
            ->line('Please renew it to remain eligible for bookings.')
            ->action('Update Certifications', url('/api/v1/worker/certifications'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'certification_id'   => $this->certification->id,
            'certification_name' => $this->certification->name,
            'expired_at'         => $this->certification->expires_at,
            'type'               => 'certification_expired',
        ];
    }
}

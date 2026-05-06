<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserSuspendedNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Labourix Account Has Been Suspended')
            ->line('Your account has been suspended by an administrator.')
            ->line('While suspended, you can still log in to view your account but cannot perform any actions on the platform.')
            ->line('If you believe this is a mistake, please contact our support team.');
    }
}

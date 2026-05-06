<?php

namespace App\Notifications;

use App\Enums\OtpType;
use App\Models\OtpCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly OtpCode $otp) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = match ($this->otp->type) {
            OtpType::EmailVerification => 'Verify your Labourix account',
            OtpType::PasswordReset     => 'Reset your Labourix password',
        };

        $intro = match ($this->otp->type) {
            OtpType::EmailVerification => 'Thank you for registering. Use the code below to verify your email address.',
            OtpType::PasswordReset     => 'We received a request to reset your password. Use the code below.',
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello!')
            ->line($intro)
            ->line('Your one-time code is:')
            ->line('**' . $this->otp->code . '**')
            ->line('This code expires in 10 minutes.')
            ->line('If you did not request this, you can safely ignore this email.');
    }
}

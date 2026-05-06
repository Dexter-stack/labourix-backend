<?php

namespace App\Notifications;

use App\Models\JobListing;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobMatchedNotification extends Notification
{

    public function __construct(public readonly JobListing $job) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Job Match – Labourix')
            ->line("A new job matching your profile is available: {$this->job->title}")
            ->line("Location: {$this->job->location} | Rate: £{$this->job->hourly_rate}/hr")
            ->action('View Job', url("/api/v1/jobs/{$this->job->id}"));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'job_id'    => $this->job->id,
            'job_title' => $this->job->title,
            'location'  => $this->job->location,
            'type'      => 'job_matched',
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\JobApplication;
use App\Models\JobListing;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobApplicationNotification extends Notification
{

    public function __construct(
        public readonly JobApplication $application,
        public readonly JobListing $job,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $workerName = $this->application->worker->name;

        return (new MailMessage)
            ->subject("New Application for {$this->job->title} – Labourix")
            ->line("{$workerName} has applied for your job posting: **{$this->job->title}**.")
            ->when($this->application->cover_note, fn ($mail) => $mail
                ->line('Cover note:')
                ->line("\"{$this->application->cover_note}\"")
            )
            ->line('Log in to review the application and view the applicant\'s profile.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'job_id'         => $this->job->id,
            'job_title'      => $this->job->title,
            'worker_name'    => $this->application->worker->name,
            'type'           => 'job_application_received',
        ];
    }
}

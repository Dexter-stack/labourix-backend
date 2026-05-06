<?php

namespace App\Listeners;

use App\Events\JobApplicationSubmitted;
use App\Notifications\JobApplicationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyEmployerOfApplication implements ShouldQueue
{
    public function handle(JobApplicationSubmitted $event): void
    {
        $employer = $event->job->employer;

        $employer->notify(
            new JobApplicationNotification($event->application, $event->job)
        );
    }
}

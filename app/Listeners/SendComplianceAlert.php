<?php

namespace App\Listeners;

use App\Events\CertificationExpired;
use App\Notifications\CertificationExpiredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendComplianceAlert implements ShouldQueue
{
    public function handle(CertificationExpired $event): void
    {
        $event->certification->workerProfile->user->notify(
            new CertificationExpiredNotification($event->certification)
        );
    }
}

<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Notifications\BookingConfirmedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyWorkerOfConfirmation implements ShouldQueue
{
    public function handle(BookingConfirmed $event): void
    {
        $event->booking->worker->notify(
            new BookingConfirmedNotification($event->booking)
        );
    }
}

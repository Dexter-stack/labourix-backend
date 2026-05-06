<?php

namespace App\Listeners;

use App\Events\WorkerBooked;
use App\Notifications\BookingReceivedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyWorkerOfBooking implements ShouldQueue
{
    public function handle(WorkerBooked $event): void
    {
        $event->booking->worker->notify(
            new BookingReceivedNotification($event->booking)
        );
    }
}

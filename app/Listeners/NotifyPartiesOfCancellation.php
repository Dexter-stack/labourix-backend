<?php

namespace App\Listeners;

use App\Events\BookingCancelled;
use App\Notifications\BookingCancelledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyPartiesOfCancellation implements ShouldQueue
{
    public function handle(BookingCancelled $event): void
    {
        $booking = $event->booking;

        $booking->worker->notify(new BookingCancelledNotification($booking));
        $booking->employer->notify(new BookingCancelledNotification($booking));
    }
}

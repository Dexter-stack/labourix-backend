<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\BookingCancelledNotification;
use App\Notifications\JobMatchedNotification;

class NotificationService
{
    public function notifyBookingConfirmed(Booking $booking): void
    {
        $booking->worker->notify(new BookingConfirmedNotification($booking));
        $booking->employer->notify(new BookingConfirmedNotification($booking));
    }

    public function notifyBookingCancelled(Booking $booking): void
    {
        $booking->worker->notify(new BookingCancelledNotification($booking));
        $booking->employer->notify(new BookingCancelledNotification($booking));
    }

    public function notifyWorkerOfJobMatch(User $worker, \App\Models\JobListing $job): void
    {
        $worker->notify(new JobMatchedNotification($job));
    }
}

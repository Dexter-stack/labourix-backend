<?php

namespace App\Jobs;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\WorkerProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncWorkerAvailability implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Mark workers with active confirmed bookings as unavailable
        WorkerProfile::where('is_available', true)
            ->whereHas('user.bookingsAsWorker', function ($q) {
                $q->where('status', BookingStatus::Confirmed)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            })
            ->update(['is_available' => false]);

        // Re-enable workers whose bookings have all ended
        WorkerProfile::where('is_available', false)
            ->whereDoesntHave('user.bookingsAsWorker', function ($q) {
                $q->whereIn('status', [BookingStatus::Confirmed->value, BookingStatus::Pending->value])
                    ->where('end_date', '>=', now());
            })
            ->update(['is_available' => true]);
    }
}

<?php

namespace App\Listeners;

use App\Enums\ApplicationStatus;
use App\Events\BookingConfirmed;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateApplicationOnBookingConfirmed implements ShouldQueue
{
    public function __construct(
        private JobApplicationRepositoryInterface $applicationRepo,
    ) {}

    public function handle(BookingConfirmed $event): void
    {
        $booking = $event->booking;

        $application = $this->applicationRepo->findByJobAndWorker(
            $booking->job_listing_id,
            $booking->worker_id,
        );

        if ($application && $application->status === ApplicationStatus::Pending) {
            $this->applicationRepo->updateStatus($application, ApplicationStatus::Shortlisted);
        }
    }
}

<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Events\BookingCancelled;
use App\Events\BookingConfirmed;
use App\Events\WorkerBooked;
use App\Exceptions\ComplianceBlockException;
use App\Exceptions\WorkerUnavailableException;
use App\Models\Booking;
use App\Models\JobListing;
use App\Models\User;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\WorkerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        private BookingRepositoryInterface $bookingRepo,
        private WorkerRepositoryInterface $workerRepo,
        private ComplianceService $complianceService,
    ) {}

    public function createBooking(JobListing $job, User $worker, User $employer): Booking
    {
        return DB::transaction(function () use ($job, $worker, $employer) {
            $profile = $this->workerRepo->findProfileByUserId($worker->id);

            if (! $profile) {
                throw new WorkerUnavailableException('Worker has not set up a profile yet.');
            }

            if (! $profile->is_available) {
                throw new WorkerUnavailableException('Worker is not currently available.');
            }

            if ($this->bookingRepo->hasActiveBookingOverlap($worker->id, $job->start_date, $job->end_date)) {
                throw new WorkerUnavailableException('Worker already has a booking that overlaps this period.');
            }

            $this->complianceService->assertCompliant($profile, $job->required_certifications);

            $booking = $this->bookingRepo->create([
                'job_listing_id'     => $job->id,
                'worker_id'          => $worker->id,
                'employer_id'        => $employer->id,
                'status'             => BookingStatus::Pending,
                'start_date'         => $job->start_date,
                'end_date'           => $job->end_date,
                'agreed_hourly_rate' => $job->hourly_rate,
            ]);

            event(new WorkerBooked($booking));

            return $booking;
        });
    }

    public function confirmBooking(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {
            $confirmed = $this->bookingRepo->update($booking, [
                'status'       => BookingStatus::Confirmed,
                'confirmed_at' => now(),
            ]);

            event(new BookingConfirmed($confirmed));

            return $confirmed;
        });
    }

    public function cancelBooking(Booking $booking, string $reason): Booking
    {
        return DB::transaction(function () use ($booking, $reason) {
            $updated = $this->bookingRepo->update($booking, [
                'status'              => BookingStatus::Cancelled,
                'cancellation_reason' => $reason,
            ]);

            event(new BookingCancelled($updated));

            return $updated;
        });
    }

    public function completeBooking(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {
            return $this->bookingRepo->update($booking, [
                'status'       => BookingStatus::Completed,
                'completed_at' => now(),
            ]);
        });
    }

    public function getWorkerBookings(User $worker, int $perPage = 15): LengthAwarePaginator
    {
        return $this->bookingRepo->getWorkerBookings($worker->id, $perPage);
    }

    public function getEmployerBookings(User $employer, int $perPage = 15): LengthAwarePaginator
    {
        return $this->bookingRepo->getEmployerBookings($employer->id, $perPage);
    }

    public function getBooking(int $id): ?Booking
    {
        return $this->bookingRepo->findById($id);
    }
}

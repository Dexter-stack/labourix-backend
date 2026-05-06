<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\JobRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\WorkerRepositoryInterface;

class StatsService
{
    public function __construct(
        private BookingRepositoryInterface $bookingRepo,
        private JobRepositoryInterface $jobRepo,
        private WorkerRepositoryInterface $workerRepo,
        private UserRepositoryInterface $userRepo,
    ) {}

    public function workerStats(User $user): array
    {
        $profile = $this->workerRepo->findProfileByUserId($user->id);
        $bookingStats = $this->bookingRepo->workerBookingStats($user->id);

        $certs = $profile ? $profile->certifications : collect();
        $activeCerts   = $certs->filter(fn ($c) => ! $c->isExpired())->count();
        $expiringSoon  = $certs->filter(fn ($c) => $c->isExpiringSoon(30))->count();
        $expiredCerts  = $certs->filter(fn ($c) => $c->isExpired())->count();

        return [
            'bookings' => $bookingStats['counts'],
            'earnings' => [
                'total'      => $bookingStats['earnings_total'],
                'this_month' => $bookingStats['earnings_this_month'],
            ],
            'profile' => [
                'average_rating'       => $profile?->average_rating ?? '0.00',
                'total_jobs_completed' => $profile?->total_jobs_completed ?? 0,
                'is_available'         => $profile?->is_available ?? false,
            ],
            'certifications' => [
                'active'        => $activeCerts,
                'expiring_soon' => $expiringSoon,
                'expired'       => $expiredCerts,
            ],
        ];
    }

    public function employerStats(User $user): array
    {
        $bookingStats = $this->bookingRepo->employerBookingStats($user->id);
        $jobStats     = $this->jobRepo->employerJobStats($user->id);

        return [
            'jobs'                 => $jobStats,
            'bookings'             => $bookingStats['counts'],
            'spend'                => [
                'total'      => $bookingStats['spend_total'],
                'this_month' => $bookingStats['spend_this_month'],
            ],
            'unique_workers_hired' => $bookingStats['unique_workers_hired'],
        ];
    }

    public function adminStats(): array
    {
        $bookingStats = $this->bookingRepo->platformBookingStats();

        return [
            'users'    => $this->userRepo->platformUserStats(),
            'jobs'     => $this->jobRepo->platformJobStats(),
            'bookings' => $bookingStats,
            'platform' => [
                'workers_available'  => $this->workerRepo->countAvailableWorkers(),
                'compliance_alerts'  => $this->workerRepo->countWorkersWithExpiringCerts(30),
                'total_spend'        => $bookingStats['total_spend'],
            ],
        ];
    }
}

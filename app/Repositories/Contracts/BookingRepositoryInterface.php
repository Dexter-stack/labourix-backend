<?php

namespace App\Repositories\Contracts;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BookingRepositoryInterface
{
    public function create(array $data): Booking;

    public function findById(int $id): ?Booking;

    public function update(Booking $booking, array $data): Booking;

    public function getWorkerBookings(int $workerId, int $perPage = 15): LengthAwarePaginator;

    public function getEmployerBookings(int $employerId, int $perPage = 15): LengthAwarePaginator;

    public function hasActiveBookingOverlap(int $workerId, string $startDate, string $endDate, ?int $excludeId = null): bool;

    public function workerBookingStats(int $workerId): array;

    public function employerBookingStats(int $employerId): array;

    public function platformBookingStats(): array;
}

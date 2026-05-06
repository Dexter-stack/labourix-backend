<?php

namespace App\Repositories\Eloquent;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookingRepository implements BookingRepositoryInterface
{
    public function create(array $data): Booking
    {
        return Booking::create($data);
    }

    public function findById(int $id): ?Booking
    {
        return Booking::with(['jobListing', 'worker.workerProfile', 'employer', 'ratings'])->find($id);
    }

    public function update(Booking $booking, array $data): Booking
    {
        $booking->update($data);
        return $booking->fresh();
    }

    public function getWorkerBookings(int $workerId, int $perPage = 15): LengthAwarePaginator
    {
        return Booking::with(['jobListing', 'employer'])
            ->where('worker_id', $workerId)
            ->latest()
            ->paginate($perPage);
    }

    public function getEmployerBookings(int $employerId, int $perPage = 15): LengthAwarePaginator
    {
        return Booking::with(['jobListing', 'worker.workerProfile'])
            ->where('employer_id', $employerId)
            ->latest()
            ->paginate($perPage);
    }

    public function hasActiveBookingOverlap(int $workerId, string $startDate, string $endDate, ?int $excludeId = null): bool
    {
        $query = Booking::where('worker_id', $workerId)
            ->whereIn('status', [BookingStatus::Pending->value, BookingStatus::Confirmed->value])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function workerBookingStats(int $workerId): array
    {
        $counts = Booking::where('worker_id', $workerId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $earnings = Booking::where('worker_id', $workerId)
            ->where('status', BookingStatus::Completed->value)
            ->selectRaw("COALESCE(SUM(agreed_hourly_rate * TIMESTAMPDIFF(HOUR, start_date, end_date)), 0) as total")
            ->value('total');

        $earningsThisMonth = Booking::where('worker_id', $workerId)
            ->where('status', BookingStatus::Completed->value)
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->selectRaw("COALESCE(SUM(agreed_hourly_rate * TIMESTAMPDIFF(HOUR, start_date, end_date)), 0) as total")
            ->value('total');

        return [
            'counts' => [
                'pending'   => (int) ($counts[BookingStatus::Pending->value] ?? 0),
                'confirmed' => (int) ($counts[BookingStatus::Confirmed->value] ?? 0),
                'completed' => (int) ($counts[BookingStatus::Completed->value] ?? 0),
                'cancelled' => (int) ($counts[BookingStatus::Cancelled->value] ?? 0),
            ],
            'earnings_total'      => number_format((float) $earnings, 2, '.', ''),
            'earnings_this_month' => number_format((float) $earningsThisMonth, 2, '.', ''),
        ];
    }

    public function employerBookingStats(int $employerId): array
    {
        $counts = Booking::where('employer_id', $employerId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $spend = Booking::where('employer_id', $employerId)
            ->where('status', BookingStatus::Completed->value)
            ->selectRaw("COALESCE(SUM(agreed_hourly_rate * TIMESTAMPDIFF(HOUR, start_date, end_date)), 0) as total")
            ->value('total');

        $spendThisMonth = Booking::where('employer_id', $employerId)
            ->where('status', BookingStatus::Completed->value)
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->selectRaw("COALESCE(SUM(agreed_hourly_rate * TIMESTAMPDIFF(HOUR, start_date, end_date)), 0) as total")
            ->value('total');

        $uniqueWorkers = Booking::where('employer_id', $employerId)
            ->whereIn('status', [BookingStatus::Confirmed->value, BookingStatus::Completed->value])
            ->selectRaw('COUNT(DISTINCT worker_id) as count')
            ->value('count') ?? 0;

        return [
            'counts' => [
                'pending'   => (int) ($counts[BookingStatus::Pending->value] ?? 0),
                'confirmed' => (int) ($counts[BookingStatus::Confirmed->value] ?? 0),
                'completed' => (int) ($counts[BookingStatus::Completed->value] ?? 0),
                'cancelled' => (int) ($counts[BookingStatus::Cancelled->value] ?? 0),
            ],
            'spend_total'          => number_format((float) $spend, 2, '.', ''),
            'spend_this_month'     => number_format((float) $spendThisMonth, 2, '.', ''),
            'unique_workers_hired' => $uniqueWorkers,
        ];
    }

    public function platformBookingStats(): array
    {
        $counts = Booking::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $totalSpend = Booking::where('status', BookingStatus::Completed->value)
            ->selectRaw("COALESCE(SUM(agreed_hourly_rate * TIMESTAMPDIFF(HOUR, start_date, end_date)), 0) as total")
            ->value('total');

        return [
            'total'       => (int) array_sum($counts),
            'pending'     => (int) ($counts[BookingStatus::Pending->value] ?? 0),
            'confirmed'   => (int) ($counts[BookingStatus::Confirmed->value] ?? 0),
            'completed'   => (int) ($counts[BookingStatus::Completed->value] ?? 0),
            'cancelled'   => (int) ($counts[BookingStatus::Cancelled->value] ?? 0),
            'total_spend' => number_format((float) $totalSpend, 2, '.', ''),
        ];
    }
}

<?php

namespace App\Repositories\Eloquent;

use App\Enums\JobStatus;
use App\Models\JobListing;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class JobRepository implements JobRepositoryInterface
{
    public function create(array $data): JobListing
    {
        return JobListing::create($data);
    }

    public function findById(int $id): ?JobListing
    {
        return JobListing::with(['employer', 'bookings'])->find($id);
    }

    public function update(JobListing $job, array $data): JobListing
    {
        $job->update($data);
        return $job->fresh();
    }

    public function delete(JobListing $job): void
    {
        $job->delete();
    }

    public function findActiveByFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = JobListing::with('employer')
            ->where('status', JobStatus::Active);

        if (! empty($filters['trade'])) {
            $query->where('trade', $filters['trade']);
        }

        if (! empty($filters['location'])) {
            $query->where('location', 'ILIKE', "%{$filters['location']}%");
        }

        if (! empty($filters['skills'])) {
            foreach ($filters['skills'] as $skill) {
                $query->whereJsonContains('required_skills', $skill);
            }
        }

        if (! empty($filters['max_rate'])) {
            $query->where('hourly_rate', '<=', $filters['max_rate']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function findAvailableBySkills(array $skills, string $location): Collection
    {
        $query = JobListing::with(['employer'])
            ->where('status', JobStatus::Active)
            ->where('location', 'ILIKE', "%{$location}%");

        foreach ($skills as $skill) {
            $query->whereJsonContains('required_skills', $skill);
        }

        return $query->get();
    }

    public function getEmployerJobs(int $employerId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = JobListing::where('employer_id', $employerId)
            ->with('bookings');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['trade'])) {
            $query->where('trade', $filters['trade']);
        }

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'ILIKE', "%{$filters['search']}%")
                    ->orWhere('description', 'ILIKE', "%{$filters['search']}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function employerJobStats(int $employerId): array
    {
        $counts = JobListing::where('employer_id', $employerId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'draft'     => (int) ($counts[JobStatus::Draft->value] ?? 0),
            'active'    => (int) ($counts[JobStatus::Active->value] ?? 0),
            'filled'    => (int) ($counts[JobStatus::Filled->value] ?? 0),
            'cancelled' => (int) ($counts[JobStatus::Cancelled->value] ?? 0),
        ];
    }

    public function platformJobStats(): array
    {
        $counts = JobListing::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'total'     => (int) array_sum($counts),
            'draft'     => (int) ($counts[JobStatus::Draft->value] ?? 0),
            'active'    => (int) ($counts[JobStatus::Active->value] ?? 0),
            'filled'    => (int) ($counts[JobStatus::Filled->value] ?? 0),
            'cancelled' => (int) ($counts[JobStatus::Cancelled->value] ?? 0),
        ];
    }
}

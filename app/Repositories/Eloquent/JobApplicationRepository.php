<?php

namespace App\Repositories\Eloquent;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class JobApplicationRepository implements JobApplicationRepositoryInterface
{
    public function create(int $jobId, int $workerId, ?string $coverNote): JobApplication
    {
        return JobApplication::create([
            'job_listing_id' => $jobId,
            'worker_id'      => $workerId,
            'cover_note'     => $coverNote,
        ]);
    }

    public function hasApplied(int $jobId, int $workerId): bool
    {
        return JobApplication::where('job_listing_id', $jobId)
            ->where('worker_id', $workerId)
            ->exists();
    }

    public function findByJobAndWorker(int $jobId, int $workerId): ?JobApplication
    {
        return JobApplication::where('job_listing_id', $jobId)
            ->where('worker_id', $workerId)
            ->first();
    }

    public function updateStatus(JobApplication $application, ApplicationStatus $status): void
    {
        $application->update(['status' => $status]);
    }

    public function getWorkerApplications(int $workerId, int $perPage = 15): LengthAwarePaginator
    {
        return JobApplication::with('jobListing.employer')
            ->where('worker_id', $workerId)
            ->latest()
            ->paginate($perPage);
    }

    public function getJobApplications(int $jobId, int $perPage = 15): LengthAwarePaginator
    {
        return JobApplication::with(['worker.workerProfile'])
            ->where('job_listing_id', $jobId)
            ->latest()
            ->paginate($perPage);
    }
}

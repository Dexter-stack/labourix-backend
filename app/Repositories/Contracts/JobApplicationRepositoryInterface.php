<?php

namespace App\Repositories\Contracts;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface JobApplicationRepositoryInterface
{
    public function create(int $jobId, int $workerId, ?string $coverNote): JobApplication;

    public function hasApplied(int $jobId, int $workerId): bool;

    public function findByJobAndWorker(int $jobId, int $workerId): ?JobApplication;

    public function updateStatus(JobApplication $application, ApplicationStatus $status): void;

    public function getWorkerApplications(int $workerId, int $perPage = 15): LengthAwarePaginator;

    public function getJobApplications(int $jobId, int $perPage = 15): LengthAwarePaginator;
}

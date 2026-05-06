<?php

namespace App\Services;

use App\Enums\JobStatus;
use App\Events\JobApplicationSubmitted;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\User;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class JobApplicationService
{
    public function __construct(
        private JobApplicationRepositoryInterface $applicationRepo,
    ) {}

    public function apply(JobListing $job, User $worker, ?string $coverNote): JobApplication
    {
        if ($job->status !== JobStatus::Active) {
            throw ValidationException::withMessages([
                'job' => ['This job is not accepting applications.'],
            ]);
        }

        if ($this->applicationRepo->hasApplied($job->id, $worker->id)) {
            throw ValidationException::withMessages([
                'job' => ['You have already applied for this job.'],
            ]);
        }

        $application = $this->applicationRepo->create($job->id, $worker->id, $coverNote);

        event(new JobApplicationSubmitted($application->load('worker'), $job->load('employer')));

        return $application;
    }

    public function getWorkerApplications(User $worker, int $perPage = 15): LengthAwarePaginator
    {
        return $this->applicationRepo->getWorkerApplications($worker->id, $perPage);
    }

    public function getJobApplications(JobListing $job, int $perPage = 15): LengthAwarePaginator
    {
        return $this->applicationRepo->getJobApplications($job->id, $perPage);
    }
}

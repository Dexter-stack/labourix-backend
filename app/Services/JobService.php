<?php

namespace App\Services;

use App\Events\JobPosted;
use App\Models\JobListing;
use App\Models\User;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class JobService
{
    public function __construct(
        private JobRepositoryInterface $jobRepo,
    ) {}

    public function createJob(array $data, User $employer): JobListing
    {
        $job = $this->jobRepo->create(array_merge($data, [
            'employer_id' => $employer->id,
            'status'      => 'draft',
        ]));

        return $job;
    }

    public function publishJob(JobListing $job): JobListing
    {
        $updated = $this->jobRepo->update($job, ['status' => 'active']);
        event(new JobPosted($updated));
        return $updated;
    }

    public function updateJob(JobListing $job, array $data): JobListing
    {
        return $this->jobRepo->update($job, $data);
    }

    public function deleteJob(JobListing $job): void
    {
        $this->jobRepo->delete($job);
    }

    public function searchJobs(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->jobRepo->findActiveByFilters($filters, $perPage);
    }

    public function getEmployerJobs(int $employerId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->jobRepo->getEmployerJobs($employerId, $filters, $perPage);
    }

    public function getJob(int $id): ?JobListing
    {
        return $this->jobRepo->findById($id);
    }
}

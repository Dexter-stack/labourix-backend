<?php

namespace App\Repositories\Contracts;

use App\Models\JobListing;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface JobRepositoryInterface
{
    public function create(array $data): JobListing;

    public function findById(int $id): ?JobListing;

    public function update(JobListing $job, array $data): JobListing;

    public function delete(JobListing $job): void;

    public function findActiveByFilters(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function findAvailableBySkills(array $skills, string $location): Collection;

    public function getEmployerJobs(int $employerId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function employerJobStats(int $employerId): array;

    public function platformJobStats(): array;
}

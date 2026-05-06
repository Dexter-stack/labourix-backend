<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Database\Eloquent\Collection;

interface WorkerRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findProfileByUserId(int $userId): ?WorkerProfile;

    public function createProfile(int $userId, array $data): WorkerProfile;

    public function updateProfile(WorkerProfile $profile, array $data): WorkerProfile;

    public function findAvailableBySkillsAndLocation(array $skills, string $location): Collection;

    public function updateAvailability(WorkerProfile $profile, bool $isAvailable): WorkerProfile;

    public function recalculateAverageRating(WorkerProfile $profile): void;

    public function countAvailableWorkers(): int;

    public function countWorkersWithExpiringCerts(int $withinDays = 30): int;
}

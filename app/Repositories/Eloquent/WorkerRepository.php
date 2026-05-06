<?php

namespace App\Repositories\Eloquent;

use App\Models\Rating;
use App\Models\User;
use App\Models\WorkerProfile;
use App\Repositories\Contracts\WorkerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class WorkerRepository implements WorkerRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::with('workerProfile.certifications')->find($id);
    }

    public function findProfileByUserId(int $userId): ?WorkerProfile
    {
        return WorkerProfile::with('certifications')
            ->where('user_id', $userId)
            ->first();
    }

    public function createProfile(int $userId, array $data): WorkerProfile
    {
        return WorkerProfile::create(array_merge($data, ['user_id' => $userId]));
    }

    public function updateProfile(WorkerProfile $profile, array $data): WorkerProfile
    {
        $profile->update($data);
        return $profile->fresh(['certifications']);
    }

    public function findAvailableBySkillsAndLocation(array $skills, string $location): Collection
    {
        $query = WorkerProfile::with(['user', 'certifications'])
            ->where('is_available', true)
            ->where('location', 'ILIKE', "%{$location}%");

        foreach ($skills as $skill) {
            $query->whereJsonContains('skills', $skill);
        }

        return $query->get();
    }

    public function updateAvailability(WorkerProfile $profile, bool $isAvailable): WorkerProfile
    {
        $profile->update(['is_available' => $isAvailable]);
        return $profile;
    }

    public function recalculateAverageRating(WorkerProfile $profile): void
    {
        $avg = Rating::where('ratee_id', $profile->user_id)->avg('score') ?? 0;
        $profile->update(['average_rating' => round($avg, 2)]);
    }

    public function countAvailableWorkers(): int
    {
        return WorkerProfile::where('is_available', true)->count();
    }

    public function countWorkersWithExpiringCerts(int $withinDays = 30): int
    {
        return WorkerProfile::whereHas('certifications', function ($q) use ($withinDays) {
            $q->whereNotNull('expires_at')
                ->where('expires_at', '>', now())
                ->where('expires_at', '<=', now()->addDays($withinDays));
        })->count();
    }
}

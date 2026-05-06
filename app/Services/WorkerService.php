<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkerProfile;
use App\Repositories\Contracts\WorkerRepositoryInterface;
use Illuminate\Support\Facades\Broadcast;

class WorkerService
{
    public function __construct(
        private WorkerRepositoryInterface $workerRepo,
    ) {}

    public function getOrCreateProfile(User $user, array $data): WorkerProfile
    {
        $profile = $this->workerRepo->findProfileByUserId($user->id);

        if ($profile) {
            return $this->workerRepo->updateProfile($profile, $data);
        }

        return $this->workerRepo->createProfile($user->id, $data);
    }

    public function updateProfile(User $user, array $data): WorkerProfile
    {
        $profile = $this->workerRepo->findProfileByUserId($user->id);
        return $this->workerRepo->updateProfile($profile, $data);
    }

    public function setAvailability(User $user, bool $isAvailable): WorkerProfile
    {
        $profile = $this->workerRepo->findProfileByUserId($user->id);
        $updated = $this->workerRepo->updateAvailability($profile, $isAvailable);

        // Broadcast real-time availability change for frontend updates
        broadcast(new \App\Events\WorkerAvailabilityChanged($updated))->toOthers();

        return $updated;
    }

    public function getProfile(User $user): ?WorkerProfile
    {
        return $this->workerRepo->findProfileByUserId($user->id);
    }
}

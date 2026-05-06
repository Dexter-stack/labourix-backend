<?php

namespace App\Repositories\Contracts;

use App\Models\Certification;
use App\Models\WorkerProfile;
use Illuminate\Database\Eloquent\Collection;

interface CertificationRepositoryInterface
{
    public function allForProfile(WorkerProfile $profile): Collection;

    public function create(WorkerProfile $profile, array $data): Certification;

    public function findById(int $id): ?Certification;

    public function delete(Certification $certification): void;

    public function findExpiring(int $withinDays = 30): Collection;
}

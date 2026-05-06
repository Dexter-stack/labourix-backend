<?php

namespace App\Repositories\Eloquent;

use App\Models\Certification;
use App\Models\WorkerProfile;
use App\Repositories\Contracts\CertificationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CertificationRepository implements CertificationRepositoryInterface
{
    public function allForProfile(WorkerProfile $profile): Collection
    {
        return $profile->certifications()->latest()->get();
    }

    public function create(WorkerProfile $profile, array $data): Certification
    {
        return $profile->certifications()->create($data);
    }

    public function findById(int $id): ?Certification
    {
        return Certification::with('workerProfile')->find($id);
    }

    public function delete(Certification $certification): void
    {
        if ($certification->document_path) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($certification->document_path);
        }

        $certification->delete();
    }

    public function findExpiring(int $withinDays = 30): Collection
    {
        return Certification::with('workerProfile.user')
            ->whereNull('used_at')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', now()->addDays($withinDays))
            ->get();
    }
}

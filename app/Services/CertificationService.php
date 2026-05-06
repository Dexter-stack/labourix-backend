<?php

namespace App\Services;

use App\Events\CertificationExpired;
use App\Models\Certification;
use App\Models\User;
use App\Repositories\Contracts\CertificationRepositoryInterface;
use App\Repositories\Contracts\WorkerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CertificationService
{
    public function __construct(
        private CertificationRepositoryInterface $certRepo,
        private WorkerRepositoryInterface $workerRepo,
    ) {}

    public function listForUser(User $user): Collection
    {
        $profile = $this->resolveProfile($user);

        return $this->certRepo->allForProfile($profile);
    }

    public function addCertification(User $user, array $data, ?UploadedFile $document = null): Certification
    {
        return DB::transaction(function () use ($user, $data, $document) {
            $profile = $this->resolveProfile($user);

            if ($document) {
                $data['document_path'] = $document->store(
                    "certifications/{$profile->id}",
                    'local'
                );
            }

            return $this->certRepo->create($profile, $data);
        });
    }

    public function deleteCertification(User $user, Certification $certification): void
    {
        $this->assertOwnership($user, $certification);

        $this->certRepo->delete($certification);
    }

    /**
     * Called by a scheduled command to fire alerts for expiring/expired certs.
     */
    public function dispatchExpiryAlerts(): void
    {
        $expiring = $this->certRepo->findExpiring(
            config('labourix.compliance.expiry_warning_days', 30)
        );

        foreach ($expiring as $cert) {
            if ($cert->isExpired()) {
                event(new CertificationExpired($cert));
            }
        }
    }

    private function resolveProfile(User $user): \App\Models\WorkerProfile
    {
        $profile = $this->workerRepo->findProfileByUserId($user->id);

        if (! $profile) {
            throw ValidationException::withMessages([
                'profile' => ['Worker profile not found. Please complete your profile first.'],
            ]);
        }

        return $profile;
    }

    private function assertOwnership(User $user, Certification $certification): void
    {
        $profile = $this->workerRepo->findProfileByUserId($user->id);

        if (! $profile || $certification->worker_profile_id !== $profile->id) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'You do not own this certification.'
            );
        }
    }
}

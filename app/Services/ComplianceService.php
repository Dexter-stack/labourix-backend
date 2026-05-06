<?php

namespace App\Services;

use App\Exceptions\ComplianceBlockException;
use App\Models\WorkerProfile;
use Illuminate\Support\Carbon;

class ComplianceService
{
    /**
     * Throws ComplianceBlockException if the worker lacks any required certification.
     *
     * @param  string[]  $requiredCerts  Certification names required by the job
     */
    public function assertCompliant(WorkerProfile $profile, array $requiredCerts): void
    {
        if (empty($requiredCerts) || ! config('labourix.compliance.block_booking_on_expired_cert')) {
            return;
        }

        $workerCerts = $profile->certifications->keyBy('name');

        foreach ($requiredCerts as $certName) {
            $cert = $workerCerts->get($certName);

            if (! $cert) {
                throw new ComplianceBlockException("Worker is missing required certification: {$certName}.");
            }

            if ($cert->isExpired()) {
                throw new ComplianceBlockException("Worker's {$certName} certification has expired.");
            }
        }
    }

    public function getExpiringCertifications(WorkerProfile $profile): \Illuminate\Database\Eloquent\Collection
    {
        $warningDays = config('labourix.compliance.expiry_warning_days', 30);

        return $profile->certifications
            ->filter(fn ($cert) => $cert->isExpiringSoon($warningDays));
    }

    public function isFullyCompliantForJob(WorkerProfile $profile, array $requiredCerts): bool
    {
        try {
            $this->assertCompliant($profile, $requiredCerts);
            return true;
        } catch (ComplianceBlockException) {
            return false;
        }
    }
}

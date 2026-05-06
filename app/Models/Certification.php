<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certification extends Model
{
    protected $fillable = [
        'worker_profile_id',
        'name',
        'issuing_body',
        'certificate_number',
        'issued_at',
        'expires_at',
        'document_path',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expires_at' => 'date',
            'is_verified' => 'boolean',
        ];
    }

    public function workerProfile(): BelongsTo
    {
        return $this->belongsTo(WorkerProfile::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isExpiringSoon(int $withinDays = 30): bool
    {
        return $this->expires_at !== null
            && ! $this->isExpired()
            && $this->expires_at->diffInDays(now()) <= $withinDays;
    }
}

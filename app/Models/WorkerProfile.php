<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'trade',
        'skills',
        'location',
        'latitude',
        'longitude',
        'hourly_rate',
        'bio',
        'is_available',
        'availability_schedule',
        'average_rating',
        'total_jobs_completed',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'availability_schedule' => 'array',
            'is_available' => 'boolean',
            'hourly_rate' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }

    public function activeCertifications(): HasMany
    {
        return $this->hasMany(Certification::class)
            ->where('expires_at', '>', now())
            ->orWhereNull('expires_at');
    }
}

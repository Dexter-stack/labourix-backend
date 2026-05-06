<?php

namespace App\Models;

use App\Enums\JobStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobListing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employer_id',
        'title',
        'description',
        'trade',
        'required_skills',
        'required_certifications',
        'location',
        'latitude',
        'longitude',
        'hourly_rate',
        'start_date',
        'end_date',
        'workers_needed',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'required_skills' => 'array',
            'required_certifications' => 'array',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'hourly_rate' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'status' => JobStatus::class,
        ];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', JobStatus::Active);
    }

    public function scopeByTrade($query, string $trade)
    {
        return $query->where('trade', $trade);
    }

    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', 'LIKE', "%{$location}%");
    }
}

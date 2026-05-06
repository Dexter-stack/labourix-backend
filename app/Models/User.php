<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'suspended_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'suspended_at'      => 'datetime',
            'password'          => 'hashed',
            'role'              => UserRole::class,
        ];
    }

    public function workerProfile(): HasOne
    {
        return $this->hasOne(WorkerProfile::class);
    }

    public function jobListings(): HasMany
    {
        return $this->hasMany(JobListings::class, 'employer_id');
    }

    public function bookingsAsWorker(): HasMany
    {
        return $this->hasMany(Booking::class, 'worker_id');
    }

    public function bookingsAsEmployer(): HasMany
    {
        return $this->hasMany(Booking::class, 'employer_id');
    }

    public function ratingsGiven(): HasMany
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    public function ratingsReceived(): HasMany
    {
        return $this->hasMany(Rating::class, 'ratee_id');
    }

    public function demandForecasts(): HasMany
    {
        return $this->hasMany(WorkforceDemandForecast::class, 'employer_id');
    }

    public function isEmployer(): bool
    {
        return $this->role === UserRole::Employer;
    }

    public function isWorker(): bool
    {
        return $this->role === UserRole::Worker;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [UserRole::Admin, UserRole::SuperAdmin]);
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }
}

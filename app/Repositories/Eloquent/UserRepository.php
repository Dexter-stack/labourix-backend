<?php

namespace App\Repositories\Eloquent;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function platformUserStats(): array
    {
        $byRole = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        $newThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'total'          => (int) array_sum($byRole),
            'workers'        => (int) ($byRole[UserRole::Worker->value] ?? 0),
            'employers'      => (int) ($byRole[UserRole::Employer->value] ?? 0),
            'admins'         => (int) (($byRole[UserRole::Admin->value] ?? 0) + ($byRole[UserRole::SuperAdmin->value] ?? 0)),
            'new_this_month' => $newThisMonth,
        ];
    }
}

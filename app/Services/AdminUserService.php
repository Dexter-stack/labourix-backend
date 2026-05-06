<?php

namespace App\Services;

use App\Events\UserSuspended;
use App\Models\User;

class AdminUserService
{
    public function suspend(User $user): User
    {
        abort_if($user->isSuspended(), 409, 'User is already suspended.');

        $user->update(['suspended_at' => now()]);

        event(new UserSuspended($user->fresh()));

        return $user->fresh();
    }

    public function unsuspend(User $user): User
    {
        abort_if(! $user->isSuspended(), 409, 'User is not suspended.');

        $user->update(['suspended_at' => null]);

        return $user->fresh();
    }
}

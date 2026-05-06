<?php

namespace App\Listeners;

use App\Events\UserSuspended;
use App\Notifications\UserSuspendedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyUserOfSuspension implements ShouldQueue
{
    public function handle(UserSuspended $event): void
    {
        $event->user->notify(new UserSuspendedNotification());
    }
}

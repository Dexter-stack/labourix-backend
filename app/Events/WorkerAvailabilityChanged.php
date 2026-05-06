<?php

namespace App\Events;

use App\Models\WorkerProfile;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkerAvailabilityChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly WorkerProfile $profile) {}

    public function broadcastOn(): array
    {
        return [new Channel('workforce.availability')];
    }

    public function broadcastAs(): string
    {
        return 'worker.availability.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'worker_profile_id' => $this->profile->id,
            'user_id'           => $this->profile->user_id,
            'is_available'      => $this->profile->is_available,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $_request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'email'        => $this->email,
            'role'         => $this->role,
            'is_suspended' => $this->isSuspended(),
            'suspended_at' => $this->suspended_at,
            'created_at'   => $this->created_at,
            'profile'      => WorkerProfileResource::make($this->whenLoaded('workerProfile')),
        ];
    }
}

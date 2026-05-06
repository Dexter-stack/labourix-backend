<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'status'      => $this->status,
            'cover_note'  => $this->cover_note,
            'applied_at'  => $this->created_at,
            'job'         => JobListingResource::make($this->whenLoaded('jobListing')),
            'worker'      => WorkerProfileResource::make($this->whenLoaded('worker')),
        ];
    }
}

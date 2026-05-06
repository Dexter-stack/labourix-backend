<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'status'              => $this->status,
            'start_date'          => $this->start_date,
            'end_date'            => $this->end_date,
            'agreed_hourly_rate'  => $this->agreed_hourly_rate,
            'cancellation_reason' => $this->cancellation_reason,
            'confirmed_at'        => $this->confirmed_at,
            'completed_at'        => $this->completed_at,
            'job'                 => JobListingResource::make($this->whenLoaded('jobListing')),
            'worker'              => UserResource::make($this->whenLoaded('worker')),
            'employer'            => UserResource::make($this->whenLoaded('employer')),
            'created_at'          => $this->created_at,
        ];
    }
}

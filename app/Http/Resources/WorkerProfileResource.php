<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkerProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'trade'                 => $this->trade,
            'skills'                => $this->skills,
            'location'              => $this->location,
            'hourly_rate'           => $this->hourly_rate,
            'bio'                   => $this->bio,
            'is_available'          => $this->is_available,
            'availability_schedule' => $this->availability_schedule,
            'average_rating'        => $this->average_rating,
            'total_jobs_completed'  => $this->total_jobs_completed,
            'certifications'        => CertificationResource::collection($this->whenLoaded('certifications')),
            'match_score'           => $this->when(isset($this->match_score), $this->match_score),
        ];
    }
}

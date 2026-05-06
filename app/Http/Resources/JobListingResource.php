<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                       => $this->id,
            'title'                    => $this->title,
            'description'              => $this->description,
            'trade'                    => $this->trade,
            'required_skills'          => $this->required_skills,
            'required_certifications'  => $this->required_certifications,
            'location'                 => $this->location,
            'hourly_rate'              => $this->hourly_rate,
            'start_date'               => $this->start_date,
            'end_date'                 => $this->end_date,
            'workers_needed'           => $this->workers_needed,
            'status'                   => $this->status,
            'employer'                 => UserResource::make($this->whenLoaded('employer')),
            'created_at'               => $this->created_at,
        ];
    }
}

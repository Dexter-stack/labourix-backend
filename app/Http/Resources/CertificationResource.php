<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'issuing_body'      => $this->issuing_body,
            'certificate_number' => $this->certificate_number,
            'issued_at'         => $this->issued_at,
            'expires_at'        => $this->expires_at,
            'is_verified'       => $this->is_verified,
            'is_expired'        => $this->isExpired(),
        ];
    }
}

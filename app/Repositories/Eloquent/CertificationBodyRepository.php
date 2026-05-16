<?php

namespace App\Repositories\Eloquent;

use App\Models\CertificationBody;
use App\Repositories\Contracts\CertificationBodyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CertificationBodyRepository implements CertificationBodyRepositoryInterface
{
    public function all(): Collection
    {
        return CertificationBody::active()->orderBy('name')->get();
    }
}

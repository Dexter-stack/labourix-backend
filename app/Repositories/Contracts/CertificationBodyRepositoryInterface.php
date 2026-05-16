<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface CertificationBodyRepositoryInterface
{
    public function all(): Collection;
}

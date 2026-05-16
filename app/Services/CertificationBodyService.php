<?php

namespace App\Services;

use App\Repositories\Contracts\CertificationBodyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CertificationBodyService
{
    public function __construct(private CertificationBodyRepositoryInterface $bodyRepo) {}

    public function list(): Collection
    {
        return $this->bodyRepo->all();
    }
}

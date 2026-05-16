<?php

namespace App\Http\Controllers;

use App\Http\Resources\CertificationBodyResource;
use App\Services\CertificationBodyService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CertificationBodyController extends Controller
{
    use ApiResponse;

    public function __construct(private CertificationBodyService $bodyService) {}

    public function index(): JsonResponse
    {
        $bodies = $this->bodyService->list();

        return $this->success(
            CertificationBodyResource::collection($bodies),
            'Certification bodies retrieved.'
        );
    }
}

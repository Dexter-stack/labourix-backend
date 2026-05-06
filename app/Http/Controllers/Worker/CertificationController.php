<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\StoreCertificationRequest;
use App\Http\Resources\CertificationResource;
use App\Models\Certification;
use App\Services\CertificationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificationController extends Controller
{
    use ApiResponse;

    public function __construct(private CertificationService $certService) {}

    public function index(Request $request): JsonResponse
    {
        $certifications = $this->certService->listForUser($request->user());

        return $this->success(
            CertificationResource::collection($certifications),
            'Certifications retrieved.'
        );
    }

    public function store(StoreCertificationRequest $request): JsonResponse
    {
        $certification = $this->certService->addCertification(
            $request->user(),
            $request->safe()->except('document'),
            $request->file('document'),
        );

        return $this->created(
            new CertificationResource($certification),
            'Certification added successfully.'
        );
    }

    public function destroy(Request $request, Certification $certification): JsonResponse
    {
        $this->certService->deleteCertification($request->user(), $certification);

        return $this->noContent();
    }
}

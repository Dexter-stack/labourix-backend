<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Services\WorkforceOptimisationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkforceOptimisationController extends Controller
{
    use ApiResponse;

    public function __construct(private WorkforceOptimisationService $optimisationService) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->optimisationService->getOptimisationData($request->user());

        return $this->success($data, 'Workforce optimisation data retrieved.');
    }
}

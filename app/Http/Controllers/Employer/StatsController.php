<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Services\StatsService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    use ApiResponse;

    public function __construct(private StatsService $statsService) {}

    public function __invoke(Request $request): JsonResponse
    {
        return $this->success(
            $this->statsService->employerStats($request->user()),
            'Employer stats retrieved.'
        );
    }
}

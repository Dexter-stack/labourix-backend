<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StatsService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    use ApiResponse;

    public function __construct(private StatsService $statsService) {}

    public function __invoke(): JsonResponse
    {
        return $this->success(
            $this->statsService->adminStats(),
            'Admin stats retrieved.'
        );
    }
}

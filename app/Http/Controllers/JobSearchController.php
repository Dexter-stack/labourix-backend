<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Resources\JobListingResource;
use App\Services\JobService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobSearchController extends Controller
{
    use ApiResponse;

    public function __construct(private JobService $jobService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['trade', 'location', 'skills', 'max_rate']);

        $user = auth('sanctum')->user();
        if ($user && $user->role === UserRole::Worker) {
            $filters['exclude_worker_id'] = $user->id;
        }

        $jobs = $this->jobService->searchJobs($filters);

        return $this->success(JobListingResource::collection($jobs), 'Jobs retrieved.');
    }
}

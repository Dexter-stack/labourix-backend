<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\ApplyForJobRequest;
use App\Http\Resources\JobApplicationResource;
use App\Models\JobListing;
use App\Services\JobApplicationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    use ApiResponse;

    public function __construct(private JobApplicationService $applicationService) {}

    public function index(Request $request): JsonResponse
    {
        $applications = $this->applicationService->getWorkerApplications(
            $request->user(),
            (int) $request->input('per_page', 15)
        );

        return $this->success(JobApplicationResource::collection($applications), 'Applications retrieved.');
    }

    public function store(ApplyForJobRequest $request, JobListing $job): JsonResponse
    {
        $application = $this->applicationService->apply(
            $job,
            $request->user(),
            $request->input('cover_note')
        );

        return $this->created(
            new JobApplicationResource($application->load('jobListing')),
            'Application submitted successfully.'
        );
    }
}

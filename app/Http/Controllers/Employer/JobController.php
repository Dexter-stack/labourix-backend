<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Job\PostJobRequest;
use App\Http\Requests\Job\UpdateJobRequest;
use App\Http\Resources\JobApplicationResource;
use App\Http\Resources\JobListingResource;
use App\Http\Resources\WorkerProfileResource;
use App\Models\JobListing;
use App\Services\JobApplicationService;
use App\Services\JobService;
use App\Services\MatchingService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    use ApiResponse;

    public function __construct(
        private JobService $jobService,
        private MatchingService $matchingService,
        private JobApplicationService $applicationService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'trade', 'search']);
        $perPage = (int) $request->input('per_page', 15);

        $jobs = $this->jobService->getEmployerJobs($request->user()->id, $filters, $perPage);

        return $this->success(JobListingResource::collection($jobs), 'Jobs retrieved.');
    }

    public function store(PostJobRequest $request): JsonResponse
    {
        $job = $this->jobService->createJob($request->validated(), $request->user());

        return $this->created(new JobListingResource($job), 'Job created successfully.');
    }

    public function show(JobListing $job): JsonResponse
    {
        return $this->success(
            new JobListingResource($job->load('employer', 'bookings')),
            'Job retrieved.'
        );
    }

    public function update(UpdateJobRequest $request, JobListing $job): JsonResponse
    {
        $updated = $this->jobService->updateJob($job, $request->validated());

        return $this->success(new JobListingResource($updated), 'Job updated successfully.');
    }

    public function destroy(JobListing $job): JsonResponse
    {
        $this->jobService->deleteJob($job);

        return $this->noContent();
    }

    public function publish(JobListing $job): JsonResponse
    {
        $published = $this->jobService->publishJob($job);

        return $this->success(new JobListingResource($published), 'Job published successfully.');
    }

    public function matchedWorkers(JobListing $job): JsonResponse
    {
        $workers = $this->matchingService->getRankedWorkersForJob($job);

        return $this->success(
            WorkerProfileResource::collection($workers),
            'Matched workers retrieved.'
        );
    }

    public function applications(Request $request, JobListing $job): JsonResponse
    {
        $applications = $this->applicationService->getJobApplications(
            $job,
            (int) $request->input('per_page', 15)
        );

        return $this->success(
            JobApplicationResource::collection($applications),
            'Applicants retrieved.'
        );
    }
}

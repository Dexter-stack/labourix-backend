<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\UpdateProfileRequest;
use App\Http\Resources\WorkerProfileResource;
use App\Services\WorkerService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(private WorkerService $workerService) {}

    public function show(Request $request): JsonResponse
    {
        $profile = $this->workerService->getProfile($request->user());

        return $this->success(
            new WorkerProfileResource($profile->load('certifications')),
            'Profile retrieved.'
        );
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $profile = $this->workerService->getOrCreateProfile($request->user(), $request->validated());

        return $this->success(
            new WorkerProfileResource($profile->load('certifications')),
            'Profile updated successfully.'
        );
    }

    public function setAvailability(Request $request): JsonResponse
    {
        $request->validate(['is_available' => ['required', 'boolean']]);

        $profile = $this->workerService->setAvailability(
            $request->user(),
            $request->boolean('is_available')
        );

        return $this->success(
            new WorkerProfileResource($profile),
            'Availability updated.'
        );
    }
}

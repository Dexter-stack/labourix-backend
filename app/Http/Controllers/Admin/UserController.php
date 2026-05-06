<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AdminUserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(private AdminUserService $adminUserService) {}

    public function index(Request $request): JsonResponse
    {
        $users = User::with('workerProfile')
            ->when($request->input('role'), fn ($q, $role) => $q->where('role', $role))
            ->paginate(20);

        return $this->success(UserResource::collection($users), 'Users retrieved.');
    }

    public function show(User $user): JsonResponse
    {
        return $this->success(
            new UserResource($user->load('workerProfile.certifications')),
            'User retrieved.'
        );
    }

    public function suspend(User $user): JsonResponse
    {
        $updated = $this->adminUserService->suspend($user);

        return $this->success(new UserResource($updated), 'User suspended.');
    }

    public function unsuspend(User $user): JsonResponse
    {
        $updated = $this->adminUserService->unsuspend($user);

        return $this->success(new UserResource($updated), 'User unsuspended.');
    }
}

<?php

use App\Http\Controllers\Admin\StatsController as AdminStatsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Employer\BookingController as EmployerBookingController;
use App\Http\Controllers\Employer\JobController as EmployerJobController;
use App\Http\Controllers\Employer\StatsController as EmployerStatsController;
use App\Http\Controllers\JobSearchController;
use App\Http\Controllers\Worker\BookingController as WorkerBookingController;
use App\Http\Controllers\Worker\CertificationController as WorkerCertificationController;
use App\Http\Controllers\Worker\JobApplicationController as WorkerJobApplicationController;
use App\Http\Controllers\Worker\ProfileController as WorkerProfileController;
use App\Http\Controllers\Worker\StatsController as WorkerStatsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1 Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public auth routes
    Route::prefix('auth')->group(function () {
        // Registration
        Route::post('register',    [AuthController::class, 'register']);
        Route::post('verify-email', [AuthController::class, 'verifyEmail']);
        Route::post('resend-otp',  [AuthController::class, 'resendOtp']);

        // Login
        Route::post('login', [AuthController::class, 'login']);

        // Forgot password (3-step flow)
        Route::post('forgot-password',    [AuthController::class, 'forgotPassword']);
        Route::post('verify-reset-otp',   [AuthController::class, 'verifyResetOtp']);
        Route::post('reset-password',     [AuthController::class, 'resetPassword']);
    });

    Route::get('jobs', [JobSearchController::class, 'index']);

    // Authenticated
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        // Employer routes
        Route::prefix('employer')->middleware(['role:employer', 'suspended'])->group(function () {
            Route::get('stats', EmployerStatsController::class);

            Route::apiResource('jobs', EmployerJobController::class);
            Route::post('jobs/{job}/publish', [EmployerJobController::class, 'publish']);
            Route::get('jobs/{job}/matched-workers', [EmployerJobController::class, 'matchedWorkers']);
            Route::get('jobs/{job}/applications', [EmployerJobController::class, 'applications']);

            Route::get('bookings', [EmployerBookingController::class, 'index']);
            Route::post('bookings', [EmployerBookingController::class, 'store']);
            Route::get('bookings/{booking}', [EmployerBookingController::class, 'show']);
            Route::post('bookings/{booking}/confirm', [EmployerBookingController::class, 'confirm']);
            Route::post('bookings/{booking}/cancel', [EmployerBookingController::class, 'cancel']);
        });

        // Worker routes
        Route::prefix('worker')->middleware(['role:worker', 'suspended'])->group(function () {
            Route::get('stats', WorkerStatsController::class);

            Route::get('profile', [WorkerProfileController::class, 'show']);
            Route::put('profile', [WorkerProfileController::class, 'update']);
            Route::post('profile/availability', [WorkerProfileController::class, 'setAvailability']);

            Route::get('bookings', [WorkerBookingController::class, 'index']);
            Route::get('bookings/{booking}', [WorkerBookingController::class, 'show']);
            Route::post('bookings/{booking}/cancel', [WorkerBookingController::class, 'cancel']);

            Route::get('certifications', [WorkerCertificationController::class, 'index']);
            Route::post('certifications', [WorkerCertificationController::class, 'store']);
            Route::delete('certifications/{certification}', [WorkerCertificationController::class, 'destroy']);

            Route::get('applications', [WorkerJobApplicationController::class, 'index']);
            Route::post('jobs/{job}/apply', [WorkerJobApplicationController::class, 'store']);
        });

        // Admin routes
        Route::prefix('admin')->middleware(['role:admin,super_admin', 'suspended'])->group(function () {
            Route::get('stats', AdminStatsController::class);

            Route::get('users', [AdminUserController::class, 'index']);
            Route::get('users/{user}', [AdminUserController::class, 'show']);
            Route::post('users/{user}/suspend', [AdminUserController::class, 'suspend']);
            Route::post('users/{user}/unsuspend', [AdminUserController::class, 'unsuspend']);
        });

    });

});

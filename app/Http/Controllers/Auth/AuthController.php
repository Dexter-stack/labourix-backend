<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Requests\Auth\VerifyResetOtpRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        return $this->created(
            new UserResource($user),
            'Account created. Please check your email for a 6-digit verification code.'
        );
    }

    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        ['user' => $user, 'token' => $token] = $this->authService->verifyEmail(
            $request->email,
            $request->code,
        );

        return $this->success(
            ['user' => new UserResource($user), 'token' => $token],
            'Email verified successfully.'
        );
    }

    public function resendOtp(ResendOtpRequest $request): JsonResponse
    {
        $this->authService->resendVerificationOtp($request->email);

        return $this->success(message: 'A new verification code has been sent to your email.');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        ['user' => $user, 'token' => $token] = $this->authService->login($request->validated());

        return $this->success(
            ['user' => new UserResource($user), 'token' => $token],
            'Logged in successfully.'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->success(message: 'Logged out successfully.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(
            new UserResource($request->user()->load('workerProfile')),
            'Authenticated user retrieved.'
        );
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->authService->sendPasswordResetOtp($request->email);

        // Always 200 regardless of whether email exists — prevents enumeration
        return $this->success(message: 'If that email is registered, a reset code has been sent.');
    }

    public function verifyResetOtp(VerifyResetOtpRequest $request): JsonResponse
    {
        $resetToken = $this->authService->verifyResetOtp($request->email, $request->code);

        return $this->success(
            ['reset_token' => $resetToken],
            'OTP verified. Use the reset token to set your new password.'
        );
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $this->authService->resetPassword($request->reset_token, $request->password);

        return $this->success(message: 'Password reset successfully. Please log in.');
    }
}

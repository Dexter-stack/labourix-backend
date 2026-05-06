<?php

namespace App\Services;

use App\Enums\OtpType;
use App\Enums\UserRole;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(private OtpService $otpService) {}

    // -------------------------------------------------------------------------
    // Registration
    // -------------------------------------------------------------------------

    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'],
                'role'     => UserRole::from($data['role'] ?? UserRole::Worker->value),
            ]);

            $this->otpService->sendOtp($user->email, OtpType::EmailVerification);

            return $user;
        });
    }

    public function verifyEmail(string $email, string $code): array
    {
        return DB::transaction(function () use ($email, $code) {
            $user = $this->findUser($email);

            if ($user->hasVerifiedEmail()) {
                throw ValidationException::withMessages([
                    'email' => ['Email is already verified.'],
                ]);
            }

            $this->otpService->consumeOtp($email, $code, OtpType::EmailVerification);

            $user->markEmailAsVerified();

            $token = $user->createToken('api')->plainTextToken;

            return compact('user', 'token');
        });
    }

    public function resendVerificationOtp(string $email): void
    {
        $user = $this->findUser($email);

        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['Email is already verified.'],
            ]);
        }

        $this->otpService->sendOtp($email, OtpType::EmailVerification);
    }

    // -------------------------------------------------------------------------
    // Login / Logout
    // -------------------------------------------------------------------------

    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        if (! $user->hasVerifiedEmail()) {
            $this->otpService->sendOtp($user->email, OtpType::EmailVerification);

            throw ValidationException::withMessages([
                'email' => ['Your email is not verified. A new verification code has been sent.'],
            ]);
        }

        $token = $user->createToken('api')->plainTextToken;

        return compact('user', 'token');
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    // -------------------------------------------------------------------------
    // Forgot password
    // -------------------------------------------------------------------------

    public function sendPasswordResetOtp(string $email): void
    {
        $user = User::where('email', $email)->first();

        // Silently return — prevents email enumeration
        if (! $user) {
            return;
        }

        $this->otpService->sendOtp($email, OtpType::PasswordReset);
    }

    public function verifyResetOtp(string $email, string $code): string
    {
        $this->findUser($email);

        return $this->otpService->exchangeForResetToken($email, $code);
    }

    public function resetPassword(string $resetToken, string $newPassword): void
    {
        DB::transaction(function () use ($resetToken, $newPassword) {
            $otp = OtpCode::where('reset_token', $resetToken)
                ->where('type', OtpType::PasswordReset)
                ->first();

            if (! $otp || $otp->expires_at->addMinutes(30)->isPast()) {
                throw ValidationException::withMessages([
                    'reset_token' => ['Invalid or expired reset token.'],
                ]);
            }

            $user = User::where('email', $otp->email)->firstOrFail();
            $user->update(['password' => $newPassword]);

            $otp->update(['reset_token' => null]);
            $user->tokens()->delete();
        });
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function findUser(string $email): User
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['No account found with that email address.'],
            ]);
        }

        return $user;
    }
}

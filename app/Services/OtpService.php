<?php

namespace App\Services;

use App\Enums\OtpType;
use App\Models\OtpCode;
use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OtpService
{
    private const TTL_MINUTES    = 10;
    private const RESEND_SECONDS = 60;

    public function sendOtp(string $email, OtpType $type): OtpCode
    {
        $this->enforceResendCooldown($email, $type);

        // Invalidate any unused previous OTPs of the same type
        OtpCode::where('email', $email)
            ->where('type', $type)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $otp = OtpCode::create([
            'email'      => $email,
            'type'       => $type,
            'code'       => $this->generateCode(),
            'expires_at' => now()->addMinutes(self::TTL_MINUTES),
        ]);

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->notify(new OtpNotification($otp));
        }

        return $otp;
    }

    public function verifyOtp(string $email, string $code, OtpType $type): OtpCode
    {
        $otp = OtpCode::where('email', $email)
            ->where('type', $type)
            ->where('code', $code)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (! $otp || $otp->isExpired()) {
            throw ValidationException::withMessages([
                'code' => ['Invalid or expired OTP.'],
            ]);
        }

        return $otp;
    }

    /**
     * Verify OTP and immediately mark it as used. Returns the OTP record.
     */
    public function consumeOtp(string $email, string $code, OtpType $type): OtpCode
    {
        $otp = $this->verifyOtp($email, $code, $type);
        $otp->markUsed();
        return $otp;
    }

    /**
     * Verify OTP, mark used, store a reset token, and return the token.
     * Used in the forgot-password flow so the client can exchange OTP for a short-lived reset token.
     */
    public function exchangeForResetToken(string $email, string $code): string
    {
        $otp = $this->consumeOtp($email, $code, OtpType::PasswordReset);

        $resetToken = Str::random(64);
        $otp->update(['reset_token' => $resetToken]);

        return $resetToken;
    }

    private function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function enforceResendCooldown(string $email, OtpType $type): void
    {
        $recent = OtpCode::where('email', $email)
            ->where('type', $type)
            ->where('created_at', '>=', now()->subSeconds(self::RESEND_SECONDS))
            ->exists();

        if ($recent) {
            throw ValidationException::withMessages([
                'email' => ['Please wait before requesting a new OTP.'],
            ]);
        }
    }
}

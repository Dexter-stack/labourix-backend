<?php

namespace App\Services;

use App\AI\Contracts\AIProviderInterface;
use App\Models\User;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WorkforceOptimisationService
{
    public function __construct(
        private AIProviderInterface $ai,
        private JobRepositoryInterface $jobRepo,
        private BookingRepositoryInterface $bookingRepo,
    ) {}

    public function getOptimisationData(User $employer): array
    {
        $jobStats     = $this->jobRepo->employerJobStats($employer->id);
        $bookingStats = $this->bookingRepo->employerBookingStats($employer->id);
        $utilisation  = $this->bookingRepo->employerTradeUtilisation($employer->id);

        $recommendations = $this->generateRecommendations($employer->id, $jobStats, $bookingStats, $utilisation);

        return [
            'recommendations'     => $recommendations,
            'utilisation_by_trade' => $utilisation,
        ];
    }

    private function generateRecommendations(int $employerId, array $jobStats, array $bookingStats, array $utilisation): array
    {
        $cacheKey = "workforce_opt_recommendations_{$employerId}";
        $ttl      = now()->addHours(2);

        try {
            return Cache::remember($cacheKey, $ttl, function () use ($jobStats, $bookingStats, $utilisation) {
                $content = $this->ai->chat([
                    [
                        'role'    => 'system',
                        'content' => 'You are a workforce optimisation AI for a construction staffing platform. '
                            . 'Analyse the employer\'s workforce data and return ONLY a valid JSON array of recommendation objects. '
                            . 'Each object must have exactly these keys: '
                            . '"type" (string: coverage_gap|overutilised|cost_optimisation|compliance_risk|high_cancellation|pending_action), '
                            . '"priority" (string: high|medium|low), '
                            . '"title" (string, max 60 chars), '
                            . '"description" (string, max 200 chars, actionable advice). '
                            . 'Return between 0 and 6 recommendations. No markdown, no explanation, just the JSON array.',
                    ],
                    [
                        'role'    => 'user',
                        'content' => "Analyse this workforce data and return optimisation recommendations.\n\n"
                            . "Job stats:\n" . json_encode($jobStats)
                            . "\n\nBooking stats:\n" . json_encode($bookingStats)
                            . "\n\nUtilisation by trade:\n" . json_encode($utilisation),
                    ],
                ]);

                $decoded = json_decode($content, true);

                return is_array($decoded) ? $decoded : [];
            });
        } catch (\Throwable $e) {
            Log::warning('AI workforce optimisation unavailable, using rule-based fallback', ['error' => $e->getMessage()]);
            return $this->ruleBasedRecommendations($bookingStats, $utilisation);
        }
    }

    private function ruleBasedRecommendations(array $bookingStats, array $utilisation): array
    {
        $recommendations = [];

        foreach ($utilisation as $row) {
            if ($row['utilisation_rate'] < 50 && $row['workers_needed'] > 0) {
                $recommendations[] = [
                    'type'        => 'coverage_gap',
                    'priority'    => 'high',
                    'title'       => "Coverage gap: {$row['trade']}",
                    'description' => "Only {$row['workers_booked']} of {$row['workers_needed']} {$row['trade']} roles are filled. "
                        . "Review your active listings or adjust requirements to attract more applicants.",
                ];
            } elseif ($row['utilisation_rate'] >= 90) {
                $recommendations[] = [
                    'type'        => 'overutilised',
                    'priority'    => 'medium',
                    'title'       => "High demand: {$row['trade']}",
                    'description' => "{$row['trade']} roles are {$row['utilisation_rate']}% utilised. "
                        . "Consider posting additional listings to maintain cover for upcoming projects.",
                ];
            }
        }

        if (($bookingStats['counts']['pending'] ?? 0) > 2) {
            $recommendations[] = [
                'type'        => 'pending_action',
                'priority'    => 'medium',
                'title'       => 'Pending bookings require confirmation',
                'description' => "You have {$bookingStats['counts']['pending']} bookings awaiting confirmation. "
                    . "Confirm or cancel them promptly to keep workers' availability accurate.",
            ];
        }

        $cancelled  = $bookingStats['counts']['cancelled'] ?? 0;
        $completed  = $bookingStats['counts']['completed'] ?? 0;
        if ($cancelled > 0 && $completed > 0 && ($cancelled / ($cancelled + $completed)) > 0.3) {
            $recommendations[] = [
                'type'        => 'high_cancellation',
                'priority'    => 'high',
                'title'       => 'High booking cancellation rate',
                'description' => 'More than 30% of your bookings have been cancelled. '
                    . 'Review job requirements and timelines to reduce last-minute cancellations.',
            ];
        }

        return $recommendations;
    }
}

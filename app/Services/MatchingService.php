<?php

namespace App\Services;

use App\AI\Contracts\AIProviderInterface;
use App\Models\JobListing;
use App\Repositories\Contracts\WorkerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class MatchingService
{
    public function __construct(
        private WorkerRepositoryInterface $workerRepo,
        private ComplianceService $complianceService,
        private AIProviderInterface $ai,
    ) {}

    public function getRankedWorkersForJob(JobListing $job): Collection
    {
        $candidates = $this->workerRepo->findAvailableBySkillsAndLocation(
            $job->required_skills,
            $job->location
        );

        $candidates = $candidates->filter(fn ($profile) =>
            $this->complianceService->isFullyCompliantForJob($profile, $job->required_certifications)
        );

        try {
            return $this->scoreViaAi($job, $candidates);
        } catch (\Throwable $e) {
            Log::warning('AI matching unavailable, using rule-based fallback', ['error' => $e->getMessage()]);
            return $this->ruleBasedScore($job, $candidates);
        }
    }

    private function scoreViaAi(JobListing $job, Collection $candidates): Collection
    {
        $candidateList = $candidates->map(fn ($p) => [
            'worker_profile_id' => $p->id,
            'skills'            => $p->skills,
            'trade'             => $p->trade,
            'location'          => $p->location,
            'average_rating'    => $p->average_rating,
            'jobs_completed'    => $p->jobs_completed,
        ])->values()->toArray();

        $content = $this->ai->chat([
            [
                'role'    => 'system',
                'content' => 'You are a workforce matching AI for a construction platform. '
                    . 'Return ONLY a valid JSON array of worker_profile_id integers, ranked best-first. '
                    . 'No explanation, no markdown, just the JSON array.',
            ],
            [
                'role'    => 'user',
                'content' => 'Rank these candidates for the following job.'
                    . "\n\nJob:\n" . json_encode($job->only([
                        'title', 'trade', 'location', 'required_skills',
                        'required_certifications', 'hourly_rate',
                    ]))
                    . "\n\nCandidates:\n" . json_encode($candidateList),
            ],
        ]);

        $rankedIds = collect(json_decode($content, true));

        return $candidates->sortBy(fn ($profile) =>
            $rankedIds->search($profile->id) !== false
                ? $rankedIds->search($profile->id)
                : PHP_INT_MAX
        )->values();
    }

    private function ruleBasedScore(JobListing $job, Collection $candidates): Collection
    {
        $weights = config('labourix.matching.weights');

        return $candidates->map(function ($profile) use ($job, $weights) {
            $skillScore  = $this->skillMatchScore($profile->skills, $job->required_skills);
            $ratingScore = min($profile->average_rating / 5, 1.0);

            $profile->match_score = round(
                ($skillScore * $weights['skill_match']) + ($ratingScore * $weights['rating']),
                4
            );

            return $profile;
        })->sortByDesc('match_score')->values();
    }

    private function skillMatchScore(array $workerSkills, array $requiredSkills): float
    {
        if (empty($requiredSkills)) {
            return 1.0;
        }

        $matched = \count(array_intersect($workerSkills, $requiredSkills));
        return $matched / \count($requiredSkills);
    }
}

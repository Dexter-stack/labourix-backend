<?php

namespace App\Services;

use App\Models\JobListing;
use App\Repositories\Contracts\WorkerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MatchingService
{
    public function __construct(
        private WorkerRepositoryInterface $workerRepo,
        private ComplianceService $complianceService,
    ) {}

    /**
     * Returns a ranked collection of available workers for a job.
     * Calls Python AI microservice; falls back to rule-based scoring on failure.
     */
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
            return $this->scoreViaAiService($job, $candidates);
        } catch (\Throwable $e) {
            Log::warning('AI matching service unavailable, using rule-based fallback', ['error' => $e->getMessage()]);
            return $this->ruleBasedScore($job, $candidates);
        }
    }

    private function scoreViaAiService(JobListing $job, Collection $candidates): Collection
    {
        $url     = config('labourix.ai.matching_url');
        $timeout = config('labourix.ai.timeout', 5);

        $response = Http::timeout($timeout)->post($url, [
            'job'        => $job->toArray(),
            'candidates' => $candidates->map->toArray()->values(),
        ]);

        $response->throw();

        $ranked = collect($response->json('ranked_candidates'));

        return $candidates->sortBy(fn ($profile) =>
            $ranked->search(fn ($r) => $r['worker_profile_id'] === $profile->id)
        )->values();
    }

    private function ruleBasedScore(JobListing $job, Collection $candidates): Collection
    {
        $weights = config('labourix.matching.weights');

        return $candidates->map(function ($profile) use ($job, $weights) {
            $skillScore = $this->skillMatchScore($profile->skills, $job->required_skills);
            $ratingScore = min($profile->average_rating / 5, 1.0);

            $score = ($skillScore * $weights['skill_match'])
                + ($ratingScore * $weights['rating']);

            $profile->match_score = round($score, 4);
            return $profile;
        })->sortByDesc('match_score')->values();
    }

    private function skillMatchScore(array $workerSkills, array $requiredSkills): float
    {
        if (empty($requiredSkills)) {
            return 1.0;
        }

        $matched = count(array_intersect($workerSkills, $requiredSkills));
        return $matched / count($requiredSkills);
    }
}

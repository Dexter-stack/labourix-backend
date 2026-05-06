<?php

namespace App\Listeners;

use App\Events\JobPosted;
use App\Notifications\JobMatchedNotification;
use App\Repositories\Contracts\WorkerRepositoryInterface;
use App\Services\MatchingService;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyMatchedWorkers implements ShouldQueue
{
    public function __construct(
        private MatchingService $matchingService,
    ) {}

    public function handle(JobPosted $event): void
    {
        $rankedWorkers = $this->matchingService->getRankedWorkersForJob($event->job);

        $rankedWorkers->take(10)->each(function ($profile) use ($event) {
            $profile->user->notify(new JobMatchedNotification($event->job));
        });
    }
}

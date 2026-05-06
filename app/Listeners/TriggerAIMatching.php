<?php

namespace App\Listeners;

use App\Events\JobPosted;
use App\Jobs\RunAIMatching;
use Illuminate\Contracts\Queue\ShouldQueue;

class TriggerAIMatching implements ShouldQueue
{
    public function handle(JobPosted $event): void
    {
        RunAIMatching::dispatch($event->job);
    }
}

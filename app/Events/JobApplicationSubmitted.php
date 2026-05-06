<?php

namespace App\Events;

use App\Models\JobApplication;
use App\Models\JobListing;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobApplicationSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly JobApplication $application,
        public readonly JobListing $job,
    ) {}
}

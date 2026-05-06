<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\DemandForecastService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunDemandForecast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public readonly User $employer,
        public readonly array $params,
    ) {}

    public function handle(DemandForecastService $forecastService): void
    {
        $forecastService->generateForecast($this->employer, $this->params);
    }
}

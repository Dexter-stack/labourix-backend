<?php

namespace App\Events;

use App\Models\WorkforceDemandForecast;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DemandForecastGenerated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly WorkforceDemandForecast $forecast) {}
}

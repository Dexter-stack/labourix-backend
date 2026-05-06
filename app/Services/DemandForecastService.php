<?php

namespace App\Services;

use App\Events\DemandForecastGenerated;
use App\Models\User;
use App\Models\WorkforceDemandForecast;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DemandForecastService
{
    public function generateForecast(User $employer, array $params): WorkforceDemandForecast
    {
        $forecastData = $this->fetchForecastFromAi($params);

        $forecast = WorkforceDemandForecast::create([
            'employer_id'      => $employer->id,
            'trade'            => $params['trade'],
            'location'         => $params['location'] ?? null,
            'forecast_date'    => $forecastData['forecast_date'],
            'predicted_demand' => $forecastData['predicted_demand'],
            'current_supply'   => $forecastData['current_supply'] ?? 0,
            'inputs'           => $params,
            'confidence_score' => $forecastData['confidence_score'] ?? null,
        ]);

        event(new DemandForecastGenerated($forecast));

        return $forecast;
    }

    private function fetchForecastFromAi(array $params): array
    {
        $url     = config('labourix.ai.forecast_url');
        $timeout = config('labourix.ai.timeout', 5);

        try {
            $response = Http::timeout($timeout)->post($url, $params);
            $response->throw();
            return $response->json();
        } catch (\Throwable $e) {
            Log::warning('AI forecast service unavailable, using basic estimate', ['error' => $e->getMessage()]);
            return $this->basicForecastFallback($params);
        }
    }

    private function basicForecastFallback(array $params): array
    {
        return [
            'forecast_date'    => now()->addDays(7)->toDateString(),
            'predicted_demand' => $params['project_size'] ?? 1,
            'current_supply'   => 0,
            'confidence_score' => 0.5,
        ];
    }
}

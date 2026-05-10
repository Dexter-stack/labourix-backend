<?php

namespace App\Services;

use App\AI\Contracts\AIProviderInterface;
use App\Events\DemandForecastGenerated;
use App\Models\User;
use App\Models\WorkforceDemandForecast;
use Illuminate\Support\Facades\Log;

class DemandForecastService
{
    public function __construct(private AIProviderInterface $ai) {}

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
        try {
            $content = $this->ai->chat([
                [
                    'role'    => 'system',
                    'content' => 'You are a workforce demand forecasting AI for construction projects. '
                        . 'Return ONLY a valid JSON object with these exact keys: '
                        . 'forecast_date (ISO date string), predicted_demand (integer), '
                        . 'current_supply (integer), confidence_score (float 0–1). '
                        . 'No explanation, no markdown, just the JSON object.',
                ],
                [
                    'role'    => 'user',
                    'content' => 'Generate a workforce demand forecast for the following project parameters:'
                        . "\n\n" . json_encode($params),
                ],
            ]);

            return json_decode($content, true);
        } catch (\Throwable $e) {
            Log::warning('AI forecast unavailable, using basic estimate', ['error' => $e->getMessage()]);
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

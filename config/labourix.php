<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Matching Engine Scoring Weights
    |--------------------------------------------------------------------------
    | Weights must sum to 1.0. Adjust in .env or override here.
    */
    'matching' => [
        'weights' => [
            'skill_match'  => (float) env('MATCH_WEIGHT_SKILL', 0.40),
            'proximity'    => (float) env('MATCH_WEIGHT_PROXIMITY', 0.20),
            'availability' => (float) env('MATCH_WEIGHT_AVAILABILITY', 0.20),
            'rating'       => (float) env('MATCH_WEIGHT_RATING', 0.20),
        ],
        'fallback_rule_based' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Microservice Endpoints
    |--------------------------------------------------------------------------
    */
    'ai' => [
        'matching_url' => env('AI_MATCHING_URL', 'http://ai-service:8001/api/match'),
        'forecast_url' => env('AI_FORECAST_URL', 'http://ai-service:8001/api/forecast'),
        'timeout'      => (int) env('AI_TIMEOUT', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compliance Rules
    |--------------------------------------------------------------------------
    */
    'compliance' => [
        'block_booking_on_expired_cert' => true,
        'expiry_warning_days' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Workforce Sharing
    |--------------------------------------------------------------------------
    */
    'workforce_sharing' => [
        'enabled' => env('WORKFORCE_SHARING_ENABLED', true),
        'max_transfer_days' => 90,
    ],

];

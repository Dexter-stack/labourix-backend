<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkforceDemandForecast extends Model
{
    protected $fillable = [
        'employer_id',
        'trade',
        'location',
        'forecast_date',
        'predicted_demand',
        'current_supply',
        'inputs',
        'confidence_score',
    ];

    protected function casts(): array
    {
        return [
            'forecast_date' => 'date',
            'inputs' => 'array',
            'confidence_score' => 'decimal:4',
        ];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function getShortageAttribute(): int
    {
        return max(0, $this->predicted_demand - $this->current_supply);
    }
}

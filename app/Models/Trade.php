<?php

namespace App\Models;

use App\Enums\TradeCategory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'category'  => TradeCategory::class,
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, TradeCategory $category)
    {
        return $query->where('category', $category->value);
    }
}

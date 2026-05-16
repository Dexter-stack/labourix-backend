<?php

namespace App\Services;

use App\Enums\TradeCategory;
use App\Repositories\Contracts\TradeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TradeService
{
    public function __construct(private TradeRepositoryInterface $tradeRepo) {}

    public function list(?string $category = null): Collection
    {
        if ($category !== null) {
            $enum = TradeCategory::tryFrom($category);

            if ($enum === null) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'category' => ['Invalid category. Must be construction_building or engineering_technical.'],
                ]);
            }

            return $this->tradeRepo->allByCategory($enum);
        }

        return $this->tradeRepo->all();
    }
}

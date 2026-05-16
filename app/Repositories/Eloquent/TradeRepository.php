<?php

namespace App\Repositories\Eloquent;

use App\Enums\TradeCategory;
use App\Models\Trade;
use App\Repositories\Contracts\TradeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TradeRepository implements TradeRepositoryInterface
{
    public function all(): Collection
    {
        return Trade::active()->orderBy('category')->orderBy('name')->get();
    }

    public function allByCategory(TradeCategory $category): Collection
    {
        return Trade::active()->byCategory($category)->orderBy('name')->get();
    }
}

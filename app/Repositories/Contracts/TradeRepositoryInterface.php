<?php

namespace App\Repositories\Contracts;

use App\Enums\TradeCategory;
use Illuminate\Database\Eloquent\Collection;

interface TradeRepositoryInterface
{
    public function all(): Collection;

    public function allByCategory(TradeCategory $category): Collection;
}

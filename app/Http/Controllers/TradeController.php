<?php

namespace App\Http\Controllers;

use App\Http\Resources\TradeResource;
use App\Services\TradeService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    use ApiResponse;

    public function __construct(private TradeService $tradeService) {}

    public function index(Request $request): JsonResponse
    {
        $trades = $this->tradeService->list($request->query('category'));

        return $this->success(
            TradeResource::collection($trades),
            'Trades retrieved.'
        );
    }
}

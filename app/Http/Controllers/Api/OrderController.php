<?php

namespace App\Http\Controllers\Api;

use App\Http\Services\OrderService;
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function placeOrder(OrderRequest $request, OrderService $orderService): JsonResponse
    {
        return $orderService->handle($request);
    }
}

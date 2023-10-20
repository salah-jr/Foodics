<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $request->validate([
           'products' => 'required|array',
           'products.*.product_id' => 'required|exists:products,id',
           'products.*.quantity' => 'required|integer|min:1',
        ]);


    }
}

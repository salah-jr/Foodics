<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function placeOrder(OrderRequest $request)
    {
        $products = $request->get('products');

        //        DB::beginTransaction();

        //        $order = Order::create();

        foreach ($products as $productDetails) {

            $product = Product::findOrFail($productDetails['product_id']);
            $quantity = $productDetails['quantity'];

            foreach ($product->ingredients as $ingredient) {
               $totalUsedQuantityInGrams = $ingredient->pivot->quantity * $quantity;
               $totalQuantityInKilograms = $totalUsedQuantityInGrams / 1000;
            }
        }
    }
}

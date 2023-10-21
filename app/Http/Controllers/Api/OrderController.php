<?php

namespace App\Http\Controllers\Api;

use App\Models\IngredientStockHistory;
use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function placeOrder(OrderRequest $request)
    {
        $products = $request->get('products');
        $order = Order::create();

        try {

            DB::beginTransaction();
            foreach ($products as $productDetails) {

                $product = Product::with('ingredients')->findOrFail($productDetails['product_id']);
                $quantity = $productDetails['quantity'];
                $order->products()->attach($product->id, ['quantity' => $quantity]);

                foreach ($product->ingredients as $ingredient) {
                    $totalUsedQuantityInGrams = $ingredient->pivot->quantity * $quantity;

                    $totalQuantityInKilograms = $totalUsedQuantityInGrams / 1000;

                    if ($ingredient->available_stock < $totalQuantityInKilograms) {
                        return response()->json(['error' => 'Insufficient stock for ' . $ingredient->name], 400);
                    }

                    $newAvailableStock = $ingredient->available_stock - $totalQuantityInKilograms;
                    $emailSent = false;

                    if ($ingredient->available_stock < $ingredient->stock * 0.5) {
                        $emailSent = true;
                    }

                    if (!$emailSent) {
                        // Todo:: send email notification to the merchant
                    }

                    $ingredient->update([
                        'available_stock' => $newAvailableStock
                    ]);
                }
            }

            $order->update([
               'status' => Order::$success,
            ]);

            DB::commit();

            return response()->json(['message' => 'Order processed successfully'], 200);
        } catch (Exception $e) {
            DB::rollBack();

            $order->update([
                'status' => Order::$failed,
            ]);

            return response()->json(['error' => 'An error occurred while processing the order'], 500);
        }

    }
}

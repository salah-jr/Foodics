<?php

namespace App\Http\Services;

use App\Http\Requests\OrderRequest;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use http\Client\Request;
use http\Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function handle(OrderRequest $request) {
        $products = $request->get('products');
        $order = Order::create();

        try {
            DB::beginTransaction();

            foreach ($products as $productDetails) {
                $product = Product::with('ingredients')->findOrFail($productDetails['product_id']);
                $quantity = $productDetails['quantity'];
                $order->products()->attach($product->id, ['quantity' => $quantity]);

                foreach ($product->ingredients as $ingredient) {
                    $requiredIngredientInGrams = $ingredient->pivot->quantity * $quantity;
                    $requiredIngredientInKilograms = $requiredIngredientInGrams / 1000;

                    if ($ingredient->available_stock < $requiredIngredientInKilograms) {
                        return response()->json(['error' => 'Insufficient stock for ' . $ingredient->name], 400);
                    }

                    $emailSent = false;

                    // Email already sent before
                    if ($ingredient->available_stock < $ingredient->stock * 0.5) $emailSent = true;

                    $newAvailableStock = $ingredient->available_stock - $requiredIngredientInKilograms;

                    if (!$emailSent && $newAvailableStock < $ingredient->stock * 0.5) {
                        $this->sendEmail($ingredient);
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

            return response()->json(['message' => 'Order placed successfully'], 200);

        } catch (Exception $e) {
            DB::rollBack();

            $order->update([
                'status' => Order::$failed,
            ]);

            return response()->json(['error' => 'An error occurred while processing the order'], 500);
        }
    }

    private function sendEmail(Ingredient $ingredient) {
        // Todo:: Send email notification tot he merchant
    }
}

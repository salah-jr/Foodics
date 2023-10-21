<?php

namespace App\Http\Services;

use App\Http\Requests\OrderRequest;
use App\Mail\IngredientLowStockMail;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use http\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderService
{
    public function handle(OrderRequest $request): \Illuminate\Http\JsonResponse
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
                    $requiredIngredientGm = $ingredient->pivot->quantity * $quantity;
                    $requiredIngredientInKg = $requiredIngredientGm / 1000;

                    if ($ingredient->available_stock < $requiredIngredientInKg) {
                        return response()->json(['error' => 'Insufficient stock for ' . $ingredient->name], 400);
                    }

                    $newAvailableStock = $ingredient->available_stock - $requiredIngredientInKg;
                    $emailSent = false;

                    if ($ingredient->available_stock < $ingredient->stock * 0.5) $emailSent = true;

                    /**
                     * When `$emailSent = true` means that the email is already sent before,
                     * Alternatives: We could store the sending email status in the cache or inside the DB.
                     */
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

            return response()->json([
                'order_id' => $order->id,
                'message' => 'Order placed successfully'
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            $order->update([
                'status' => Order::$failed,
            ]);

            return response()->json(['error' => 'An error occurred while processing the order'], 500);
        }
    }

    private function sendEmail(Ingredient $ingredient): void
    {
        Mail::send(new IngredientLowStockMail($ingredient));
    }
}

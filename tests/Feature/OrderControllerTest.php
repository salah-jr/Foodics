<?php

namespace Tests\Feature;

use App\Mail\IngredientLowStockMail;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function itPassesValidationForValidData()
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $data = [
            'products' => [
                ['product_id' => $product1->id, 'quantity' => 2],
                ['product_id' => $product2->id, 'quantity' => 3],
            ],
        ];

        $response = $this->postJson('/api/place-order', $data);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function itFailsValidationForMissingProductID()
    {
        $data = [
            'products' => [
                ['quantity' => 2],
            ],
        ];

        $response = $this->postJson('/api/place-order', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['products.0.product_id']);
    }

    /**
     * @test
     */
    public function itFailsValidationForInvalidProductID()
    {
        $data = [
            'products' => [
                ['product_id' => 999, 'quantity' => 2],
            ],
        ];

        $response = $this->postJson('/api/place-order', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['products.0.product_id']);
    }

    /**
     * @test
     */
    public function itFailsValidationForNegativeQuantity()
    {
        $data = [
            'products' => [
                ['product_id' => 1, 'quantity' => -1],
            ],
        ];

        $response = $this->postJson('/api/place-order', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['products.0.quantity']);
    }

    /**
     * @test
     */
    public function itFailsValidationForNonIntegerQuantity()
    {
        $data = [
            'products' => [
                ['product_id' => 1, 'quantity' => 'not_an_integer'],
            ],
        ];

        $response = $this->postJson('/api/place-order', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['products.0.quantity']);
    }

    /**
     * @test
     */
    public function itPlacesAnOrder()
    {
        $product = Product::factory()->create();

        $ingredient1 = Ingredient::factory()->create();

        $product->ingredients()->attach($ingredient1, ['quantity' => 250]);

        $response = $this->postJson('/api/place-order', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 3],
            ],
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'id' => $response->json('order_id')
        ]);

        $this->assertDatabaseHas('order_products', [
            'order_id' => $response->json('order_id'),
            'product_id' => $product->id
        ]);
    }

    /**
     * @test
     */
    public function itFailsToPlaceAnOrderDueToInsufficientStock()
    {
        $product = Product::factory()->create();
        $ingredient = Ingredient::factory()->create(['stock' => 1.0, 'available_stock' => 1.0]); // 1 KG
        $product->ingredients()->attach($ingredient, ['quantity' => 1500]); // 1.5 KG

        $response = $this->postJson('/api/place-order', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Insufficient stock for ' . $ingredient->name]);
    }

    /**
     * @test
     */
    public function itDeductsAvailableStockAfterPlacingAnOrder()
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $ingredient1 = Ingredient::factory()->create([
            'stock' => 2.0, // 2 KG
            'available_stock' => 2.0, // 2 KG
        ]);

        $ingredient2 = Ingredient::factory()->create([
            'stock' => 4.0, // 2 KG
            'available_stock' => 4.0, // 2 KG
        ]);

        $product1->ingredients()->attach($ingredient1, ['quantity' => 500]); // 0.5 KG
        $product2->ingredients()->attach($ingredient1, ['quantity' => 1000]); // 1 KG
        $product2->ingredients()->attach($ingredient2, ['quantity' => 200]); // 0.2 KG

        $response = $this->postJson('/api/place-order', [
            'products' => [
                ['product_id' => $product1->id, 'quantity' => 1],
                ['product_id' => $product2->id, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(200);

        $updatedIngredient1 = Ingredient::find($ingredient1->id);
        $updatedIngredient2 = Ingredient::find($ingredient2->id);

        // Assuming (1000g +500g) / 1000 = 1.5 kg      (deducted from 2.0 KG)
        $this->assertEquals(0.5, $updatedIngredient1->available_stock);

        // Assuming 200g / 1000 = 0.2 kg               (deducted from 4.0 KG)
        $this->assertEquals(3.8, $updatedIngredient2->available_stock);
    }

    /**
     * @test
     */
    public function itSendsAnEmailWhenAvailableStockReachesBelowFiftyPercent()
    {
        Mail::fake();

        $product = Product::factory()->create();

        $ingredient = Ingredient::factory()->create([
            'stock' => 2.0, // 2 KG
            'available_stock' => 2.0, // 2 KG
        ]);

        $product->ingredients()->attach($ingredient, ['quantity' => 1500]); // 1.5 KG

        $response = $this->postJson('/api/place-order', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(200);

        Mail::assertSent(IngredientLowStockMail::class);
    }

    /**
     * @test
     */
    public function itDontSendAnEmailIfIsSentBefore()
    {
        Mail::fake();

        $product = Product::factory()->create();

        $ingredient = Ingredient::factory()->create([
            'stock' => 2.0, // 2 KG
            'available_stock' => 2.0, // 2 KG
        ]);

        $product->ingredients()->attach($ingredient, ['quantity' => 600]); // 0.6 KG

        $response = $this->postJson('/api/place-order', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $response->assertStatus(200);

        Mail::assertSent(IngredientLowStockMail::class);

        $product2 = Product::factory()->create();
        $product2->ingredients()->attach($ingredient, ['quantity' => 500]); // 0.5 KG

        $response = $this->postJson('/api/place-order', [
            'products' => [
                ['product_id' => $product2->id, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(200);

        Mail::fake();

        Mail::assertNotSent(IngredientLowStockMail::class);
    }
}

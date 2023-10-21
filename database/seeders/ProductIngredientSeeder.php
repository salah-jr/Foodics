<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductIngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Burger Ingredients
        Product::find(1)->ingredients()->attach([
            1 => ['quantity' => 150],   // Beef
            2 => ['quantity' => 30],    // Cheese
            3 => ['quantity' => 20],    // Onion
        ]);

//        // Pizza Ingredients
//        Product::find(2)->ingredients()->attach([
//            1 => ['quantity' => 200],   // Beef
//            2 => ['quantity' => 50],    // Cheese
//            4 => ['quantity' => 20],    // Tomato
//            7 => ['quantity' => 10],    // Bacon
//            8 => ['quantity' => 5],     // Pickles
//        ]);
//
//        // Pasta Ingredients
//        Product::find(3)->ingredients()->attach([
//            1 => ['quantity' => 100],   // Beef
//            5 => ['quantity' => 30],    // Mushroom
//            6 => ['quantity' => 20],    // Lettuce
//            9 => ['quantity' => 10],    // Pepper
//        ]);
    }
}

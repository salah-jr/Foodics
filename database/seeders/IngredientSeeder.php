<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ingredient::create(['name' => 'Beef', 'stock' => 20.0]);
        Ingredient::create(['name' => 'Cheese', 'stock' => 5.0]);
        Ingredient::create(['name' => 'Onion', 'stock' => 1.0]);
        Ingredient::create(['name' => 'Tomato', 'stock' => 2.5]);
        Ingredient::create(['name' => 'Mushroom', 'stock' => 3.0]);
        Ingredient::create(['name' => 'Lettuce', 'stock' => 1.8]);
        Ingredient::create(['name' => 'Bacon', 'stock' => 4.0]);
        Ingredient::create(['name' => 'Pickles', 'stock' => 1.2]);
        Ingredient::create(['name' => 'Pepper', 'stock' => 0.5]);
        Ingredient::create(['name' => 'Mustard', 'stock' => 0.7]);
        Ingredient::create(['name' => 'Ketchup', 'stock' => 1.2]);
        Ingredient::create(['name' => 'Garlic', 'stock' => 2.0]);
    }
}

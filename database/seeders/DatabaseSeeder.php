<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductIngredient;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ProductSeeder::class);
        $this->call(IngredientSeeder::class);
        $this->call(ProductIngredientSeeder::class);
    }
}

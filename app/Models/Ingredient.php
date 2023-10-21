<?php

namespace App\Models;

use JetBrains\PhpStorm\NoReturn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ingredient extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (Ingredient $ingredient) {
            IngredientStockHistory::create([
               'ingredient_id' => $ingredient->id,
               'stock' => $ingredient->stock
            ]);
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, ProductIngredient::class)
            ->withPivot('quantity');
    }
}

<?php

namespace App\Models;

use JetBrains\PhpStorm\NoReturn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ingredient extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function products()
    {
        return $this->belongsToMany(Product::class, ProductIngredient::class)
            ->withPivot('quantity');
    }
}

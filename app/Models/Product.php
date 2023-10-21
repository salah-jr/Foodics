<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, ProductIngredient::class)
            ->withPivot('quantity');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, OrderProduct::class)
            ->withPivot('quantity')->withTimestamps();
    }
}

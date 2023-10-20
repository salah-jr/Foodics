<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class)
            ->withPivot('quantity'); // The quantity of this ingredient in the product
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('quantity'); // The quantity of the product in the order
    }
}

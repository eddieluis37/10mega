<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Dish extends Model
{
    protected $fillable = ['name','code','description','price','image','status'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'dish_product')
                    ->withPivot('quantity','unitofmeasure_id')
                    ->withTimestamps();
    }
}
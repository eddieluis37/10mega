<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Unitofmeasure extends Model
{

    protected $table = 'unitsofmeasures';
    // Campos que se pueden asignar masivamente
    protected $fillable = ['name', 'description', 'status'];

    /**
     * Relación con productos: un unitOfMeasure puede usarse en muchos productos
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Si quieres además exponer la relación en platos (dish_product pivot),
     * podrías añadir:
     *
     * 
     */

    public function dishIngredients(): BelongsToMany
    {
        return $this->belongsToMany(Dish::class, 'dish_product')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}

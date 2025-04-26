<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Combo extends Model
{
    protected $fillable = ['name','code','description','price','status'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'combo_product')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}
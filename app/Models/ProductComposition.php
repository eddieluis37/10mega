<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productcomposition extends Model
{
    protected $table = 'product_compositions';
    protected $fillable = ['product_id', 'component_id', 'quantity'];

    public function component()
    {
        return $this->belongsTo(Product::class, 'component_id');
    }
}

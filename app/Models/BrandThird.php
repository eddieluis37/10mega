<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandThird extends Model
{
    use HasFactory;

    protected $table = 'brand_third';

    protected $fillable = ['name', 'third_id', 'brand_id'];

    // Relación inversa: cada registro de BrandThird pertenece a una Marca
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // Relación inversa: cada registro de BrandThird pertenece a un Proveedor (Third)
    public function provider()
    {
        return $this->belongsTo(Third::class, 'third_id');
    }
}

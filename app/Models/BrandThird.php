<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandThird extends Model
{
    use HasFactory;

    protected $table = 'brand_third';

    protected $fillable = ['name', 'brand_id'];

     // Relación: cada registro pertenece a una Marca
     public function brand()
     {
         return $this->belongsTo(Brand::class);
     }
 
     // Relación muchos a muchos con proveedores (Third) a través de la tabla pivote 'brand_third_third'
     public function thirds()
     {
         return $this->belongsToMany(Third::class, 'brand_third_third', 'brand_third_id', 'third_id')
                     ->withTimestamps();
     }
}

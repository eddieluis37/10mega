<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'status'];

    // Relación uno a muchos con la tabla pivote (BrandThird)
    public function brandThirds()
    {
        return $this->hasMany(BrandThird::class);
    }

    // Relación muchos a muchos con Proveedores (Third) a través de la tabla intermedia 'brand_third'
    public function providers()
    {
        return $this->belongsToMany(Third::class, 'brand_third', 'brand_id', 'third_id')
            ->withPivot('id', 'name')
            ->withTimestamps();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use HasFactory;

    protected $table = 'lotes';


    protected $fillable = [
        'category_id',
        'codigo',
        'fecha_vencimiento',
        'costo',
    ];

    // un lote tenga muchos productos
    public function productos()
    {
        return $this->belongsToMany(Product::class, 'lote_products')
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    /**
     * Relación muchos a muchos con el modelo Product.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'lote_products')
            ->withPivot('cantidad', 'costo') // Campos adicionales en la tabla pivote
            ->withTimestamps();
    }

    /* // Relación muchos a muchos con productos
    public function products()
    {
        return $this->belongsToMany(Product::class, 'lote_products', 'lote_id', 'product_id')
            ->withPivot('cantidad', 'precio') // Columnas adicionales en `lote_products`
            ->withTimestamps();
    } */


    // Relación con categorías
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Relación con la tabla `Inventarios`.
     */
    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'lote_id');
    }

    /**
     * Relación con la tabla `MovimientoInventarios`.
     */
    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'lote_id');
    }
}

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

    /**
     * Relación uno a muchos con el modelo ProductLote.
     */
    public function productLotes()
    {
        return $this->hasMany(ProductLote::class);
    }

    /**
     * Relación muchos a muchos con el modelo Product a través de la tabla `product_lote`.
     */
    public function productsThroughProductLote()
    {
        return $this->belongsToMany(Product::class, 'product_lote')
            ->withPivot('quantity') // Campo adicional en esta tabla pivote
            ->withTimestamps();
    }

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

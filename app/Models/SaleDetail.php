<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id','inventario_id','store_id','product_id','lote_id','price','quantity','porc_desc','descuento','descuento_cliente','porc_iva','iva','porc_otro_impuesto','otro_impuesto','porc_impoconsumo','impoconsumo','total_bruto','total','status'];

    public function sale(){
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    public function third(){
        return $this->belongsTo(Third::class, 'third_id', 'id');
    }

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /* public function centro_costo_products()
    {
        return $this->belongsToMany(Centro_costo_product::class, 'saledetail_ccps', 'saledetail_id', 'centro_costo_product_id')
            ->withPivot('venta_real', 'cto_venta_total');
    } */

    public function notacredito_details()
    {
        return $this->belongsTo(NotaCreditoDetail::class, 'product_id', 'id');
    }

    public function notadebito_details()
    {
        return $this->belongsTo(NotaDebitoDetail::class, 'product_id', 'id');
    }
    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id','inventario_id','store_id','lote_id','product_id','price','quantity','porc_desc','descuento','descuento_cliente','porc_iva','iva','porc_otro_impuesto','otro_impuesto','porc_impoconsumo','impoconsumo','promo_percent','promo_value','total_bruto','price_venta','total','status'];

    public function sale(){
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function third(){
        return $this->belongsTo(Third::class, 'third_id', 'id');
    }

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }   

    public function notacredito_details()
    {
        return $this->belongsTo(NotaCreditoDetalle::class, 'product_id', 'id');
    }

    public function notadebito_details()
    {
        return $this->belongsTo(NotaDebitoDetail::class, 'product_id', 'id');
    }
    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionDetail extends Model
{
     protected $table = 'promotion_details';

     protected $fillable = ['promotion_id', 'inventario_id', 'store_id', 'lote_id',  'product_id', 'quantity', 'porc_desc', 'fecha_inicio', 'hora_inicio', 'fecha_final', 'hora_final', 'observacion', 'user_id', 'status'];

     public function promotion()
     {
          return $this->belongsTo(Promotion::class);
     }

     public function inventario()
     {
          return $this->belongsTo(Inventario::class);
     }

     public function store()
     {
          return $this->belongsTo(Store::class);
     }

     public function product()
     {
          return $this->belongsTo(Product::class);
     }

     public function lote()
     {
          return $this->belongsTo(Lote::class);
     }

     public function scopeActivePromotion($q)
     {
          // promotion.status debe ser '1' para considerar la promoción vigente
          return $q->whereHas('promotion', fn($qq) => $qq->where('status', '1'));
     }
}

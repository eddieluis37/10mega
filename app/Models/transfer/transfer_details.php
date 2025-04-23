<?php

namespace App\Models\transfer;

use App\Models\Lote;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class transfer_details extends Model
{
   // Si tu tabla no sigue la convención:
    // protected $table = 'transfer_details';

    protected $fillable = [
        'transfers_id',
        'lote_prod_traslado_id',
        'product_id',
        'kgrequeridos',
        'status',
    ];

    protected $casts = [
        'kgrequeridos' => 'decimal:2',
        'status'       => 'boolean',
    ];

    /**
     * Relación al traslado padre
     */
    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class, 'transfers_id');
    }

    /**
     * Producto trasladado
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Lote asignado al traslado
     */
    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_prod_traslado_id');
    }
}


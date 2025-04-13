<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saleformapago extends Model
{
    use HasFactory;

    protected $table = 'saleformapagos';

    protected $fillable = [
        'sale_id',
        'formapago_id',
        'diascredito',
        'telefonoasociado',
        'bancoorigen',
        'bancodestino',
        'numcheque',
        'descripcion'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'formapago_id');
    }
}
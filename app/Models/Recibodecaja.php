<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recibodecaja extends Model
{
    use HasFactory;
    protected $table = 'recibodecajas';
    
    protected $fillable = [
        'user_id', 
        'third_id',
        'sale_id',
        'formapagos_id', 
        'saldo', 
        'abono', 
        'nuevo_saldo',
        'fecha_elaboracion', 
        'fecha_cierre',
        'consecutivo', 
        'consec',
        'status',
        'tipo',            // '1' => Ingreso, '2' => Egreso, etc.
        'realizar_un',     // Ejemplo: 'Abono a deuda', 'Anticipo', 'Avanzado (Impuestos, descuentos, ajustes)'
        'observations'
    ];

    // Relación con el usuario que genera el recibo
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el tercero (cliente o proveedor) involucrado
    public function third()
    {
        return $this->belongsTo(Third::class);
    }

    // Relación con la venta asociada (en caso de ser un abono a ventas a crédito)
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // Relación con la forma de pago utilizada
    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'formapagos_id');
    }
    
    // Si se decide almacenar el detalle de cada movimiento del recibo en una tabla relacionada,
    // se puede establecer la siguiente relación (requiere incluir un campo recibo_de_caja_id en la tabla detalle)
    public function detalles()
    {
        return $this->hasMany(CajaReciboDineroDetail::class, 'recibo_de_caja_id');
    }
}
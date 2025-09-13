<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrazaMovementRequest extends FormRequest
{
    public function authorize()
    {
        return true; // controla acceso con middleware (ej. sanctum)
    }

    public function rules()
    {
        return [
            'lote_codigo' => 'required|string|max:255',
            'id_producto_terminado' => 'required|integer|exists:products,id',
            'cantidad' => 'required|numeric|min:0.0001',
            'costo_unidad' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date',
            // Opcionales para idempotencia / trazabilidad
            'external_reference' => 'nullable|string|max:255',
        ];
    }
}

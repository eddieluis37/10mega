<?php

namespace App\Http\Controllers;

use App\Models\CuentaPorCobrar;
use App\Models\CuentaPorPagar;
use App\Models\ReciboDeCaja;
use App\Models\CajaReciboDineroDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Registra un pago o abono de cliente (Entrada de Dinero).
     */
    public function customerPayment(Request $request)
    {
        // Valida la información recibida
        $validated = $request->validate([
            'sale_ids'       => 'required|array',
            'amount'         => 'required|numeric',
            'details'        => 'required|array',
            'formapagos_id'  => 'nullable|numeric',
            'caja_id'        => 'required|numeric',
            'observations'   => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Actualiza las cuentas por cobrar asociadas a cada venta
            foreach ($validated['sale_ids'] as $saleId) {
                $cuenta = CuentaPorCobrar::where('sale_id', $saleId)->first();
                if ($cuenta) {
                    // Se descuenta el monto abonado (el ejemplo utiliza el mismo monto para cada venta; en la práctica, se puede distribuir)
                    $nuevaDeuda = $cuenta->deuda_x_cobrar - $validated['amount'];
                    $cuenta->deuda_x_cobrar = $nuevaDeuda < 0 ? 0 : $nuevaDeuda;
                    $cuenta->save();
                }
            }

            // Crea el registro de Recibo de Caja para Ingreso
            $recibo = ReciboDeCaja::create([
                'user_id'       => 1,
                'sale_id'       => $validated['sale_ids'][0], // para efectos del ejemplo, se asocia la primera venta
                'formapagos_id' => $validated['formapagos_id'] ?? null,
                'saldo'         => 0, // Se puede calcular según el estado actual de la caja
                'abono'         => $validated['amount'],
                'nuevo_saldo'   => 0, // Se asigna de acuerdo al balance actualizado
                'fecha_elaboracion' => now(),
                'status'        => '0',
                'tipo'          => '1', // '1' representa Ingreso
                'realizar_un'   => 'Abono a deuda',
                'observations'  => $validated['observations'] ?? '',
            ]);

            // Registra el detalle del movimiento en caja_recibo_dinero_details
            foreach ($validated['details'] as $detail) {
                CajaReciboDineroDetail::create([
                    'caja_id'           => $validated['caja_id'],
                    'user_id'           => 1,
                    'third_id'          => $detail['third_id'] ?? null,
                    'quantity'          => $detail['quantity'],
                    'price'             => $detail['price'],
                    'porc_desc'         => $detail['porc_desc'] ?? 0,
                    'descuento'         => $detail['descuento'] ?? 0,
                    'porc_iva'          => $detail['porc_iva'] ?? 0,
                    'iva'               => $detail['iva'] ?? 0,
                    'total_bruto'       => $detail['total_bruto'] ?? 0,
                    'total'             => $detail['total'] ?? 0,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pago de cliente registrado correctamente'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Registra el pago total de facturas de proveedores (Salida de Dinero).
     */
    public function supplierPayment(Request $request)
    {
        // Valida la información recibida
        $validated = $request->validate([
            'cuenta_por_pagars_id' => 'required|numeric',
            'amount'               => 'required|numeric',
            'details'              => 'required|array',
            'formapagos_id'        => 'nullable|numeric',
            'caja_id'              => 'required|numeric',
            'observations'         => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Actualiza la cuenta por pagar del proveedor
            $cuentaProveedor = \App\Models\CuentaPorPagar::find($validated['cuenta_por_pagars_id']);
            if ($cuentaProveedor) {
                $nuevoPendiente = $cuentaProveedor->monto_pendiente - $validated['amount'];
                $cuentaProveedor->monto_pendiente = $nuevoPendiente < 0 ? 0 : $nuevoPendiente;
                $cuentaProveedor->save();
            }

            // Crea el registro de Recibo de Caja para Egreso
            $recibo = ReciboDeCaja::create([
                'user_id'       => auth()->user()->id,
                'third_id'      => $cuentaProveedor->proveedor_id,
                'sale_id'       => null,
                'formapagos_id' => $validated['formapagos_id'] ?? null,
                'saldo'         => 0,
                'abono'         => $validated['amount'],
                'nuevo_saldo'   => 0,
                'fecha_elaboracion' => now(),
                'status'        => '0',
                'tipo'          => '2', // '2' representa Egreso
                'realizar_un'   => 'Pago a proveedores',
                'observations'  => $validated['observations'] ?? '',
            ]);

            // Inserta el detalle del movimiento en caja_recibo_dinero_details
            foreach ($validated['details'] as $detail) {
                CajaReciboDineroDetail::create([
                    'caja_id'           => $validated['caja_id'],
                    'user_id'           => auth()->user()->id,
                    'third_id'          => $detail['third_id'] ?? null,
                    'quantity'          => $detail['quantity'],
                    'price'             => $detail['price'],
                    'porc_desc'         => $detail['porc_desc'] ?? 0,
                    'descuento'         => $detail['descuento'] ?? 0,
                    'porc_iva'          => $detail['porc_iva'] ?? 0,
                    'iva'               => $detail['iva'] ?? 0,
                    'total_bruto'       => $detail['total_bruto'] ?? 0,
                    'total'             => $detail['total'] ?? 0,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pago de proveedor registrado correctamente'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

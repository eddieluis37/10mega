<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CuentaPorCobrar;
use App\Models\CuentaPorPagar;
use App\Models\ReciboDeCaja;
use App\Models\CajaReciboDineroDetail;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        // Validación de los datos
        $validator = Validator::make($request->all(), [
            'cliente' => 'required|exists:thrids,id',
            'formaPago' => 'required|exists:formapagos,id',
            'tableData' => 'required|array|min:1',
            'tableData.*.id' => 'required|exists:cuentas_por_cobrars,id',
            'tableData.*.vr_deuda' => 'required|numeric|min:0',
            'tableData.*.vr_pago' => 'required|numeric|min:0',
            'tableData.*.nvo_saldo' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $clientId = $request->cliente;
            $paymentMethodId = $request->formaPago;
            $tableData = $request->tableData;

            // Crear registro de pago al cliente
            $customerPayment = ReciboDeCaja::create([
                'third_id'          => $clientId,
                'formapagos_id'  => $paymentMethodId,
                'saldo'         => 0,
                'abono'      => 0,
                'nuevo_saldo'  => 0,
                'fecha_elaboracion' => now(),
                'status'        => '0',
                'tipo'          => '1', // '1' representa Ingreso
                'realizar_un'   => 'Abono a deuda',
            ]);

            $totalDebt       = 0;
            $totalPayment    = 0;
            $totalNewBalance = 0;

            // Itera sobre cada registro de la tabla para actualizar la cuenta por cobrar
            foreach ($tableData as $row) {
                // Buscar la cuenta por cobrar
                $account = CuentaPorCobrar::find($row['id']);
                if (!$account) {
                    throw new \Exception("Cuenta por cobrar con id {$row['id']} no encontrada.");
                }

                // Se actualiza el valor de la deuda pendiente (deuda_x_cobrar)
                $account->update([
                    'deuda_x_cobrar' => $row['nvo_saldo']
                ]);

              
            }

            // Actualiza los totales en el registro del pago
            $customerPayment->update([
                'total_debt'         => $totalDebt,
                'total_payment'      => $totalPayment,
                'total_new_balance'  => $totalNewBalance,
            ]);

            // Registrar el movimiento en caja (caja_recibo_dinero_details)
            CajaReciboDineroDetail::create([
                'caja_id' => $customerPayment->id,
                'third_id'           => $clientId,              
                'total'              => $totalPayment,
            ]);

            // Log de operación exitosa
            Log::info("Pago registrado exitosamente. ID de pago: {$customerPayment->id}");

            DB::commit();

            return response()->json(['success' => 'Pago registrado exitosamente.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al registrar pago: " . $e->getMessage());
            return response()->json(['error' => 'Error al registrar el pago.'], 500);
        }
    }
}

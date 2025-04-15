<?php

namespace App\Http\Controllers;

use App\Models\CuentaPorCobrar;
use App\Models\CuentaPorPagar;
use App\Models\ReciboDeCaja;
use App\Models\CajaReciboDineroDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        // Validación de los datos
        $validator = Validator::make($request->all(), [
            'cliente' => 'required|exists:clients,id',
            'formaPago' => 'required|exists:payment_methods,id',
            'tableData' => 'required|array|min:1',
            'tableData.*.id' => 'required|exists:cuentas_por_cobrars,id',
            'tableData.*.vr_deuda' => 'required|numeric|min:0',
            'tableData.*.vr_pago' => 'required|numeric|min:0',
            'tableData.*.nvo_saldo' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=> $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $clientId = $request->cliente;
            $paymentMethodId = $request->formaPago;
            $tableData = $request->tableData;

            // Crear registro de pago al cliente
            $customerPayment = CustomerPayment::create([
                'client_id'          => $clientId,
                'payment_method_id'  => $paymentMethodId,
                'total_debt'         => 0,
                'total_payment'      => 0,
                'total_new_balance'  => 0,
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

                // Registrar cada detalle del pago
                CustomerPaymentDetail::create([
                    'customer_payment_id' => $customerPayment->id,
                    'cuenta_id'           => $row['id'],
                    'vr_deuda'            => $row['vr_deuda'],
                    'vr_pago'             => $row['vr_pago'],
                    'nvo_saldo'           => $row['nvo_saldo'],
                ]);

                $totalDebt       += $row['vr_deuda'];
                $totalPayment    += $row['vr_pago'];
                $totalNewBalance += $row['nvo_saldo'];
            }

            // Actualiza los totales en el registro del pago
            $customerPayment->update([
                'total_debt'         => $totalDebt,
                'total_payment'      => $totalPayment,
                'total_new_balance'  => $totalNewBalance,
            ]);

            // Registrar el movimiento en caja (caja_recibo_dinero_details)
            CashReceiptDetail::create([
                'customer_payment_id' => $customerPayment->id,
                'client_id'           => $clientId,
                'payment_method_id'   => $paymentMethodId,
                'amount'              => $totalPayment,
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

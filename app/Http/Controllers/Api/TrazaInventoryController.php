<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrazaMovementRequest;
use App\Models\Lote;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class TrazaInventoryController extends Controller
{
    public function store(StoreTrazaMovementRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Puedes configurar un store destino por defecto en config/traza.php o usar fijo 37
        $storeDestinoId = config('traza.default_store_destino_id', 37);

        // Idempotencia: si te llega external_reference, evita duplicados
        if (!empty($data['external_reference'])) {
            $exists = MovimientoInventario::where('external_reference', $data['external_reference'])->first();
            if ($exists) {
                return response()->json([
                    'status' => 'ignored',
                    'message' => 'Movimiento ya procesado (external_reference duplicado).',
                    'movimiento_id' => $exists->id
                ], 200);
            }
        }

        DB::beginTransaction();
        try {
            // 1) Crear o recuperar lote por codigo
            $lote = Lote::firstOrCreate(
                ['codigo' => $data['lote_codigo']],
                [
                    // asigna fecha_vencimiento si llega
                    'fecha_vencimiento' => isset($data['fecha_vencimiento']) ? Carbon::parse($data['fecha_vencimiento']) : null,
                    // agrega otros campos por defecto si tu modelo lo requiere
                ]
            );

            // 2) Inventario: firstOrCreate y luego sobrescribir cantidad y costo
            $inventario = Inventario::firstOrCreate(
                [
                    'product_id' => $data['id_producto_terminado'],
                    'lote_id' => $lote->id,
                    'store_id' => $storeDestinoId,
                ],
                [
                    'cantidad_prod_term' => 0,
                    'costo_unitario' => 0,
                    'costo_total' => 0,
                ]
            );

            // Sobrescribimos con lo que trae TRAZA
            $inventario->cantidad_prod_term = $data['cantidad'];
            $inventario->costo_unitario = $data['costo_unidad'];
            $inventario->costo_total = $data['cantidad'] * $data['costo_unidad'];
            $inventario->save();

            // 3) Registrar movimiento de inventario
            $mov = MovimientoInventario::create([
                'tipo' => 'products_terminados',
                'store_origen_id' => null,
                'store_destino_id' => $storeDestinoId,
                'lote_id' => $lote->id,
                'product_id' => $data['id_producto_terminado'],
                'cantidad' => $data['cantidad'],
                'costo_unitario' => $data['costo_unidad'],
                'total' => $data['cantidad'] * $data['costo_unidad'],
                'fecha' => Carbon::now(),
                'external_reference' => $data['external_reference'] ?? null,
            ]);

            // 4) Actualizar campo cost de products con el costo de compra recibido
            $producto = Product::find($data['id_producto_terminado']);
            if ($producto) {
                $producto->cost = $data['costo_unidad'];
                $producto->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'ok',
                'message' => 'Movimiento procesado.',
                'lote_id' => $lote->id,
                'inventario_id' => $inventario->id,
                'movimiento_id' => $mov->id,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            // log::error($e->getMessage()) // registra en logs
            return response()->json([
                'status' => 'error',
                'message' => 'Error procesando movimiento: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrazaMovementRequest; // si usaste FormRequest previamente
use App\Models\Lote;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrazaInventoryController extends Controller
{
    /**
     * Recibe movimiento desde TRAZA y crea/actualiza inventario y movimiento.
     */
    public function store(StoreTrazaMovementRequest $request): JsonResponse
    {
        $data = $request->validated();

        // store destino por defecto (ajusta o permite que venga en $data si prefieres)
        $storeDestinoId = config('traza.default_store_destino_id', 37);

        // Normalizar/castear valores
        $productId = (int) $data['id_producto_terminado'];

        // cantidad como float (si quieres, puedes aplicar limpieza similar a costo)
        $cantidad = (float) $data['cantidad'];

        // --- LIMPIEZA de costo_unitario: quitar símbolos, gestionar comas/puntos ---
        $rawCosto = isset($data['costo_unidad']) ? trim($data['costo_unidad']) : '0';
        // mantener sólo dígitos, coma, punto y signo menos
        $rawCosto = preg_replace('/[^\d\.,\-]/', '', $rawCosto);

        // Si hay tanto coma como punto, asumimos que la coma es separador de miles -> eliminar comas
        if (strpos($rawCosto, ',') !== false && strpos($rawCosto, '.') !== false) {
            $rawCosto = str_replace(',', '', $rawCosto);
        } elseif (strpos($rawCosto, ',') !== false) {
            // si sólo hay coma, la convertimos a punto decimal
            $rawCosto = str_replace(',', '.', $rawCosto);
        }

        $costoUnitario = $rawCosto;
        // ---------------------------------------------------------------------

        $fechaVencimiento = isset($data['fecha_vencimiento']) && $data['fecha_vencimiento']
            ? Carbon::parse($data['fecha_vencimiento'])
            : null;

        DB::beginTransaction();
        try {
            // 1) Crear o recuperar lote por codigo
            $lote = Lote::firstOrCreate(
                ['codigo' => $data['lote_codigo']],
                ['fecha_vencimiento' => $fechaVencimiento]
            );

            // 2) Buscar inventario y bloquear fila para evitar race conditions
            $inventario = Inventario::where('product_id', $productId)
                ->where('lote_id', $lote->id)
                ->where('store_id', $storeDestinoId)
                ->lockForUpdate()
                ->first();

            $inventarioCreated = false;
            if (! $inventario) {
                // crea inventario si no existe (usa cantidad y costo recibidos)
                $inventario = Inventario::create([
                    'product_id' => $productId,
                    'lote_id' => $lote->id,
                    'store_id' => $storeDestinoId,
                    'cantidad_prod_term' => $cantidad,
                    'costo_unitario' => $costoUnitario,
                    'costo_total' => $cantidad * $costoUnitario,
                ]);
                $inventarioCreated = true;
                $action = 'created';
            } else {
                // existe -> SUMAR la nueva cantidad a la que ya tenía (no sobrescribir)
                $inventario->cantidad_prod_term += $cantidad;

                // actualizar costo unitario con el nuevo valor recibido (según tu petición)
                $inventario->costo_unitario = $costoUnitario;

                // recalcular total con la nueva cantidad acumulada y el nuevo costo unitario
                $inventario->costo_total = $inventario->cantidad_prod_term * $costoUnitario;

                $inventario->save();
                $action = 'updated';
            }

            // 3) Registrar movimiento de inventario (histórico)
            $mov = MovimientoInventario::create([
                'tipo' => 'products_terminados',
                'store_origen_id' => null,
                'store_destino_id' => $storeDestinoId,
                'lote_id' => $lote->id,
                'product_id' => $productId,
                'cantidad' => $cantidad,
                'costo_unitario' => $costoUnitario,
                'total' => $cantidad * $costoUnitario,
                'fecha' => Carbon::now(),
            ]);

            // 4) Actualizar el campo cost del producto con el nuevo costo recibido
            $producto = Product::find($productId);
            if ($producto) {
                $producto->cost = $costoUnitario;
                $producto->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'ok',
                'action' => $action, // 'created' o 'updated' sobre inventario
                'lote_id' => $lote->id,
                'inventario_id' => $inventario->id,
                'movimiento_id' => $mov->id,
            ], $inventarioCreated ? 201 : 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            // registra el error para debugging
            logger()->error('Error procesando movimiento TRAZA: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error procesando movimiento: ' . $e->getMessage()
            ], 500);
        }
    }
}

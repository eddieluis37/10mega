<?php

namespace App\Http\Controllers\cuentasporcobrar;

use App\Models\Cuentas_por_cobrar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class cuentasporcobrarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dateFrom = session('dateFrom');
        $dateTo = Carbon::parse($request->input('dateTo'))->format('Y-m-d');
        /*  $dateTo = session('dateTo'); */

        /*  $dateFrom = '2024-05-01';
         */

        $category = Category::orderBy('name', 'asc')->get();
        $centros = Store::Where('status', 1)->get();

        return view('cuentas_por_cobrar.index', compact('category', 'centros', 'dateFrom', 'dateTo'));
    }

     /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cuentas_por_cobrar  $cuentas_por_cobrar
     * @return \Illuminate\Http\Response
     */
      public function show(Request $request)
    {
        $centroCostoId = $request->input('centrocosto');
        $categoryId   = $request->input('categoria');
        $dateFrom = $request->input('dateFrom');
        $dateTo = $request->input('dateTo');
        // Guardar los valores en la sesión
        session(['dateFrom' => $dateFrom, 'dateTo' => $dateTo]);

        // Consulta consolidada de ajuste: inicial + ajuste, incluyendo categoría
        $table = config('activitylog.table_name');
        $query = DB::table("{$table} as al")
            ->select(
                'al.created_at as diahora_ajuste',
                'c.name as category_name',
                'p.id as product_id',
                'p.code as product_code',
                'p.name as product_name',
                's.name as store_name',
                'l.codigo as lote_code',
                'l.fecha_vencimiento as fecha_vencimientolote',
                // Usa COALESCE para caer a '—' si no hay usuario
                DB::raw("COALESCE(u.name, '—') as user_name"),
                // Valor inicial desde registro de limpieza
                DB::raw("MAX(CASE WHEN al.description = 'Ajuste de inventario realizado' 
            THEN CAST(JSON_UNQUOTE(JSON_EXTRACT(al.properties, '$.before.stock_ideal')) 
            AS DECIMAL(10,2)) END) AS stock_ideal_antes"),
                DB::raw("MAX(CASE WHEN al.description = 'Ajuste de inventario realizado' 
            THEN CAST(JSON_UNQUOTE(JSON_EXTRACT(al.properties, '$.after.stock_fisico')) 
            AS DECIMAL(10,2)) END) AS stock_fisico_despues"),
                // Valor costo total desde registro de limpieza
                DB::raw("MAX(CASE WHEN al.description = 'Campos de inventario reseteados a cero' 
            THEN CAST(JSON_UNQUOTE(JSON_EXTRACT(al.properties, '$.after.costo_total')) 
            AS DECIMAL(12,2)) END) AS costo_inicial_total"),
                // Valor tras ajuste realizado
                DB::raw("MAX(CASE WHEN al.description = 'Ajuste de inventario realizado' 
            THEN CAST(JSON_UNQUOTE(JSON_EXTRACT(al.properties, '$.after.cantidad_diferencia')) 
            AS DECIMAL(10,2)) END) AS cantidad_diferencia"),
                DB::raw("MAX(CASE WHEN al.description = 'Ajuste de inventario realizado' 
            THEN CAST(JSON_UNQUOTE(JSON_EXTRACT(al.properties, '$.after.costo_total')) 
            AS DECIMAL(12,2)) END) AS costo_total_ajuste")
            )
            // Uniones para extraer IDs desde JSON y traer datos relacionados
            ->join(
                'products as p',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(al.properties, '$.metadata.product_id'))"),
                '=',
                'p.id'
            )
            ->join(
                'categories as c',     // <-- nueva unión
                'p.category_id',
                '=',
                'c.id'
            )
            ->join(
                'stores as s',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(al.properties, '$.metadata.store_id'))"),
                '=',
                's.id'
            )
            ->join(
                'lotes as l',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(al.properties, '$.metadata.lote_id'))"),
                '=',
                'l.id'
            )
            // LEFT JOIN a users, probando dos posibles ID:
            ->leftJoin('users as u', function ($join) {
                $join->on('al.causer_id', '=', 'u.id')
                    ->orOn(DB::raw(
                        "CAST(JSON_UNQUOTE(JSON_EXTRACT(al.properties, '$.metadata.causer_id')) AS UNSIGNED)"
                    ), '=', 'u.id');
            })
            ->where('al.log_name', 'ajustes_inventario')
            ->when($centroCostoId, function ($q) use ($centroCostoId) {
                return $q->where('s.id', $centroCostoId);
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                return $q->where('p.category_id', $categoryId);
            })
            ->whereIn('al.description', [
                'Campos de inventario reseteados a cero',
                'Ajuste de inventario realizado'
            ])
            ->whereBetween('al.created_at', [$dateFrom, $dateTo])
            ->groupBy(
                'p.code',
                'p.name',
                'c.name',      // <-- agregar categoría al agrupar
                's.name',
                'l.codigo',
                'u.name'
            );

        return datatables()->of($query)
            ->addIndexColumn()
            ->make(true);
    }
}

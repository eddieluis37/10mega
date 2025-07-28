<?php

namespace App\Http\Controllers\cuentasporcobrar;

use App\Models\Cuentas_por_cobrar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Cuentaporcobrar;
use App\Models\Store;
use App\Models\Third;
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

        $clientes = Third::Where('cliente', 1)->get();
        $vendedores = Third::Where('vendedor', 1)->get();
        $domiciliarios = Third::Where('domiciliario', 1)->get();

        // $clientes = Third::Where('status', 1)->get();

        $category = Category::orderBy('name', 'asc')->get();

        $centros = Store::Where('status', 1)->get();

        return view('cuentas_por_cobrar.index', compact('category', 'clientes', 'vendedores', 'domiciliarios',  'centros', 'dateFrom', 'dateTo'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cuentas_por_cobrar  $cuentas_por_cobrar
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request)
    {
        $clienteId    = $request->input('cliente');
        $vendedorId   = $request->input('vendedor');
        $domiciliarioId   = $request->input('domiciliario');
        $dateFrom     = $request->input('dateFrom');
        $dateTo       = $request->input('dateTo');

        // Guardar filtro de fechas en sesión (si lo necesitas luego)
        session(['dateFrom' => $dateFrom, 'dateTo' => $dateTo]);

        // Construcción de la query
        $query = Cuentaporcobrar::query()
            // Relacionamos con la venta
            ->join('sales as sa', 'sa.id', '=', 'cuentas_por_cobrars.sale_id')
            // Centro de costo (tercero asociado a la cuenta por cobrar)
            ->join('thirds as cc', 'cc.id', '=', 'cuentas_por_cobrars.third_id')
            // Cliente, vendedor y domiciliario sacados desde la venta (ajusta los campos FK según tu esquema)
            ->leftJoin('thirds as cl', 'cl.id', '=', 'sa.third_id')
            ->leftJoin('thirds as v',  'v.id',  '=', 'sa.vendedor_id')
            ->leftJoin('thirds as d',  'd.id',  '=', 'sa.domiciliario_id')
            // Opcional: si necesitas traer info de pagos parciales
            ->leftJoin('recibodecajas as rc',         'rc.third_id',              '=', 'cc.id')
            ->leftJoin('caja_recibo_dinero_details as crdd', 'crdd.recibodecaja_id', '=', 'rc.id')
            // Filtros dinámicos
            ->when(
                $clienteId,
                fn($q) =>
                $q->where('cuentas_por_cobrars.third_id', $clienteId)
            )
            ->when(
                $vendedorId,
                fn($q) =>
                $q->where('sa.vendedor_id', $vendedorId)
            )
            ->when(
                $domiciliarioId,
                fn($q) =>
                $q->where('sa.domiciliario_id', $domiciliarioId)
            )
            ->whereBetween('cuentas_por_cobrars.created_at', [$dateFrom, $dateTo])
            // Selección de columnas con los alias que tu DataTable espera
            ->select([
                'cl.name as cliente_name',
                'v.name  as vendedor_name',
                'd.name  as domiciliario_name',
                'sa.consecutivo                   as sales_consecutivo',
                'cuentas_por_cobrars.fecha_vencimiento                    as fecha_vencimiento',

                // cálculo de días de mora:
                DB::raw(
                    // Evitamos días negativos con GREATEST
                    'GREATEST(DATEDIFF(CURDATE(), cuentas_por_cobrars.fecha_vencimiento), 0) as dias_mora'
                ),
                // Formateo de moneda colombiana (sin decimales, miles/puntos):
                DB::raw("REPLACE(FORMAT(cuentas_por_cobrars.deuda_inicial, 0), ',', ',') as cuentas_por_cobrars_deuda_inicial"),
                DB::raw("REPLACE(FORMAT(cuentas_por_cobrars.deuda_x_cobrar, 0), ',', ',') as cuentas_por_cobrars_deuda_x_cobrar"),
            ]);

        // Retornamos al servidor de DataTables
        return datatables()
            ->of($query)
            ->editColumn('fecha_vencimiento', function ($row) {
                return Carbon::parse($row->fecha_vencimiento)
                    ->format('d/m/Y');
            })
            ->addIndexColumn()
            ->make(true);
    }


    public function showOriginal(Request $request)
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

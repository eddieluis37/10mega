<?php

namespace App\Http\Controllers\reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\centros\Centrocosto;
use App\Models\SaleDetail;
use App\Models\Third;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class reporteventaprodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $startDate = Carbon::parse(Carbon::now())->format('Y-m-d');
        $endDate = Carbon::parse(Carbon::now())->format('Y-m-d');

        $category = Category::orderBy('name', 'asc')->get();
        // Obtiene los IDs de los centros de costo asociados a las tiendas del usuario autenticado.
        $centroIds = Auth::user()->stores->pluck('centrocosto_id')->unique();

        // Obtiene los modelos de centros de costo usando los IDs obtenidos
        $centros = Centrocosto::whereIn('id', $centroIds)->get();

        $vendedores = Third::Where('vendedor', 1)->get();
        $domiciliarios = Third::Where('domiciliario', 1)->get();

        return view('reportes.consolidado', compact('category', 'centros', 'startDate', 'endDate', 'vendedores', 'domiciliarios'));
    }

    public function show(Request $request)
    {
        // 1) Capturamos los filtros del request
        $centroCostoId   = $request->input('centrocosto');
        $categoryId      = $request->input('categoria');
        $startDate       = Carbon::parse($request->input('startDate'))->format('Y-m-d H:i:s');
        $endDate         = Carbon::parse($request->input('endDate'))->format('Y-m-d H:i:s');
        $vendedorId      = $request->input('vendedor');      // id o null
        $domiciliarioId  = $request->input('domiciliario');  // id o null

        // 2) Armamos la consulta con joins y agrupamientos
        $data = DB::table('sale_details as sd')
            ->select([
                'p.name as producto',
                'l.codigo as lote',
                DB::raw('SUM(sd.quantity) as cantidad'),
                DB::raw('ROUND(AVG(sd.price), 0) as precio_base'),
                DB::raw('SUM(sd.total_bruto) as total_base'),
                DB::raw('SUM(sd.descuento) as descuento_productos'),
                DB::raw('SUM(sd.descuento_cliente) as descuento_clientes'),
                DB::raw('SUM(sd.iva) as total_iva'),
                DB::raw('SUM(sd.otro_impuesto) as total_up'),
                DB::raw('SUM(sd.impoconsumo) as total_ic'),
                DB::raw('SUM(sd.total) as total_venta'),
            ])
            ->join('sales as s', 's.id', '=', 'sd.sale_id')
            ->join('products as p', 'p.id', '=', 'sd.product_id')
            ->join('lotes as l', 'l.id', '=', 'sd.lote_id')
            // filtros condicionales
            ->when($centroCostoId, function ($q) use ($centroCostoId) {
                return $q->where('s.centrocosto_id', $centroCostoId);
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                return $q->where('p.category_id', $categoryId);
            })
            ->when($vendedorId, function ($q) use ($vendedorId) {
                return $q->where('s.vendedor_id', $vendedorId);
            })
            ->when($domiciliarioId, function ($q) use ($domiciliarioId) {
                return $q->where('s.domiciliario_id', $domiciliarioId);
            })
            ->whereBetween('s.fecha_venta', [$startDate, $endDate])
            // Agrupar por las columnas no agregadas para evitar problemas en modos SQL estrictos
            ->groupBy('sd.product_id', 'sd.lote_id', 'p.name', 'l.codigo')
            ->orderBy('p.name')
            ->get();

        // 3) Devolvemos al DataTable
        return datatables()->of($data)
            ->addIndexColumn()
            ->make(true);
    }
}

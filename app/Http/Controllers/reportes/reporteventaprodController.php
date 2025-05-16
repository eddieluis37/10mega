<?php

namespace App\Http\Controllers\reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\centros\Centrocosto;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $centros = Centrocosto::Where('status', 1)->get();

        return view('reportes.consolidado', compact('category', 'centros', 'startDate', 'endDate'));
    }

    public function show(Request $request)
    {
        // 1) Capturamos los filtros del request
        $centroCostoId = $request->input('centrocosto');
        $categoryId   = $request->input('categoria');
        $startDate    = Carbon::parse($request->input('startDate'))->format('Y-m-d H:i:s');
        $endDate      = Carbon::parse($request->input('endDate'))->format('Y-m-d H:i:s');

        // 2) Armamos la consulta con joins y agrupamientos
        $data = DB::table('sale_details as sd')       
            ->select([
                'p.name as producto',
                'l.codigo as lote',
                DB::raw('SUM(sd.quantity) as cantidad'),
                DB::raw('ROUND(AVG(sd.price), 2) as precio_base'),
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
            ->when($centroCostoId, function ($q) use ($centroCostoId) {
                return $q->where('s.centrocosto_id', $centroCostoId);
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                return $q->where('p.category_id', $categoryId);
            })
            ->whereBetween('s.fecha_venta', [$startDate, $endDate])
            ->groupBy('sd.product_id', 'sd.lote_id')
            ->orderBy('p.name')
            ->get();

        // 3) Devolvemos al DataTable
        return datatables()->of($data)
            ->addIndexColumn()
            ->make(true);
    }
}

<?php

namespace App\Http\Controllers\sale;

use App\Http\Controllers\Controller;
use App\Models\caja\Caja;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

/* $sale = Sale::with('third')->findOrFail($id);
            $sale = Sale::with('third')->whereHas('third', function ($query) {
                $query->where('status', 1);
            })->findOrFail($id); */

class exportComandaController extends Controller
{
    public function showComanda($id)
    {
        // 1) Datos principales de la venta
        $sale = Sale::join('thirds as third', 'sales.third_id', '=', 'third.id')
            ->join('users as u', 'sales.user_id', '=', 'u.id')
            ->join('centro_costo as c', 'sales.centrocosto_id', '=', 'c.id')
            ->leftJoin('formapagos as fp', 'sales.forma_pago_tarjeta_id', '=', 'fp.id')
            ->leftJoin('formapagos as fp2', 'sales.forma_pago_otros_id', '=', 'fp2.id')
            ->leftJoin('formapagos as fp3', 'sales.forma_pago_credito_id', '=', 'fp3.id')
            ->select(
                'sales.*',
                'u.name as nameuser',
                'third.name as namethird',
                'fp.nombre as formapago1',
                'fp2.nombre as formapago2',
                'fp3.nombre as formapago3',
                'third.identification',
                'third.direccion',
                'c.name as namecentrocosto',
                'third.porc_descuento',
                'sales.total_iva',
                'sales.vendedor_id'
            )
            ->where('sales.id', $id)
            ->first();

        // 2) Parte “base” de los detalles
        $baseDetails = SaleDetail::join('products as pro', 'sale_details.product_id', '=', 'pro.id')
            ->leftJoin('lotes as lot', 'sale_details.lote_id', '=', 'lot.id')
            ->where('sale_details.sale_id', $id)
            ->where('sale_details.status', '1')
            ->select([
                'sale_details.id as parent_id',                // para orden
                DB::raw('0 as is_component'),                  // marca línea “original”
                'pro.name as nameprod',
                'pro.code',
                'sale_details.quantity',
                'lot.codigo as lote_codigo',
                'lot.fecha_vencimiento as lote_fecha_vencimiento',
                'sale_details.porc_iva',
                'sale_details.iva',
                'sale_details.porc_otro_impuesto',
            ]);

        // 3) Parte de componentes/composiciones (sin lote ni fecha)
        $componentes = SaleDetail::join('products as padre', 'sale_details.product_id', '=', 'padre.id')
            ->join('product_compositions as pc', 'padre.id', '=', 'pc.product_id')
            ->join('products as comp', 'pc.component_id', '=', 'comp.id')
            ->where('sale_details.sale_id', $id)
            ->where('sale_details.status', '1')
            ->select([
                'sale_details.id as parent_id',                            // vincula al padre
                DB::raw('1 as is_component'),                             // marca línea “componente”
                'comp.name as nameprod',
                'comp.code',
                DB::raw(' (pc.quantity * sale_details.quantity) as quantity'), // qty del componente
                DB::raw('NULL as lote_codigo'),
                DB::raw('NULL as lote_fecha_vencimiento'),
                'sale_details.porc_iva',
                'sale_details.iva',
                'sale_details.porc_otro_impuesto',
            ]);

        // 4) Unión y obtención final
        $saleDetails = $baseDetails
            ->unionAll($componentes)
            ->orderBy('parent_id')
            ->orderBy('is_component')
            ->get();

        // Totales
        $saleDetailCount = $saleDetails->count();
        $totalQuantity    = $saleDetails->sum('quantity');

        // Genera PDF
        $pdf = PDF::loadView('sale.comanda', compact('sale', 'saleDetails', 'saleDetailCount', 'totalQuantity'));
        return $pdf->stream('sale.pdf');
    }
}

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
use Maatwebsite\Excel\Facades\Excel;

/* $sale = Sale::with('third')->findOrFail($id);
            $sale = Sale::with('third')->whereHas('third', function ($query) {
                $query->where('status', 1);
            })->findOrFail($id); */

class exportFacturaController extends Controller
{
    public function showFactura($id)
    {
        $sale = Sale::join('thirds as third', 'sales.third_id', '=', 'third.id')
            ->join('users as u', 'sales.user_id', '=', 'u.id')
            ->join('centro_costo as c', 'sales.centrocosto_id', '=', 'c.id')
            ->leftJoin('formapagos as fp', 'sales.forma_pago_tarjeta_id', '=', 'fp.id')
            ->leftJoin('formapagos as fpt2', 'sales.forma_pago_tarjeta2_id', '=', 'fpt2.id')
            ->leftJoin('formapagos as fpt3', 'sales.forma_pago_tarjeta3_id', '=', 'fpt3.id')
            ->leftJoin('formapagos as fp2', 'sales.forma_pago_otros_id', '=', 'fp2.id')
            ->leftJoin('formapagos as fp3', 'sales.forma_pago_credito_id', '=', 'fp3.id')
            ->select(
                'sales.*',
                'u.name as nameuser',
                'third.name as namethird',
                'third.celular as celularcliente',
                'fp.nombre as formapago1',
                'fpt2.nombre as formapagot2',
                'fpt3.nombre as formapagot3',
                'fp2.nombre as formapago2',
                'fp3.nombre as formapago3',
                'third.identification',
                'third.direccion',
                'c.name as namecentrocosto',
                'third.porc_descuento',
                'sales.total_iva',
                'sales.vendedor_id',
                'c.prefijo',
                'c.direccion',
                'c.resolucion_dian',
                'c.desde',
                'c.hasta',
                'c.fecha_inicial',
                'c.fecha_final'
            )
            ->where('sales.id', $id)
            ->first();

        $saleDetails = SaleDetail::join('products as pro', 'sale_details.product_id', '=', 'pro.id')
            ->leftJoin('lotes as lot', 'sale_details.lote_id', '=', 'lot.id')
            ->select(
                'sale_details.*',
                'pro.name as nameprod',
                'pro.code',
                'lot.codigo as lote_codigo',
                'lot.fecha_vencimiento as lote_fecha_vencimiento',
                'sale_details.porc_iva',
                'sale_details.iva',
                'sale_details.porc_otro_impuesto'
            )
            ->where('sale_details.sale_id', $id)
            ->where('sale_details.status', '1')
            ->get();

        // Se calcula la cantidad de registros de detalles
        $saleDetailCount = $saleDetails->count();

        // Se calcula la cantidad de Peso o unidades del total de productos de la venta.
        $totalQuantity = $saleDetails->sum('quantity');


        $totalIva = $saleDetails->sum('iva');

        $totalOtroImp = $saleDetails->sum('otro_impuesto');

        $totalIC = $saleDetails->sum('impoconsumo');

        $totalDesProd = $saleDetails->sum('descuento');

        $totalDesClient= $saleDetails->sum('descuento_cliente');        

        $totalApagar = $saleDetails->sum('total');


        $pdf = PDF::loadView('sale.reporte', compact('sale', 'saleDetails', 'saleDetailCount', 'totalQuantity', 'totalIva', 'totalOtroImp', 'totalIC', 'totalDesProd', 'totalDesClient', 'totalApagar'));
        return $pdf->stream('sale.pdf');
    }
}

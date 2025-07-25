<?php

namespace App\Http\Controllers\compensado;

use App\Http\Controllers\Controller;
use App\Models\caja\Caja;
use App\Models\compensado\Compensadores;
use App\Models\Compensador;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;


class ordencomprapdfCompensadoController extends Controller
{
    public function ordencomprapdfCompensado($id)
    {

        //   dd($comp);

        // 9) Fecha en español
        Carbon::setLocale('es');

        $comp = Compensador::with(['third', 'user', 'store', 'formapago', 'centroCosto'])
            ->findOrFail($id);

        // dd($comp);  

        // Usando el accesor:
        $fechaCierre = $comp->fecha_compensado_formatted;

        $compDetails = DB::table('compensadores_details as comp_de')
            ->join('products as pro', 'comp_de.products_id', '=', 'pro.id')
            ->select('comp_de.*', 'pro.name as nameprod', 'pro.code')
            ->where('comp_de.compensadores_id', $id)
            ->where('comp_de.status', '1')
            ->get();

        $total_weight = 0;
        $total_descuento = 0;
        $total_iva = 0;
        $total_otro_impuesto = 0;
        $total_impoconsumo = 0;
        $total_precio = 0;
        $total_subtotal = 0;
        $total_cotiza = 0;

        foreach ($compDetails as $item) {
            $total_weight += $item->peso_cotiza;
            $total_descuento += $item->descuento_cotiza;
            $total_iva += $item->iva_cotiza;
            $total_otro_impuesto += $item->otro_imp_cotiza;
            $total_impoconsumo += $item->impoconsumo_cotiza;
            $total_precio += $item->precio_cotiza;
            $total_subtotal += $item->subtotal_cotiza;
            $total_cotiza += $item->total_cotiza;
        }

        // dd($total_weight);



        $pdfCompensado = PDF::loadView('compensado.orden_compra_pdf', compact('compDetails', 'comp', 'fechaCierre', 'total_weight', 'total_descuento', 'total_iva', 'total_otro_impuesto', 'total_impoconsumo', 'total_precio', 'total_subtotal', 'total_cotiza'));
        return $pdfCompensado->stream('compensado.orden_compra_pdf');
        //return $pdfCompensado->download('sale.pdf');
    }
}

<?php

namespace App\Http\Controllers\inventory;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\centros\Centrocosto;
use App\Models\Centro_costo_product;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Spatie\Activitylog\Facades\Activity;

class CentroCostoProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* $category = Category::whereIn('id', [1, 2, 3, 4, 5, 6, 7, 8, 9])->orderBy('name', 'asc')->get(); */
        $category = Category::orderBy('name', 'asc')->get();
        //  $centros = Store::Where('status', 1)->get();
        $centros = Store::orderBy('name', 'asc')->get();
        $centroCostoProductos = Centro_costo_product::all();

        $newToken = Crypt::encrypt(csrf_token());

        return view("inventory.centro_costo_products", compact('category', 'centros', 'centroCostoProductos'));

        // return view('hola');
        //  return view('inventory.diary');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * 
     * 
     *   $data = DB::table('centro_costo_products as ccp')
            ->join('products as pro', 'pro.id', '=', 'ccp.products_id')
            ->join('categories as cat', 'pro.category_id', '=', 'cat.id')
            ->select(
                'cat.name as namecategoria',
                'pro.name as nameproducto',
                'pro.id as productId',
                'ccp.invinicial as invinicial',
                'ccp.fisico as fisico',
                'pro.cost as costo',
            )
            ->where('ccp.centrocosto_id', $centrocostoId)
            ->where('pro.category_id', $categoriaId)
            ->where('pro.status', 1)
            ->get();

     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $centrocostoId = $request->input('centrocostoId');
        $categoriaId   = $request->input('categoriaId');

        $data = DB::table('inventarios as inv')
            // Joins
            ->join('products   as pro', 'pro.id',      '=', 'inv.product_id')
            ->join('categories as cat', 'cat.id',      '=', 'pro.category_id')
            ->join('lotes      as lot', 'lot.id',      '=', 'inv.lote_id')
            ->join('stores     as s',   's.id',        '=', 'inv.store_id')
            // Select (incluimos `lot.id as loteId`)
            ->select([
                'cat.name   as namecategoria',
                'pro.name   as nameproducto',
                'pro.id     as productId',
                'inv.stock_ideal        as stockideal',
                'inv.stock_fisico       as fisico',
                'inv.cantidad_diferencia as diferencia',
                'pro.cost   as costo',
                'lot.id     as loteId',         // ← Lo agregamos
                'lot.codigo as lotecodigo',
                'lot.fecha_vencimiento as lotevence',
            ])
            // Filtros
            ->when($centrocostoId, fn($q) => $q->where('inv.store_id', $centrocostoId))
            ->when($categoriaId,   fn($q) => $q->where('pro.category_id', $categoriaId))
            ->where('pro.status', 1)
            ->get();

        /*   // Formateo de algunos campos
        foreach ($data as $item) {
            $item->stockideal = number_format($item->stockideal, 2, ',', '.');
            $item->diferencia = number_format($item->diferencia, 2, ',', '.');
            // si quisieras dar formato al físico o al costo, aquí también 
        }
*/
        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function updateCcpInventory(Request $request)
    {
        $data = $request->validate([
            'productId'       => 'required|integer|exists:products,id',
            'centrocostoId'   => 'required|integer|exists:stores,id',
            'loteId'          => 'required|integer|exists:lotes,id',
            'fisico'          => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1) Obtengo el registro de inventario correspondiente
            $inv = Inventario::where('product_id', $data['productId'])
                ->where('store_id',   $data['centrocostoId'])
                ->where('lote_id',    $data['loteId'])
                ->firstOrFail();

            // 2) Calculo diferencia contra el stock ideal previo
            $stockIdealPrevio = $inv->stock_ideal;
            $diferencia       = $stockIdealPrevio - $data['fisico'];

            // 3) Actualizo el inventario físico y stock ideal
            $inv->update([
                'stock_fisico'        => $data['fisico'],
                'cantidad_diferencia' => $diferencia,
                'stock_ideal'         => $data['fisico'],
            ]);

            // 4) Primer log de ajuste de inventario
            Activity()
                ->causedBy(auth()->user())
                ->performedOn($inv)
                ->withProperties([
                    'before' => ['stock_ideal' => $stockIdealPrevio],
                    'after'  => [
                        'stock_fisico'        => $inv->stock_fisico,
                        'stock_ideal'         => $inv->stock_ideal,
                        'cantidad_diferencia' => $inv->cantidad_diferencia,
                    ],
                    'metadata' => [
                        'product_id' => $data['productId'],
                        'store_id'   => $data['centrocostoId'],
                        'lote_id'    => $data['loteId'],
                    ],
                ])
                ->useLog('ajustes_inventario')
                ->log('Ajuste de inventario realizado');

            // 5) Limpieza de campos de inventario (reseteo a 0)
            $beforeClean = $inv->only([
                'cantidad_inventario_inicial',
                'cantidad_compra_lote',
                'cantidad_alistamiento',
                'cantidad_compra_prod',
                'cantidad_prod_term',
                'cantidad_traslado',
                'cantidad_venta',
                'cantidad_notacredito',
                'stock_ideal',
                'stock_fisico',
                'cantidad_diferencia',
                'costo_unitario',
                'costo_total'
            ]);

            $inv->update([
                'cantidad_inventario_inicial' => 0.00,
                'cantidad_compra_lote'        => 0.00,
                'cantidad_alistamiento'       => 0.00,
                'cantidad_compra_prod'        => 0.00,
                'cantidad_prod_term'          => 0.00,
                'cantidad_traslado'           => 0.00,
                'cantidad_venta'              => 0.00,
                'cantidad_notacredito'        => 0.00,
                'stock_ideal'                 => 0.00,
                'cantidad_diferencia'         => 0.00,
                'costo_unitario'              => 0.00,
                'costo_total'                 => 0.00,
            ]);

            // 6) Segundo log: limpieza de inventario
            Activity()
                ->causedBy(auth()->user())
                ->performedOn($inv)
                ->withProperties([
                    'before' => $beforeClean,
                    'after'  => array_fill_keys(array_keys($beforeClean), 0.00),
                    'metadata' => [
                        'action'    => 'limpieza_campos_inventario',
                        'product_id' => $data['productId'],
                        'store_id'  => $data['centrocostoId'],
                        'lote_id'   => $data['loteId'],
                    ],
                ])
                ->useLog('ajustes_inventario')
                ->log('Campos de inventario reseteados a cero');

            // 7) Eliminación de movimientos de inventario que coincidan con la triada
            MovimientoInventario::where(function ($query) use ($data) {
                $query->where('store_destino_id', $data['centrocostoId'])
                    ->orWhere('store_origen_id',  $data['centrocostoId']);
            })
                ->where('lote_id',    $data['loteId'])
                ->where('product_id', $data['productId'])
                ->delete();
                
            // 8) Tercer log: eliminación de movimientos
            Activity()
                ->causedBy(auth()->user())
                ->performedOn($inv)
                ->withProperties([
                    'metadata' => [
                        'action'    => 'eliminacion_movimientos',
                        'product_id' => $data['productId'],
                        'store_id'  => $data['centrocostoId'],
                        'lote_id'   => $data['loteId'],
                    ],
                ])
                ->useLog('ajustes_inventario')
                ->log('Movimientos de inventario actualizados');

            DB::commit();

            return response()->json([
                'status'  => 1,
                'message' => 'Inventario actualizado, movimientos actualizados correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 0,
                'message' => 'Error al procesar la actualización.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}

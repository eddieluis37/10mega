<?php

namespace App\Http\Controllers\inventory;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\centros\Centrocosto;
use App\Models\Centro_costo_product;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;


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
        $categoriaId = $request->input('categoriaId');

        $data = DB::table('inventarios as inv')
            // Productos y su categoría
            ->join('products as pro', 'pro.id', '=', 'inv.product_id')
            ->join('categories as cat', 'cat.id', '=', 'pro.category_id')

            // Para obtener el código de lote
            ->join('lotes as lot', 'lot.id', '=', 'inv.lote_id')

            // Para filtrar inventarios según el centro de costo de la tienda
            ->join('stores as s', 's.id', '=', 'inv.store_id')

            // Campos a seleccionar
            ->select([
                'cat.name as namecategoria',
                'pro.name as nameproducto',
                'pro.id as productId',
                'inv.stock_ideal as stockideal',
                'inv.stock_fisico as fisico',
                'inv.cantidad_diferencia as diferencia',    
                'pro.cost as costo',
                'lot.codigo as lotecodigo',
                'lot.fecha_vencimiento as lotevence'
            ])

            // Filtros
            ->where('inv.store_id', $centrocostoId)
            ->where('pro.category_id',   $categoriaId)
            ->where('pro.status',        1)

            ->get();


        // return response()->json(['data' => $data]);
        return datatables()->of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function updateCcpInventory()
    {
        $productId = request('productId');
        $centrocostoId = request('centrocostoId');
        $fisico = request('fisico');

        DB::table('centro_costo_products')
            ->where('products_id', $productId)
            ->where('centrocosto_id', $centrocostoId)
            ->update(['fisico' => $fisico]);

        return response()->json(['success' => 'true']);
    }
}

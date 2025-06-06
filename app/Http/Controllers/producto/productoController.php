<?php

namespace App\Http\Controllers\producto;

use App\Http\Controllers\Controller;

use App\Models\caja\Caja;
use App\Models\Category;
use App\Models\centros\Centrocosto;
use App\Models\Third;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Products\Meatcut;
use App\Http\Controllers\metodosgenerales\metodosrogercodeController;
use App\Models\Brand;
use App\Models\Brand_third;
use App\Models\Category_comerciales;
use App\Models\Levels_products;
use App\Models\Listaprecio;
use App\Models\Listapreciodetalle;
use App\Models\Productcomposition;
use App\Models\Products\Unitofmeasure;

use App\Models\Subcategory_comerciales;

class productoController extends Controller
{
    public function getProductos()
    {
        $productos = Product::all(); // Asegúrate de tener el modelo correcto
        return response()->json($productos);
    }

    public function select2(Request $request)
    {
        $search = $request->q;
        $products = Product::where('status', 1)
            ->where('name', 'like', "%$search%")
            ->select('id', 'name as text')
            ->limit(10)
            ->get();
        return response()->json($products);
    }


    public function index()
    {
        $categorias = Category::orderBy('id')->get();
        $categoriasComerciales = Category_comerciales::orderBy('id')->get();
        $SubcategoriasComerciales = Subcategory_comerciales::orderBy('id')->get();
        $proveedores = Third::Where('proveedor', 1)->get();
        $niveles = Levels_products::Where('status', 1)->get();
        $presentaciones = Unitofmeasure::Where('status', 1)->get();
        $familias = Meatcut::Where('status', 1)->get();

        $brandsThirds = Brand::orderBy('id')->get();

        $usuario = User::WhereIn('id', [9, 11, 12])->get();

        $centros = Centrocosto::WhereIn('id', [1])->get();
        return view("producto.index", compact('usuario', 'brandsThirds', 'categorias', 'categoriasComerciales', 'SubcategoriasComerciales', 'proveedores', 'niveles', 'presentaciones', 'familias',  'centros'));
    }

    public function show()
    {
        $data = DB::table('products as p')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->join('meatcuts as cut', 'p.meatcut_id', '=', 'cut.id')
            //  ->join('centro_costo as centro', 'p.centrocosto_id', '=', 'centro.id')
            ->select('p.*', 'c.name as namecategorias', 'cut.name as namefamilia')
            /*  ->where('p.status', 1) */
            ->get();

        //     return $data;
        //$data = Compensadores::orderBy('id','desc');
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function ($data) {
                $currentDateTime = Carbon::now();

                if ($data->status == 1) {
                    $btn = '
                         <div class="text-center">   
                         
                         <button class="btn btn-dark" title="Editar producto" onclick="edit(' . $data->id . ');">
						    <i class="fas fa-edit"></i>
					    </button>
                      
                         				
                         <button class="btn btn-dark" title="Borrar">
                             <i class="fas fa-trash"></i>
                         </button>
                         
                         </div>
                         ';
                } elseif ($data->status == 0) {
                    $btn = '
                         <div class="text-center">
                         <a href="caja/create/' . $data->id . '" class="btn btn-dark" title="CuadreCaja">
                            <i class="fas fa-money-check-alt"></i>
                         </a>
                        
                         <a href="caja/pdfCierreCaja/' . $data->id . '" class="btn btn-dark" title="PdfCuadreCajaOpen" target="_blank">
                         <i class="far fa-file-pdf"></i>
                         </a>

                         <a href="caja/showReciboCaja/' . $data->id . '" class="btn btn-dark" title="CuadreCajaCerrado" target="_blank">
                         <i class="fas fa-eye"></i>
                         </a>	

                         <button class="btn btn-dark" title="Borrar">
                         <i class="fas fa-trash"></i>
                         </button>
                       
                         </div>
                         ';
                    //ESTADO Cerrada
                } else {
                    $btn = '
                         <div class="text-center">
                         <a href="caja/showReciboCaja/' . $data->id . '" class="btn btn-dark" title="CuadreCajaCerrado" target="_blank">
                         <i class="far fa-file-pdf"></i>
                         </a>
                         <button class="btn btn-dark" title="Borra" disabled>
                             <i class="fas fa-trash"></i>
                         </button>
                       
                         </div>
                         ';
                }
                return $btn;
            })

            ->rawColumns(['fecha1', 'fecha2', 'inventory', 'action'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */



    public function storeEnDesarrollo(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validación previa (ya la tienes)

            // Validación adicional para combos/recetas
            if (in_array($request->product_type, ['combo', 'receta'])) {
                if (!is_array($request->componentes) || count($request->componentes) === 0) {
                    return response()->json([
                        'status' => 0,
                        'errors' => ['componentes' => ['Debe agregar al menos un producto al ' . $request->product_type]],
                    ], 422);
                }
            }

            $getReg = Product::find($request->productoId);
            $prod = $getReg ?? new Product();

            // Datos generales del producto (como ya lo haces)
            $prod->category_id       = $request->categoria;
            $prod->brand_id          = $request->marca;
            $prod->level_product_id  = $request->nivel;
            $prod->unitofmeasure_id  = $request->presentacion;
            $prod->quantity          = $request->quantity;
            $prod->meatcut_id        = $request->familia;
            $prod->name              = $request->subfamilia;
            $prod->code              = $request->code;
            $prod->barcode           = $request->codigobarra;
            $prod->iva               = $request->impuestoiva;
            $prod->otro_impuesto     = $request->isa;
            $prod->impoconsumo       = $request->impoconsumo;
            $prod->status            = '1';
            $prod->alerts            = '10';
            $prod->type              = $request->product_type; // Debes tener esta columna
            $prod->save();

            // Guardar componentes si es combo o receta
            if (in_array($prod->type, ['combo', 'receta'])) {
                // Eliminar componentes anteriores si existe
                DB::table('product_compositions')->where('product_id', $prod->id)->delete();

                foreach ($request->componentes as $componente) {
                    // Si en el formulario el campo se llama product_id:
                    $componentId = $componente['product_id'] ?? null;
                    $cantidad    = $componente['cantidad']   ?? null;

                    if (! $componentId || ! $cantidad) {
                        // saltar o lanzar error
                        continue;
                    }

                    DB::table('product_compositions')->insert([
                        'product_id'   => $prod->id,
                        'component_id' => $componentId,
                        'quantity'     => $cantidad,
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'status'     => 1,
                'message'    => 'Producto ' . ($getReg ? 'actualizado' : 'creado') . ' con éxito',
                'registroId' => $prod->id,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 0,
                'error'  => $th->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Regla para el campo code
            if ($request->productoId) {
                $codeRule = 'required|unique:products,code,' . $request->productoId;
            } else {
                $codeRule = 'required|unique:products,code';
            }

            // Regla para el campo name (subfamilia)
            if ($request->productoId) {
                $nameRule = 'required|unique:products,name,' . $request->productoId;
            } else {
                $nameRule = 'required|unique:products,name';
            }


            // Reglas dinámicas
            $rules = [
                'productoId'    => 'required',
                'categoriaerp'     => 'required',
                'subcategoriaerp'     => 'required',
                'categoriaweb'     => 'required',
                'subcategoriaweb'     => 'required',
                'marca'       => 'required',
                'nivel'       => 'required',
                'presentacion'       => 'required',
                'nameproducto'    => $nameRule,
                'code'          => $codeRule,
                'impuestoiva'   => 'required|numeric',
                'isa'           => 'required|numeric',
                'impoconsumo'   => 'required|numeric',
            ];

            // Mensajes personalizados
            $messages = [
                'productoId.required'    => 'El producto es requerido',
                'categoriaerp.required'     => 'La categoría erp es requerida',
                'subcategoriaerp.required'     => 'La Subcategoría erp es requerida',
                'categoriaweb.required'     => 'La categoría web es requerida',
                'subcategoriaweb.required'     => 'La Subcategoría es requerida',
                'marca.required'         => 'La marca proveedora es requerida',
                'nivel.required'         => 'El nivel es requerido',
                'presentacion.required'         => 'La presentacion es requerida',
                'nameproducto.required'    => 'El nombre del producto es requerido',
                'nameproducto.unique'      => 'El nombre del producto ya existe, por favor ingrese uno diferente',
                'code.required'          => 'El código es requerido',
                'code.unique'            => 'El código ya existe, por favor ingrese uno diferente',
                'impuestoiva.required'   => 'El IVA es requerido',
                'impuestoiva.numeric'    => 'El IVA debe ser un número',
                'isa.required'           => 'El Imp. Saludable es requerido',
                'isa.numeric'            => 'El ISA debe ser un número',
                'impoconsumo.required'   => 'El Impoconsumo es requerido',
                'impoconsumo.numeric'    => 'El Impoconsumo debe ser un número',
            ];

            // Validar campos base
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validación adicional si es combo o receta
            if (in_array($request->product_type, ['combo', 'receta'])) {
                if (!is_array($request->componentes) || count($request->componentes) === 0) {
                    return response()->json([
                        'status' => 0,
                        'errors' => ['componentes' => ['Debe agregar al menos un producto al ' . $request->product_type]],
                    ], 422);
                }
            }

            // Se busca si existe un producto con el id proporcionado
            $getReg = Product::firstWhere('id', $request->productoId);

            if ($getReg == null) {
                // Creación de un nuevo producto
                $prod = new Product();
                $prod->category_id       = $request->categoriaerp;
                $prod->meatcut_id        = $request->subcategoriaerp;
                $prod->categories_comerciales_id       = $request->categoriaweb;
                $prod->subcategory_comerciales_id       = $request->subcategoriaweb;
                $prod->brand_id          = $request->marca;
                $prod->level_product_id  = $request->nivel;
                $prod->unitofmeasure_id  = $request->presentacion;
                $prod->quantity          = $request->quantity;

                $prod->name              = $request->nameproducto;
                $prod->code              = $request->code;
                $prod->barcode           = $request->codigobarra;
                $prod->iva               = $request->impuestoiva;
                $prod->otro_impuesto     = $request->isa;
                $prod->impoconsumo       = $request->impoconsumo;
                $prod->status            = '1'; // Activo
                $prod->alerts            = '10';
                $prod->type              = $request->product_type;

                $prod->save();

                // Registrar componentes si aplica
                $this->syncCompositions($prod->id, $request);

                // Llamadas a métodos para registrar el producto en otros módulos

                $this->CrearProductoEnListapreciodetalle();


                return response()->json([
                    'status'      => 1,
                    'message'     => "Producto: " . $prod->name . ' ' . 'Creado con ID: ' . $prod->id,
                    "registroId"  => $prod->id
                ]);
            } else {
                // Actualización del producto existente
                $updateProd = $getReg;
                $updateProd->category_id       = $request->categoriaerp;
                $updateProd->meatcut_id        = $request->subcategoriaerp;
                $updateProd->categories_comerciales_id       = $request->categoriaweb;
                $updateProd->subcategory_comerciales_id       = $request->subcategoriaweb;
                $updateProd->brand_id          = $request->marca;
                $updateProd->level_product_id  = $request->nivel;
                $updateProd->unitofmeasure_id  = $request->presentacion;
                $updateProd->quantity          = $request->quantity;

                $updateProd->name              = $request->nameproducto;
                $updateProd->code              = $request->code;
                $updateProd->barcode           = $request->codigobarra;
                $updateProd->iva               = $request->impuestoiva;
                $updateProd->otro_impuesto     = $request->isa;
                $updateProd->impoconsumo       = $request->impoconsumo;
                $updateProd->type              = $request->product_type;
                $updateProd->save();

                // Sincronizar componentes: eliminar los que no existan y actualizar/agregar nuevos
                $this->syncCompositions($updateProd->id, $request);

                return response()->json([
                    "status"      => 1,
                    "message"     => "Producto: " . $updateProd->name . ' ' . 'Editado con ID: ' . $updateProd->id,
                    "registroId"  => 0
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'array'  => (array) $th
            ]);
        }
    }

    /**
     * Sincroniza la tabla product_compositions según los componentes enviados.
     *
     * @param int     $productId
     * @param Request $request
     */
    protected function syncCompositions(int $productId, Request $request)
    {
        /* // Solo aplica para combos y recetas
        if (!in_array($request->product_type, ['combo', 'receta'])) {
            // Si previamente tenía componentes, los eliminamos
            DB::table('product_compositions')->where('product_id', $productId)->delete();
            return;
        } */

        // IDs de componentes enviados
        $incoming = collect($request->componentes)
            ->filter(fn($c) => !empty($c['product_id']) && !empty($c['cantidad']))
            ->mapWithKeys(fn($c) => [$c['product_id'] => $c['cantidad']]);

        // Eliminar composiciones que no están en la nueva lista
        DB::table('product_compositions')
            ->where('product_id', $productId)
            ->whereNotIn('component_id', $incoming->keys()->toArray())
            ->delete();

        // Insertar o actualizar cada componente
        foreach ($incoming as $componentId => $cantidad) {
            DB::table('product_compositions')->updateOrInsert(
                ['product_id' => $productId, 'component_id' => $componentId],
                ['quantity'   => $cantidad]
            );
        }
    }


    public function CrearProductoEnListapreciodetalle()
    {
        $ultimoId = Product::latest('id')->first()->id;
        $listaprecios = Listaprecio::all();
        foreach ($listaprecios as $listaprecio) {
            $listapreciodetalle = Listapreciodetalle::create([
                'listaprecio_id' => $listaprecio->id,
                'product_id' => $ultimoId,
            ]);
            $listapreciodetalle->save();
        }
    }

    public function edit($id)
    {
        $producto = Product::with([
            'category',
            'brand',
            'levelProduct',
            'unitofmeasure',
            'meatCut',
            'compositions.component'
        ])->findOrFail($id);

        // Transformar compositions para el frontend
        $componentes = $producto->compositions->map(function ($item) {
            return [
                'product_id'   => $item->component_id,
                'product_name' => $item->component->name ?? 'Sin nombre',
                'cantidad'     => $item->quantity,
            ];
        });

        return response()->json([
            'listadoproductos' => $producto,
            'componentes'      => $componentes,
        ]);
    }
}

<?php

namespace App\Http\Controllers\caja;

use App\Http\Controllers\Controller;

use App\Models\caja\Caja;
use App\Models\Category;
use App\Models\Centro_costo_product;
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
use App\Models\caja\Cajasalidaefectivo;
use App\Models\Levels_products;
use App\Models\Listaprecio;
use App\Models\Listapreciodetalle;
use App\Models\Products\Unitofmeasure;
use App\Models\shopping\shopping_enlistment;
use App\Models\shopping\shopping_enlistment_details;


class cajasalidaefectivoController extends Controller
{
    public function getProductos()
    {
        $productos = Product::all(); // Asegúrate de tener el modelo correcto
        return response()->json($productos);
    }

    public function index()
    {
        $categorias = Category::orderBy('id')->get();
        $terceros = Third::orderBy('name')->get();
        $niveles = Levels_products::Where('status', 1)->get();
        $presentaciones = Unitofmeasure::Where('status', 1)->get();
        $familias = Meatcut::Where('status', 1)->get();

        $brandsThirds = Brand::orderBy('id')->get();

        $usuario = User::WhereIn('id', [9, 11, 12])->get();

        $centros = Centrocosto::WhereIn('id', [1])->get();
        return view("caja_salida_efectivo.index", compact('usuario', 'brandsThirds', 'categorias', 'terceros', 'niveles', 'presentaciones', 'familias',  'centros'));
    }

    public function show(Request $request)
    {
        $data = DB::table('caja_salida_efectivo as csd')
            ->join('cajas as c', 'c.id', '=', 'csd.caja_id')
            ->leftJoin('thirds as t', 't.id', '=', 'csd.third_id')
            ->join('centro_costo as centro', 'c.centrocosto_id', '=', 'centro.id')
            ->join('users as u', 'c.cajero_id', '=', 'u.id')
            ->select([
                'csd.id',
                'csd.fecha_hora_salida',
                'c.id as turno',
                'u.name as name_cajero',
                'centro.name as name_centro_costo',
                'csd.vr_efectivo',
                't.name as recibe',    // el que recibe
                'csd.status',
            ])
            //  ->where('c.status', 1)               // turno vigente
            ->orderBy('csd.fecha_hora_salida', 'desc')
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()

            // formateo de fecha/hora
            ->editColumn('fecha_hora_salida', function ($row) {
                return $row->fecha_hora_salida
                    ? Carbon::parse($row->fecha_hora_salida)->format('Y-m-d H:i:s')
                    : '';
            })

            // formateo de valor
            ->editColumn('vr_efectivo', function ($row) {
                return '$' . number_format($row->vr_efectivo, 0, ',', '.');
            })

            // columna de acciones
            ->addColumn('action', function ($row) {
                if ($row->status) {
                    return '
                    <div class="text-center">
                      <button class="btn btn-sm btn-primary" onclick="editSalida(' . $row->id . ')">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button class="btn btn-sm btn-danger" onclick="deleteSalida(' . $row->id . ')">
                        <i class="fas fa-trash"></i>
                      </button>
                       <a href="caja-salida-efectivo/pdfFormatopos/' . $row->id . '" class="btn btn-dark" title="FormatoPOS" target="_blank">
                         <i class="far fa-file-pdf"></i>
                         </a>
                    </div>';
                }

                return '
                <div class="text-center">
                  <button class="btn btn-sm btn-secondary" disabled>
                    <i class="fas fa-lock"></i>
                  </button>
                </div>';
            })

            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        // 0) Limpio el valor **antes** de validar
        $cleanVrEfectivo = str_replace('.', '', $request->vr_efectivo);
        $request->merge(['vr_efectivo' => $cleanVrEfectivo]);

        try {
            // 1) Reglas de validación
            $rules = [
                'id'                   => ['nullable', 'exists:caja_salida_efectivo,id'],
                'vr_efectivo'          => ['required', 'numeric', 'min:0'],
                'concepto'             => ['required', 'string', 'min:5'],
                'third_id'             => ['required', 'exists:thirds,id'],
                'fecha_hora_salida'    => ['nullable', 'date'],
            ];

            // 2) Mensajes personalizados
            $messages = [
                'vr_efectivo.required'     => 'Debes ingresar un valor de efectivo.',
                'vr_efectivo.numeric'      => 'El valor debe ser numérico.',
                'vr_efectivo.min'          => 'El valor no puede ser negativo.',
                'concepto.required'        => 'El campo concepto es obligatorio.',
                'concepto.string'          => 'El concepto debe ser texto.',
                'concepto.min'             => 'El concepto debe tener al menos 5 caracteres.',
                'third_id.required'        => 'Debes seleccionar quién recibe.',
                'third_id.exists'          => 'El tercero seleccionado no es válido.',
                'fecha_hora_salida.date'   => 'La fecha de salida debe tener un formato válido.',
            ];

            // 3) Validar
            $v = Validator::make($request->all(), $rules, $messages);

            if ($v->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $v->errors(),
                ], 422);
            }

            $user = $request->user();
            $caja = Caja::where('cajero_id', $user->id)  // filtra por su ID
                ->where('estado', 'open')
                ->latest()
                ->first();

            if (! $caja) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'No se encontró una caja abierta para el cajero actual.'
                ]);
            }

            // 3) Crear o actualizar
            if (empty($request->id)) {
                // CREAR
                $salida = Cajasalidaefectivo::create([
                    'caja_id'           => $caja->id,
                    'vr_efectivo'       => $cleanVrEfectivo,
                    'concepto'          => $request->concepto,
                    'fecha_hora_salida' => $request->fecha_hora_salida ?? Carbon::now(),
                    'third_id'          => $request->third_id,
                    'status'            => true,
                ]);

                // Log Spatie
                activity()
                    ->performedOn($salida)
                    ->causedBy($request->user())
                    ->withProperties(['ip' => $request->ip()])
                    ->log('Creó salida de efectivo');

                return response()->json([
                    'status'     => 1,
                    'message'    => "Salida de Efectivo Creada con ID: {$salida->id}",
                    'registroId' => $salida->id,
                ]);
            } else {
                // EDITAR
                $salida = Cajasalidaefectivo::findOrFail($request->id);
                $salida->update([
                    'vr_efectivo'       => $cleanVrEfectivo,
                    'concepto'          => $request->concepto,
                    'fecha_hora_salida' => $request->fecha_hora_salida,
                    'third_id'          => $request->third_id,
                ]);

                // Log Spatie
                activity()
                    ->performedOn($salida)
                    ->causedBy($request->user())
                    ->withProperties(['ip' => $request->ip()])
                    ->log('Actualizó salida de efectivo');

                return response()->json([
                    'status'     => 1,
                    'message'    => "Salida de Efectivo ID {$salida->id} actualizada correctamente.",
                    'registroId' => 0,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'errors' => ['exception' => $th->getMessage()],
            ]);
        }
    }


    public function edit($id)
    {
        $productos = Product::where('id', $id)->first();
        return response()->json([
            "id" => $id,
            "listadoproductos" => $productos,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Caja  $caja
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Caja $caja)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Caja  $caja
     * @return \Illuminate\Http\Response
     */
    public function destroy(Caja $caja)
    {
        //
    }
}

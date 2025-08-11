@extends('layouts.theme.app')
@section('content')
<style>
    .input {
        height: 38px;
    }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row sales layout-top-spacing">
    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h4 class="card-title">
                    <b> Promociones</b>
                </h4>
                <ul class="tabs tab-pills">
                    <li>
                        <a href="javascript:void(0)" onclick="window.location.href = '../../promotions'"
                            class="tabmenu bg-dark" data-toggle="modal" data-target=""
                            title="Regresa al listado">Volver</a>
                    </li>
                </ul>
            </div>
            <div class="widget-content mt-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3 mt-3">
                            <div class="col-sm-12 col-md-3">
                                <label for="inputcentro" class="form-label">Centrocosto</label>
                                <select id="inputcentro" class="form-select select2">
                                    <option value="">Todos los centro costos</option>
                                    @foreach($centros as $c)
                                    <option value="{{$c->id}}" {{ $c->id == 1 ? 'selected' : '' }}>{{$c->name}}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-message"></span>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                <label for="inputstore" class="form-label">Bodega</label>
                                <select id="inputstore" class="form-select select2">
                                    <option value="">Todas las bodegas</option>
                                    @foreach($stores as $option)
                                    <option value="{{ $option['id'] }}" data="{{ $option }}">{{ $option['name'] }}
                                    </option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-message"></span>
                            </div>

                            <div class="col-sm-12 col-md-3">
                                <label for="inputcategoria" class="form-label">Categoria</label>
                                <select id="inputcategoria" class="form-select select2">
                                    <option value="-1">Seleccione categoria</option>
                                    <option value="">Todas las categorias</option>
                                    @foreach($categorias as $option)
                                    <option value="{{ $option->id }}" data="{{ $option }}">{{ $option->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-message"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="widget-content mt-2">
                <div class="card">
                    <div class="card-body">
                        <form id="form-detail">
                            <input type="hidden" id="ventaId" name="ventaId" value="{{$id}}">
                            <input type="hidden" id="regdetailId" name="regdetailId" value="0">
                            <input type="hidden" id="codigoBarras" name="codigoBarras" value="999999999">
                            <div class="row g-3">


                                <script>
                                    $(document).ready(function() {

                                    });
                                </script>
                                <div class="col-md-10">
                                    <div class="task-header">
                                        <div class="form-group">
                                            <label for="producto" class="form-label">Buscar producto</label>
                                            <input type="hidden" id="centrocosto" name="centrocosto"
                                                value="{{ $promotion[0]->status }}">
                                            <input type="hidden" id="cliente" name="cliente"
                                                value="{{ $promotion[0]->status }}">
                                            <input type="hidden" id="porc_descuento_cliente"
                                                name="porc_descuento_cliente" value="{{ $promotion[0]->status }}">

                                            <!-- Campos ocultos para enviar datos adicionales -->
                                            <input type="hidden" id="lote_id" name="lote_id" value="">
                                            <input type="hidden" id="inventario_id" name="inventario_id" value="">
                                            <input type="hidden" id="stock_ideal" name="stock_ideal" value="">
                                            <input type="hidden" id="store_id" name="store" value="">
                                            <input type="hidden" id="store_name" name="store_name" value="">

                                            <select class="form-control form-control-sm select2Prod" name="producto"
                                                id="producto" required>
                                                <option value="">Seleccione el producto</option>
                                                @foreach ($results as $result)
                                                <option value="{{ $result['inventario_id'] }}"
                                                    data-product-id="{{ $result['product_id'] }}"
                                                    data-lote-id="{{ $result['lote_id'] }}"
                                                    data-inventario-id="{{ $result['inventario_id'] }}"
                                                    data-stock-ideal="{{ $result['stock_ideal'] }}"
                                                    data-store-id="{{ $result['store_id'] }}"
                                                    data-store-name="{{ $result['store_name'] }}"
                                                    data-info="{{ $result['text'] }}">
                                                    {{ $result['text'] }}
                                                </option>
                                                @endforeach
                                            </select>

                                            <span class="text-danger error-message"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="" class="form-label">KG|QT</label>
                                        <div class="input-group flex-nowrap"">
										<input type=" text" id="quantity" name="quantity" class="form-control input" placeholder="EJ: 10.00">
                                            <span class="input-group-text" id="addon-wrapping">QT</span>
                                        </div>
                                        <span class="text-danger error-message"></span>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <label for="" class="form-label">Descuento</label>
                                    <div class="input-group flex-nowrap">
                                        <input type="text" id="porc_desc" name="porc_desc" class="form-control input"
                                            placeholder="">
                                        <span class="input-group-text" id="addon-wrapping">%</span>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="task-header">
                                        <div class="form-group">
                                            <label for="fecha_inicio" class="form-label">Fecha incio</label>
                                            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" placeholder="Last name" aria-label="Last name" value="{{date('Y-m-d')}}">
                                            <span class="text-danger error-message"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="task-header">
                                        <div class="form-group">
                                            <label for="hora_inicio" class="form-label">Hora inicial</label>
                                            <select class="form-control form-control-sm input" name="hora_inicio" id="hora_inicio" required>
                                                <option value="">Escoge hora inicio</option>
                                                @php
                                                $startTime = strtotime('06:00');
                                                $endTime = strtotime('17:00');
                                                $interval = 60 * 60; // 1 hour interval
                                                for ($i = $startTime; $i <= $endTime; $i +=$interval) { echo '<option value="' . date('H:i', $i) . '">' . date('h:i A', $i) . '</option>' ; } @endphp </select>
                                                    <span class="text-danger error-message"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="task-header">
                                        <div class="form-group">
                                            <label for="fecha_final" class="form-label">Fecha final</label>
                                            <input type="date" class="form-control" name="fecha_final" id="fecha_final" placeholder="Last name" aria-label="Last name" value="{{date('Y-m-d')}}">
                                            <span class="text-danger error-message"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="task-header">
                                        <div class="form-group">
                                            <label for="hora_final" class="form-label">Hora final</label>
                                            <select class="form-control form-control-sm input" name="hora_final" id="hora_final" required>
                                                <option value="">Escoge hora final</option>
                                                @php
                                                $startTime = strtotime('07:00');
                                                $endTime = strtotime('18:00');
                                                $interval = 60 * 60; // 1 hour interval
                                                for ($i = $startTime; $i <= $endTime; $i +=$interval) { echo '<option value="' . date('H:i', $i) . '">' . date('h:i A', $i) . '</option>' ; } @endphp </select>
                                                    <span class="text-danger error-message"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <label for="observations">Observaciones</label>
                                        <textarea class="form-control" id="observacion" name="observacion" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="" style="margin-top:50px;">
                                        <div class="d-grid gap-2">
                                            <button id="btnAdd" class="btn btn-primary btn-block">Añadir</button>
                                        </div>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="widget-content mt-1">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive mt-3">
                    <table id="tableDespostere" class="table table-sm table-striped table-bordered">
                        <thead class="text-white" style="background: #3B3F5C">
                            <tr>
                                <th class="table-th text-white">CENTROCOSTO</th>
                                <th class="table-th text-white">BODEGA</th>
                                <th class="table-th text-white">CATEGORIA</th>
                                <th class="table-th text-white">LOTE</th>
                                <th class="table-th text-white">FECHA.VENCE</th>
                                <th class="table-th text-white">PRODUCTO</th>
                                <th class="table-th text-white">CANT</th>
                                <th class="table-th text-white">%DESC</th>
                                <th class="table-th text-white">FECHA.INICIO</th>
                                <th class="table-th text-white">HORA.INICIO</th>
                                <th class="table-th text-white">FECHA.FINAL</th>
                                <th class="table-th text-white">HORA.FINAL</th>
                                <th class="table-th text-white">OBSERVACION</th>
                                <th class="table-th text-white">USUARIO</th>
                                <th class="table-th text-white text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyDetail">
                            @foreach($detalleVenta as $proddetail)
                            <tr>
                                <!-- CENTROCOSTO -->
                                <td>{{ $proddetail->centro_costo_name ?? '-' }}</td>

                                <!-- BODEGA -->
                                <td>{{ $proddetail->store_name ?? '-' }}</td>

                                <!-- CATEGORIA -->
                                <td>{{ $proddetail->category_name ?? '-' }}</td>

                                <!-- LOTE -->
                                <td>{{ $proddetail->lote_codigo ?? '-' }}</td>

                                <!-- FECHA.VENCE -->
                                <td>
                                    @if(!empty($proddetail->lote_fecha_vence))
                                    {{ \Carbon\Carbon::parse($proddetail->lote_fecha_vence)->format('Y-m-d') }}
                                    @else
                                    -
                                    @endif
                                </td>

                                <!-- PRODUCTO -->
                                <td>{{ $proddetail->nameprod ?? '-' }}</td>

                                <!-- CANT -->
                                <td>{{ number_format($proddetail->quantity, 2, '.', ',') }}</td>

                                <!-- %DESC -->
                                <td>{{ number_format($proddetail->porc_desc, 0, ',', '.') }}%</td>

                                <!-- FECHA.INICIO -->
                                <td>{{ optional($proddetail->fecha_inicio) ? \Carbon\Carbon::parse($proddetail->fecha_inicio)->format('Y-m-d') : '-' }}</td>

                                <!-- HORA.INICIO -->
                                <td>{{ $proddetail->hora_inicio ?? '-' }}</td>

                                <!-- FECHA.FINAL -->
                                <td>{{ optional($proddetail->fecha_final) ? \Carbon\Carbon::parse($proddetail->fecha_final)->format('Y-m-d') : '-' }}</td>

                                <!-- HORA.FINAL -->
                                <td>{{ $proddetail->hora_final ?? '-' }}</td>

                                <!-- OBSERVACION -->
                                <td>{{ $proddetail->observacion ?? '-' }}</td>

                                <!-- USUARIO -->
                                <td>{{ $proddetail->user_name ?? '-' }}</td>

                                <!-- ACCIONES -->
                                <td class="text-center">
                                    @if($promotion[0]->status == '0')
                                    <button class="btn btn-dark fas fa-edit" name="btnEdit"
                                        data-id="{{$proddetail->id}}" title="Editar">
                                    </button>
                                    <button class="btn btn-dark fas fa-trash" name="btnDown"
                                        data-id="{{$proddetail->id}}" title="Borrar">
                                    </button>
                                    @else
                                    <button class="btn btn-dark fas fa-edit" name="btnEdit" title="Editar" disabled>
                                    </button>
                                    <button class="btn btn-dark fas fa-trash" name="btnDown" title="Borrar" disabled>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot id="tabletfoot">
                            <tr>
                                <th>Totales</th>
                                <th></th>
                                <th></th>
                                <td></td>
                                <th></th>
                                <th></th>
                                <th>{{number_format($arrayTotales['TotalCantidad'], 0, ',', '.')}} </th>
                                <th>{{number_format($arrayTotales['TotalPorDesc'], 0, ',', '.')}} </th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>                                
                            </tr>
                        </tfoot>
                    </table>
                    @if($promotion[0]->status == '0')
                    <div class="col-md-12">
                        <form method="GET" action="registrar_pago/{{$id}}">
                            @csrf
                            <div class="col-md-12 text-right mt-1">
                                <button id="cargarInventarioBtn" type="submit"
                                    class="btn btn-success btn-block">Pagar</button>
                                <!-- <a href="registrar_pago/{{$id}}" target="_blank" class="btn btn-success">Pagar</a> -->
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const costoInput = document.getElementById("price");

        // Función para formatear el número con puntos
        function formatCurrency(value) {
            return value
                .replace(/\D/g, "") // Elimina caracteres que no sean dígitos
                .replace(/\B(?=(\d{3})+(?!\d))/g, "."); // Agrega puntos como separadores de miles
        }

        costoInput.addEventListener("input", function(e) {
            const value = e.target.value;
            e.target.value = formatCurrency(value);
        });

        costoInput.addEventListener("blur", function(e) {
            // Opcional: Agrega un "0" si el campo está vacío al salir
            if (!e.target.value) {
                e.target.value = "0";
            }
        });

        $('#storeDiv').hide(); // Ocultar el div store para prueba al cargar la página
        $('#cambiarContraseDiv').hide(); // 
    });
</script>

@endsection
@section('script')
<script src="{{asset('rogercode/js/promotion/rogercode-create.js')}}" type="module"></script>
@endsection
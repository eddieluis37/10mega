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
					<b> Ventas </b>
				</h4>
				<ul class="tabs tab-pills">
					<li>
						<a href="javascript:void(0)" onclick="window.location.href = '../../sales'" class="tabmenu bg-dark" data-toggle="modal" data-target="" title="Regresa al listado">Volver</a>
					</li>
				</ul>
			</div>
			<div class="widget-content mt-3">
				<div class="card">
					<div class="card-body">
						<div class="row g-3">
							<div class="col-md-4">
								<div class="task-header">
									<div class="form-group">
										<label for="" class="form-label">Centro costo</label>
										<p>{{$datacompensado[0]->namecentrocosto}}</p>
									</div>
								</div>
							</div>

							<div class="col-md-4">
								<div class="task-header">
									<div class="form-group">
										<label>Bodega</label>
										<div>
											<select class="form-control form-control-sm" name="tipobodega" id="tipobodega" required="">
											<!-- 	<option value="">Seleccione</option> -->
												<option value="AUTOSERVICIO">AUTOSERVICIO</option>
												<option value="BAR">BAR</option>
												<option value="PARRILLA">PARRILLA</option>
											</select>
											<span class="text-danger error-message"></span>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-4">
								<div class="task-header">
									<div class="form-group">
										<label for="" class="form-label">Cliente</label>
										<p>{{$datacompensado[0]->namethird}}</p>
									</div>
								</div>
							</div>

							<div class="col-md-4">
								<div class="task-header">
									<div class="form-group">
										<label for="date1" class="form-label">Fecha de venta</label>
										<input type="date" class="form-control" name="fecha" id="fecha" placeholder="Last name" aria-label="Last name" value="{{date('Y-m-d')}}">
									</div>
								</div>
							</div>

							<div class="col-md-4">
								<div class="task-header">
									<div class="form-group">
										<label for="" class="form-label">Consecutivo</label>
										<p>{{$datacompensado[0]->consecutivo}}</p>
									</div>
								</div>
							</div>

							<div class="col-md-4">
								<div class="task-header">
									<div class="form-group">
										<label for="" class="form-label">% Descuento cliente</label>
										<p>{{$datacompensado[0]->porc_descuento_cliente}}</p>
									</div>
								</div>
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
											<input type="hidden" id="centrocosto" name="centrocosto" value="{{ $datacompensado[0]->centrocosto_id }}">
											<input type="hidden" id="cliente" name="cliente" value="{{ $datacompensado[0]->third_id }}">
											<input type="hidden" id="porc_descuento_cliente" name="porc_descuento_cliente" value="{{ $datacompensado[0]->porc_descuento_cliente }}">

											<!-- Campos ocultos para enviar datos adicionales -->
											<input type="hidden" id="lote_id" name="lote_id" value="">
											<input type="hidden" id="inventario_id" name="inventario_id" value="">
											<input type="hidden" id="stock_ideal" name="stock_ideal" value="">
											<input type="hidden" id="store_id" name="store" value="">
											<input type="hidden" id="store_name" name="store_name" value="">

											<select class="form-control form-control-sm select2Prod" name="producto" id="producto" required>
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
									<label for="" class="form-label">Precio venta</label>
									<div class="input-group flex-nowrap">
										<span class="input-group-text" id="addon-wrapping">$</span>
										@can('ver_CambiarPrecioVenta')
										<!-- El usuario tiene permiso para editar -->
										<input type="text" id="price" name="price" class="form-control input" placeholder="">
										@else
										<!-- El usuario no tiene permiso, campo de solo lectura -->
										<input type="text" id="price" name="price" class="form-control input" readonly placeholder="">
										@endcan
									</div>
								</div>
								<div class="col-md-2">
									<label for="" class="form-label">I.V.A</label>
									<div class="input-group flex-nowrap">

										<input type="text" id="porc_iva" name="porc_iva" class="form-control input" readonly placeholder="">
										<span class="input-group-text" id="addon-wrapping">%</span>
									</div>
								</div>
								<div class="col-md-2">
									<label for="" class="form-label">I.U.P</label>
									<div class="input-group flex-nowrap">

										<input type="text" id="porc_otro_impuesto" name="porc_otro_impuesto" class="form-control input" readonly placeholder="">
										<span class="input-group-text" id="addon-wrapping">%</span>
									</div>
								</div>


								<div class="col-md-2">
									<label for="" class="form-label">I.A.C</label>
									<div class="input-group flex-nowrap">

										<input type="text" id="porc_impoconsumo" name="porc_impoconsumo" class="form-control input" readonly placeholder="">
										<span class="input-group-text" id="addon-wrapping">%</span>
									</div>
								</div>

								<div class="col-md-2">
									<label for="" class="form-label">Descuento</label>
									<div class="input-group flex-nowrap">

										<input type="text" id="porc_desc" name="porc_desc" class="form-control input" readonly placeholder="">
										<span class="input-group-text" id="addon-wrapping">%</span>
									</div>
								</div>

								<div class="col-md-2">
									<div class="" style="margin-top:30px;">
										<div class="d-grid gap-2">
											<button id="btnAdd" class="btn btn-primary btn-block">Añadir Producto</button>
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
								<th class="table-th text-white">Producto</th>
								<th class="table-th text-white">Cant</th>
								<th class="table-th text-white">Valor.U</th>
								<th class="table-th text-white">%Des</th>
								<th class="table-th text-white">Des</th>
								<th class="table-th text-white">{{$datacompensado[0]->porc_descuento_cliente}}%DCl</th>
								<th class="table-th text-white">Total.B</th>
								<th class="table-th text-white">%IVA</th>
								<th class="table-th text-white">IVA</th>
								<th class="table-th text-white">%I.S</th>
								<th class="table-th text-white">I.S</th>
								<th class="table-th text-white">%I.C</th>
								<th class="table-th text-white">I.C</th>
								<th class="table-th text-white">Total</th>
								<th class="table-th text-white text-center">Acciones</th>
							</tr>
						</thead>
						<tbody id="tbodyDetail">
							@foreach($detalleVenta as $proddetail)
							<tr>
								<!--td>{{$proddetail->id}}</td-->
								<td>{{$proddetail->nameprod}}</td>
								<td>{{ number_format($proddetail->quantity, 2, '.', '.')}}</td>
								<td>${{ number_format($proddetail->price, 0, ',', '.')}}</td>
								<td>{{ number_format($proddetail->porc_desc, 0, ',', '.')}}</td>
								<td>${{ number_format($proddetail->descuento, 0, ',', '.')}}</td>
								<td>${{ number_format($proddetail->descuento_cliente, 0, ',', '.')}}</td>
								<td>${{ number_format($proddetail->total_bruto, 0, ',', '.')}}</td>
								<td>{{ number_format($proddetail->porc_iva, 0, ',', '.')}}</td>
								<td>${{ number_format($proddetail->iva, 0, ',', '.')}}</td>
								<td>{{ number_format($proddetail->porc_otro_impuesto, 0, ',', '.')}}</td>
								<td>${{ number_format($proddetail->otro_impuesto, 0, ',', '.')}}</td>
								<td>{{ number_format($proddetail->porc_impoconsumo, 0, ',', '.')}}</td>
								<td>${{ number_format($proddetail->impoconsumo, 0, ',', '.')}}</td>
								<td>${{ number_format($proddetail->total, 0, ',', '.')}}</td>
								<td class="text-center">
									@if($datacompensado[0]->status == '0')
									<button class="btn btn-dark fas fa-edit" name="btnEdit" data-id="{{$proddetail->id}}" title="Editar">
									</button>
									<button class="btn btn-dark fas fa-trash" name="btnDown" data-id="{{$proddetail->id}}" title="Borrar">
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
								<th>${{number_format($arrayTotales['TotalBruto'], 0, ',', '.')}} </th>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<th>${{number_format($arrayTotales['TotalValorAPagar'], 0, ',', '.')}} </th>
							</tr>
						</tfoot>
					</table>
					@if($datacompensado[0]->status == '0')
					<div class="col-md-12">
						<form method="GET" action="registrar_pago/{{$id}}">
							@csrf
							<div class="col-md-12 text-right mt-1">
								<button id="cargarInventarioBtn" type="submit" class="btn btn-success btn-block">Pagar</button>
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
<script src="{{asset('rogercode/js/sale/rogercode-create.js')}}" type="module"></script>
@endsection
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
					<b> Orden de compra</b>
				</h4>
				<ul class="tabs tab-pills">
					<li>
						<a href="javascript:void(0)" onclick="window.location.href = '../../compensado'" class="tabmenu bg-dark" data-toggle="modal" data-target="" title="Regresa al listado">Volver</a>
					</li>
				</ul>
			</div>
			<div class="widget-content mt-3">
				<div class="card">
					<div class="card-body">
						<div class="row g-3">
							<div class="col-md-3">
								<div class="task-header">
									<div class="form-group">
										<label for="" class="form-label">Proveedor</label>
										<p>{{$datacompensado[0]->namethird}}</p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="task-header">
									<div class="form-group">
										<label for="" class="form-label">Bodega</label>
										<p>{{$datacompensado[0]->namestore}}</p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="task-header">
									<div class="form-group">
										<label for="" class="form-label">Centro de costo</label>
										<p>{{$datacompensado[0]->namecentrocosto}}</p>
									</div>
								</div>
							</div>
							<div class="col-md-2 mt-6">

							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="widget-content mt-3">
				<div class="card">
					<div class="card-body">
						<form id="form-detail">
							<input type="hidden" id="compensadoId" name="compensadoId" value="{{$id}}">
							<input type="hidden" id="regdetailId" name="regdetailId" value="0">
							<div class="row g-3">
								<div class="col-md-10">
									<div class="task-header">
										<div class="form-group">
											<label for="" class="form-label">Buscar producto</label>
											<input type="hidden" id="costo_prod" name="costo_prod" class="form-control input" readonly placeholder="">
											<select class="form-control form-control-sm select2Prod" name="producto" id="producto" required="">
												<option value="">Seleccione el producto</option>
												@foreach ($prod as $p)
												<option value="{{$p->id}}">Cod: {{$p->code}} - {{$p->name}}</option>
												@endforeach
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="" class="form-label">Precio cotizado</label>
										<div class="input-group flex-nowrap">
											<span class="input-group-text" id="addon-wrapping">$</span>
											<input type="text" id="precio_cotiza" name="precio_cotiza" class="form-control input" placeholder="EJ: 20.500">
										</div>
										<span class="text-danger error-message"></span>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="" class="form-label">KG|UND</label>
										<div class="input-group flex-nowrap">
											<input type="text" id="peso_cotiza" name="peso_cotiza" class="form-control input" placeholder="EJ: 10.00">
											<span class="input-group-text" id="addon-wrapping">QT</span>
										</div>
										<span class="text-danger error-message"></span>
									</div>
								</div>

								<div class="col-md-2">
									<label for="" class="form-label">I.V.A</label>
									<div class="input-group flex-nowrap">
										<input type="text" id="porc_iva_cotiza" name="porc_iva_cotiza" class="form-control input" placeholder="">
										<span class="input-group-text" id="addon-wrapping">%</span>
									</div>
								</div>
								<div class="col-md-2">
									<label for="" class="form-label">I.U.P</label>
									<div class="input-group flex-nowrap">
										<input type="text" id="porc_otro_imp_cotiza" name="porc_otro_imp_cotiza" class="form-control input" placeholder="">
										<span class="input-group-text" id="addon-wrapping">%</span>
									</div>
								</div>
								<div class="col-md-2">
									<label for="" class="form-label">I.A.C</label>
									<div class="input-group flex-nowrap">
										<input type="text" id="porc_impoconsumo_cotiza" name="porc_impoconsumo_cotiza" class="form-control input" placeholder="">
										<span class="input-group-text" id="addon-wrapping">%</span>
									</div>
								</div>
								<div class="col-md-2">
									<label for="" class="form-label">Descuento</label>
									<div class="input-group flex-nowrap">
										<input type="text" id="porc_descuento_cotiza" name="porc_descuento_cotiza" class="form-control input" placeholder="">
										<span class="input-group-text" id="addon-wrapping">%</span>
									</div>
								</div>
								<div class="col-md-2 d-flex justify-content-center align-items-center">
									<div style="margin-top:13px;">
										<div class="d-grid gap-2">
											<button id="btnAdd" class="btn btn-primary btn-block">Añadir_Producto</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="widget-content mt-3">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive mt-3">
							<table id="tableDespostere" class="table table-sm table-striped table-bordered">
								<thead class="text-white" style="background: #3B3F5C">
									<tr>
										<th class="table-th text-white">Producto</th>
										<th class="table-th text-white">Cant</th>
										<th class="table-th text-white">$Valor.U</th>
										<th class="table-th text-white">%Des</th>
										<th class="table-th text-white">$Des</th>
										<th class="table-th text-white">$Total.B</th>
										<th class="table-th text-white">%IVA</th>
										<th class="table-th text-white">$IVA</th>
										<th class="table-th text-white">%I.S</th>
										<th class="table-th text-white">$I.S</th>
										<th class="table-th text-white">%I.C</th>
										<th class="table-th text-white">$I.C</th>
										<th class="table-th text-white">$Total</th>
										<th class="table-th text-white text-center">Acciones</th>
									</tr>
								</thead>
								<tbody id="tbodyDetail">
									@foreach($detail as $proddetail)
									<tr>
										<td>{{$proddetail->nameprod}}</td>
										<td>{{($proddetail->peso_cotiza)}}</td>
										<td>{{ number_format($proddetail->precio_cotiza, 0, ',', '.')}}</td>										
										<td>{{ number_format($proddetail->porc_descuento_cotiza, 0, ',', '.')}}</td>
										<td>{{ number_format($proddetail->descuento_cotiza, 0, ',', '.')}}</td>
										<td>{{ number_format($proddetail->total_bruto_cotiza, 0, ',', '.')}}</td>
										<td>{{ number_format($proddetail->porc_iva_cotiza, 0, ',', '.')}}</td>
										<td>{{ number_format($proddetail->iva_cotiza, 0, ',', '.')}}</td>
										<td>{{ number_format($proddetail->porc_otro_imp_cotiza, 0, ',', '.')}}</td>
										<td>{{ number_format($proddetail->otro_imp_cotiza, 0, ',', '.')}}</td>
										<td>{{ number_format($proddetail->porc_impoconsumo_cotiza, 0, ',', '.')}}</td>
										<td>{{ number_format($proddetail->impoconsumo_cotiza, 0, ',', '.')}}</td>
										<td>{{ number_format($proddetail->total_cotiza, 0, ',', '.')}}</td>
										<td class="text-center">
											@if($status == 'true')
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
										<th>{{number_format($arrayTotales['pesoTotalGlobal'], 2, '.', '.')}}</td>
										<th>{{number_format($arrayTotales['totalGlobal'], 0, ',', '.')}} </th>
										<th>{{number_format($arrayTotales['totalPorcDesc'], 0, ',', '.')}} </th>
										<th>{{number_format($arrayTotales['totalDescCot'], 0, ',', '.')}} </th>
										<th>{{number_format($arrayTotales['totalBrutoCot'], 0, ',', '.')}} </th>
										<th>{{number_format($arrayTotales['totalPorcIvaCot'], 0, ',', '.')}} </th>
										<th>{{number_format($arrayTotales['totalIvaCot'], 0, ',', '.')}} </th>
										<th>{{number_format($arrayTotales['totalPorcOtroImpCot'], 0, ',', '.')}} </th>
										<th>{{number_format($arrayTotales['totalOtroImpCot'], 0, ',', '.')}} </th>
										<th>{{number_format($arrayTotales['totalPorcImpoCot'], 0, ',', '.')}} </th>
										<th>{{number_format($arrayTotales['totalImpoCot'], 0, ',', '.')}} </th>								
										<th>{{number_format($arrayTotales['totalCotiza'], 0, ',', '.')}} </th>
										<td class="text-center">
											<input type="hidden" id="cargarInventarioBtn" name="cargarInventarioBtn">
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="widget-content mt-3">
				<div class="card">
					<div class="card-body">

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		const costoInput = document.getElementById("precio_cotiza");

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
		// Limpia el mensaje de error al modificar el input de cantidad
		$("#lote").on("input", function() {
			$(this).closest(".form-group").find(".error-message").text("");
		});

		// Limpia el mensaje de error al modificar el input de cantidad
		$("#producto").on("input", function() {
			$(this).closest(".form-group").find(".error-message").text("");
		});

		// Limpia el mensaje de error al modificar el input de cantidad
		$("#pcompra").on("input", function() {
			$(this).closest(".form-group").find(".error-message").text("");
		});

		$("#pesokg").on("input", function() {
			$(this).closest(".form-group").find(".error-message").text("");
		});

	});
</script>
@endsection
@section('script')
<script src="{{asset('rogercode/js/compensado/rogercode-create-order.js')}}" type="module"></script>
@endsection
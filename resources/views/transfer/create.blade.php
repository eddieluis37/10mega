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
					<b> Traslado </b>
				</h4>
				<ul class="tabs tab-pills">
					<li>
						<a href="javascript:void(0)" onclick="window.location.href = '../../transfer'" class="tabmenu bg-dark" data-toggle="modal" data-target="" title="Regresa al listado">Volver</a>
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
										<label for="" class="form-label">Fecha de traslado</label>
										<p>{{$dataTransfer[0]->created_at}}</p>
									</div>
								</div>
							</div>

							<div class="col-md-4">
								<div class="task-header">
									<div class="form-group">
										<label for="" class="form-label">Bodega de origen</label>
										<input type="hidden" id="bodegaOrigen" name="bodegaOrigen" value="{{$dataTransfer[0]->bodega_origen_id}}">
										<p>{{$dataTransfer[0]->namecentrocostoOrigen}}</p>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="task-header">
									<div class="form-group">
										<label for="" class="form-label">Bodega de destino</label>
										<input type="hidden" id="bodegaDestino" name="bodegaDestino" value="{{$dataTransfer[0]->bodega_destino_id}}">
										<p>{{$dataTransfer[0]->namecentrocostoDestino}}</p>
									</div>
									<span class="text-danger error-message"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="widget-content mt-3" style="{{$display}}">
				<div class="card">
					<div class="card-body">
						<form id="form-detail">
							<input type="hidden" id="transferId" name="transferId" value="{{$dataTransfer[0]->id}}">
							<div class="row g-3"> <!-- Añadido justify-content-center para centrar los campos horizontalmente -->
								<div class="col-md-3">
									<div class="task-header">
										<div class="form-group">
											<label for="lote" class="form-label mt-0">Lote</label>
											<select class="form-control form-control-sm select2Lote" name="lote" id="lote" required>
												<option value="">Seleccione el lote</option>
												@foreach ($lotes as $l)
												<option value="{{ $l->id }}">{{ $l->codigo }}</option>
												@endforeach
											</select>
											<span class="text-danger error-message"></span>
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="task-header">
										<div class="form-group">
											<label for="" class="form-label">Buscar producto</label>
											<select class="form-control form-control-sm select2Prod" name="producto" id="producto" required>
												<option value="">Seleccione el producto</option>
											</select>
											<span class="text-danger error-message"></span>
										</div>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="" class="form-label">KG|UND a trasladar</label>
										<div class="input-group flex-nowrap">
											<input type="text" id="kgrequeridos" name="kgrequeridos" class="form-control" placeholder="EJ: 10.00">
											<span class="input-group-text" id="addon-wrapping">QT</span>
										</div>
										<span class="text-danger error-message"></span>
									</div>
								</div>
								<div class="col-md-2 text-center">
									<div class="" style="margin-top:30px;">
										<div class="d-grid gap-2">
											<button id="btnAddTransfer" class="btn btn-primary">Añadir</button>
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
						<div class="row">
							<div class="col-sm-3">
								<label for="pesoKgOrigen" class="form-label">TangibleOrigen</label>
								<div class="input-group flex-nowrap">
									<input type="text" id="pesoKgOrigen" name="pesoKgOrigen" value="{{$arrayProductsOrigin[0]->fisico_origen}}" class="form-control form-control" placeholder="0.00" readonly>
									<span class="input-group-text" id="addon-wrapping">QT</span>
								</div>
							</div>
							<div class="col-sm-3">
								<label for="stockOrigen" class="form-label">StockActualOrigen</label>
								<div class="input-group flex-nowrap">
									<input type="text" id="stockOrigen" name="stockOrigen" value="" class="form-control form-control" placeholder="0.00" readonly>
									<span class="input-group-text" id="addon-wrapping">QT</span>
								</div>
							</div>
							<div class="col-sm-3">
								<label for="pesoKgDestino" class="form-label">TangibleDestino</label>
								<div class="input-group flex-nowrap">
									<input type="text" id="pesoKgDestino" name="pesoKgDestino" value="" class="form-control form-control" placeholder="0.00" readonly>
									<span class="input-group-text" id="addon-wrapping">QT</span>
								</div>
							</div>
							<div class="col-sm-3">
								<label for="stockDestino" class="form-label">StockActualDestino</label>
								<div class="input-group flex-nowrap">
									<input type="text" id="stockDestino" name="stockDestino" value="" class="form-control form-control" placeholder="0.00" readonly>
									<span class="input-group-text" id="addon-wrapping">QT</span>
								</div>
							</div>
						</div>
						<div class="row mt-3">
							<div class="col-sm-3">
								<label for="costoOrigen" class="form-label">CostoOrigen</label>
								<div class="input-group flex-nowrap">
									<span class="input-group-text" id="addon-wrapping">$</span>
									<input type="text" id="costoOrigen" name="costoOrigen" value="" class="form-control form-control" placeholder="0.00" readonly>
								</div>
							</div>
							<div class="col-sm-3">
								<label for="costoTotalOrigen" class="form-label">CostoTotalOrigen</label>
								<div class="input-group flex-nowrap">
									<span class="input-group-text" id="addon-wrapping">$</span>
									<input type="text" id="costoTotalOrigen" name="costoTotalOrigen" value="" class="form-control form-control" placeholder="0.00" readonly>
								</div>
							</div>
							<div class="col-sm-3">
								<label for="costoDestino" class="form-label">CostoDestino</label>
								<div class="input-group flex-nowrap">
									<span class="input-group-text" id="addon-wrapping">$</span>
									<input type="text" id="costoDestino" name="costoDestino" value="" class="form-control form-control" placeholder="0.00" readonly>
								</div>
							</div>
							<div class="col-sm-3">
								<label for="costoTotalDestino" class="form-label">CostoTotalDestino</label>
								<div class="input-group flex-nowrap">
									<span class="input-group-text" id="addon-wrapping">$</span>
									<input type="text" id="costoTotalDestino" name="costoTotalDestino" value="" class="form-control form-control" placeholder="0.00" readonly>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			@php
			// Verificamos si el usuario autenticado está asociado a la bodega de origen de la transferencia
			$authorized = DB::table('store_user')
			->where('user_id', auth()->user()->id)
			->where('store_id', $dataTransfer[0]->bodega_destino_id)
			->exists();
			@endphp
			
			<div class="widget-content mt-3">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive mt-3">
							<table id="tableTransfer" class="table table-sm table-striped mt-1 table-bordered"> <!-- http://2puracarnes.test:8080/transfer/create/1  code-create.js showData -->
								<thead class="text-white" style="background: #3B3F5C">
									<tr>
										<th class="table-th text-white">LOTE</th>
										<th class="table-th text-white">Producto</th>
										<th class="table-th text-white">StkActOrigen</th>
										<th class="table-th text-white">QTAtrasladar</th>
										<th class="table-th text-white">NewStkOrigen</th>
										<th class="table-th text-white">StkActDestino</th>
										<th class="table-th text-white">NewStkDestino</th>
										<th class="table-th text-white">CostoUnit</th>
										<th class="table-th text-white">Subtotal</th>
										<th class="table-th text-white text-center">Acciones</th>
									</tr>
								</thead>
								<tbody id="tbodyDetail">
									@foreach($transfers as $proddetail)
									<tr>
										<!-- <td>{{$proddetail->id}}</td> -->
										<td>{{$proddetail->codigo}}</td>
										<td>{{$proddetail->nameprod}}</td>
										<td>{{ number_format($proddetail->actual_stock_origen, 2, '.', '.')}}</td>
										<td>
											@if($status == 'true' && $statusInventory == 'false')
											<input type="text" class="form-control-sm" data-id="{{ $proddetail->products_id }}" id="{{ $proddetail->id }}" value="{{ $proddetail->kgrequeridos }}" placeholder="Ingresar" size="5">
											<!-- Contenedor para mostrar el mensaje de error -->
											<span class="text-danger error-message"></span>
											@else
											<p>{{ number_format($proddetail->kgrequeridos, 2, '.', '.') }}</p>
											@endif
										</td>

										<td>{{ number_format($proddetail->nuevo_stock_origen, 2, '.', '.')}}</td>

										<td>{{ number_format($proddetail->actual_stock_destino, 2, '.', '.')}}</td>

										<td>{{ number_format($proddetail->nuevo_stock_destino, 2, '.', '.')}}</td>
										<td>${{ number_format($proddetail->costo_unitario_origen, 0, ',', '.')}}</td>
										<td>${{ number_format($proddetail->subtotal_traslado, 0, ',', '.')}}</td>
										<td class="text-center">
											@if($status == 'true' && $statusInventory == 'false')
											<button type="button" name="btnDownReg" data-id="{{$proddetail->id}}" class="btn btn-dark btn-sm fas fa-trash" title="Cancelar">
											</button>
											@else
											<button type="button" name="" class="btn btn-dark btn-sm fas fa-trash" title="Cancelar" disabled>
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
										<th>{{number_format($arrayTotales['kgTotalRequeridos'], 2 , '.', '.')}}</th>
										<th>{{number_format($arrayTotales['newTotalStock'], 2, '.', '.')}}</th>
										<th></th>
										<th>{{number_format($arrayTotales['newTotalStockDestino'], 2, '.', '.')}}</th>
										<th></th>
										<th>${{number_format($arrayTotales['totalTraslado'], 0, ',', '.')}}</th>
										<th class="text-center">
											@if($dataTransfer[0]->inventario == 'pending' && $authorized)
											<button class="btn btn-success btn-sm" id="addShopping">Aceptar_Traslado</button>
											@endif
										</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
    // Definimos la variable en JavaScript
    const isAuthorized = @json(
        DB::table('store_user')
            ->where('user_id', auth()->user()->id)
            ->where('store_id', $dataTransfer[0]->bodega_destino_id) // Ajusta según corresponda
            ->exists()
    );
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
	$(document).ready(function() {

		// Limpiar mensajes de error al seleccionar un campo
		$('#producto').change(function() {
			$(this).siblings('.error-message').text(''); // Limpiar mensaje de error
		});
		// Limpiar mensajes de error del campo kgrequeridos

		$('#kgrequeridos').change(function() {
			$(this).siblings('.error-message').text(''); // Limpiar mensaje de error			
		});

	});
</script>
@endsection
@section('script')
<script src="{{asset('code/js/transfer/code-create.js')}}" type="module"></script>
@endsection
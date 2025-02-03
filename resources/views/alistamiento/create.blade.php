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
					<b> Transformación | Categoria </b>
				</h4>
				<ul class="tabs tab-pills">
					<li>
						<a href="javascript:void(0)" onclick="window.location.href = '../../alistamiento'" class="tabmenu bg-dark" data-toggle="modal" data-target="" title="Regresa al listado">Volver</a>
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
										<label for="" class="form-label">Fecha de compra</label>
										<p>{{$dataAlistamiento[0]->created_at}}</p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="task-header">
									<div class="form-group">
										<label for="" class="form-label">Bodega</label>
										<input type="hidden" id="storeId" name="storeId" value="{{$dataAlistamiento[0]->store_id}}">
										<p>{{$dataAlistamiento[0]->namebodega}}</p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="task-header">
									<div class="form-group">
										<label for="" class="form-label">LotePadre</label>
										<p>{{$dataAlistamiento[0]->codigolote}}</p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="task-header">
									<div class="form-group">
										<label for="" class="form-label">LoteHijos</label>
										<p>{{$dataAlistamiento[0]->codigolotehijo}}</p>
									</div>
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
							<input type="hidden" id="alistamientoId" name="alistamientoId" value="{{$dataAlistamiento[0]->id}}">
							<div class="row g-3">
								<div class="col-md-4">
									<div class="task-header">
										<div class="form-group">
											<label for="" class="form-label">Buscar corte padre</label>
											<input type="hidden" id="meatcutId" name="meatcutId" value="{{$dataAlistamiento[0]->meatcut_id}}">
											<input type="hidden" id="productopadreId" name="productopadreId" value="{{$dataAlistamiento[0]->productopadreId}}">
											<input type="hidden" id="storeId" name="storeId" value="{{$dataAlistamiento[0]->store_id}}">
											<input type="text" id="productoCorte" name="productoCorte" value="{{$dataAlistamiento[0]->name}}" class="form-control input" readonly>
											<!--select class="form-control form-control-sm select2Prod" name="productoCorte" id="productoCorte" required="">
											<option value="">Seleccione el producto</option>
											
					                    </select>-->
										</div>
									</div>
								</div>
								<div class="col-md-3">
									<label for="" class="form-label">Seleccionar hijo </label>
									<select class="form-control form-control-sm select2ProdHijos" name="producto" id="producto" required="">
									</select>
									<span class="text-danger error-message"></span>
								</div>
								<div class="col-md-3">
									<label for="" class="form-label">KG requeridos</label>
									<div class="input-group flex-nowrap">
										<input type="text" id="kgrequeridos" name="kgrequeridos" class="form-control input" placeholder="EJ: 10,00">
										<span class="input-group-text" id="addon-wrapping">KG</span>
									</div>
									<span class="text-danger error-message"></span>
								</div>
								<div class="col-md-2 text-center">
									<div class="" style="margin-top:30px;">
										<div class="d-grid gap-2">
											<button id="btnAddAlistamiento" class="btn btn-primary">Aceptar</button>
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
							<div class="col-md-3">
								<label for="" class="form-label">Stock actual</label>
								<div class="input-group flex-nowrap">
									<input type="text" id="stockCortePadre" name="stockCortePadre" value="{{$dataAlistamiento[0]->stockPadre}}" class="form-control" placeholder="10,00 kg" readonly>
									<span class="input-group-text" id="addon-wrapping">KG</span>
								</div>
							</div>
							<div class="col-md-3">
								<label for="" class="form-label">CostoPadre</label>
								<div class="input-group flex-nowrap">
									<span class="input-group-text" id="addon-wrapping">$</span>
									<input type="text" id="costoPadre" name="costoPadre" value="{{number_format( $dataAlistamiento[0]->costoPadre ,0, ',', '.' )}}" class="form-control" placeholder="10,00 kg" readonly>
								</div>
							</div>
							<div class="col-md-3">
								<label for="" class="form-label">Ultimo conteo fisico</label>
								<div class="input-group flex-nowrap">
									<input type="text" id="pesokg" name="pesokg" value="{{$dataAlistamiento[0]->cantidad_inicial}}" class="form-control" placeholder="180.40 kg" readonly>
									<span class="input-group-text" id="addon-wrapping">KG</span>
								</div>
							</div>
							<div class="col-md-3">
								<label for="" class="form-label">Nuevo stock padre</label>
								<div class="input-group flex-nowrap">
									<input type="text" id="newStockPadre" name="newStockPadre" value="{{$dataAlistamiento[0]->nuevo_stock_padre}}" class="form-control" placeholder="30,00 kg" readonly>
									<span class="input-group-text" id="addon-wrapping">KG</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="widget-content mt-3">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive mt-3">
							<table id="tableAlistamiento" class="table table-sm table-striped table-bordered">
								<thead class="text-white" style="background: #3B3F5C">
									<tr>
										<!--th class="table-th text-white">Item</th>-->
										<th class="table-th text-white">#</th>
										<th class="table-th text-white">Codigo</th>
										<th class="table-th text-white">Prodhijo</th>
										<th class="table-th text-white">Stactual</th>
										<th class="table-th text-white">Fisico</th>
										<th class="table-th text-white">kgREQ</th>
										<th class="table-th text-white">PRICEMIN</th>
										<th class="table-th text-white">TVENTA</th>
										<th class="table-th text-white">%VENTA</th>
										<th class="table-th text-white">CostoT</th>
										<th class="table-th text-white">CostKG</th>
										<th class="table-th text-white">UTILID</th>
										<th class="table-th text-white">%UTL</th>
										<th class="table-th text-white">N_st_hj</th>
										<th class="table-th text-white text-center">Acciones</th>
									</tr>
								</thead>
								<tbody id="tbodyDetail">
									@foreach($enlistments as $proddetail)
									<tr>
										<td>{{$proddetail->id}}</td>
										<td>{{$proddetail->code}}</td>
										<td>{{$proddetail->nameprod}}</td>
										<td>{{number_format($proddetail->stockHijo, 2, ',', '.')}}KG</td>
										<td>{{number_format($proddetail->fisico, 2, ',', '.')}}KG</td>
										<td>
											@if($status == 'true' && $statusInventory == 'false')
											<input type="text" class="form-control-sm" data-id="{{$proddetail->products_id}}" id="{{$proddetail->id}}" value="{{$proddetail->kgrequeridos}}" placeholder="Ingresar" size="5">
											@else
											<p>{{number_format($proddetail->kgrequeridos, 2, ',', '.')}}KG</p>
											@endif
										</td>
										<td>${{number_format($proddetail->price_fama, 0, ',', '.')}}</td>
										<td>${{number_format($proddetail->total_venta, 0, ',', '.')}}</td>
										<td>{{number_format($proddetail->porc_venta, 2, ',', '.')}}%</td>
										<td>${{number_format($proddetail->costo_total, 0, ',', '.')}}</td>
										<td>${{number_format($proddetail->costo_kilo, 0, ',', '.')}}</td>										
										<td>${{number_format($proddetail->utilidad, 0, ',', '.')}}</td>
										<td>{{number_format($proddetail->porc_utilidad, 2, ',', '.')}}%</td>
										<td>{{number_format($proddetail->newstock, 2, ',', '.')}}KG</td>
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
										<th></th>
										<th>Total</th>
										<th></th>
										<th></th>
										<th></th>
										<th>{{number_format($arrayTotales['kgTotalRequeridos'], 2, ',', '.')}}KG</th>
										<th>${{number_format($arrayTotales['totalPrecioMinimo'], 0, ',', '.')}}</th>
										<th>${{number_format($arrayTotales['totalVentaFinal'], 0, ',', '.')}}</th>
										<th>{{number_format($arrayTotales['totalPorcVenta'], 2, ',', '.')}}%</th>
										<th>${{number_format($arrayTotales['totalCostoTotal'], 0, ',', '.')}}</th>
										<th>${{number_format($arrayTotales['totalCostoKilo'], 0, ',', '.')}}</th>										
										<th>${{number_format($arrayTotales['totalUtilidad'], 0, ',', '.')}}</th>
										<th>{{number_format($arrayTotales['totalPorcUtilidad'], 2, ',', '.')}}%</th>											
										<th>{{number_format($arrayTotales['newTotalStock'], 2, ',', '.')}}KG</th>
										<th class="text-center">
											@if($dataAlistamiento[0]->inventario == 'pending')
											<button class="btn btn-success btn-sm" id="addShopping">Cargar_Inventario</button>
											@endif
										</th>
									</tr>
									<tr>
										<th></th>
										<th>Merma:{{number_format($arrayTotales['cantidadProcesar'], 2, ',', '.')}}</th>
										<th>%Merma: </th>
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
@endsection
@section('script')
<script src="{{asset('rogercode/js/alistamiento/rogercode-create.js')}}" type="module"></script>
@endsection
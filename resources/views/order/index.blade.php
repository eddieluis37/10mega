@extends('layouts.theme.app')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row sales layout-top-spacing">
	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>Ordenes | Listado </b>
				</h4>
				<ul class="tabs tab-pills">
					<li>
						<a href="javascript:void(0)" onclick="showModalcreate()" class="tabmenu bg-dark" data-toggle="modal" data-target="#modal-create-compensado" title="Nueva orden de pedido">Ordenar</a>
						<!-- <a href="javascript:void(0)" class="tabmenu bg-dark ml-2" id="storeVentaMostradorBtn"  title="Nueva venta por mostrador">Mostrador</a> -->
					</li>
				</ul>
			</div>

			<div class="widget-content">
				<div class="table-responsive">
					<table id="tableCompensado" class="table table-striped mt-1">
						<thead class="text-white" style="background: #3B3F5C">
							<tr>
								<th class="table-th text-white">#</th>
								<th class="table-th text-white">CLIENT</th>
								<th class="table-th text-white ">CE</th>
								<th class="table-th text-white">ST</th>
								<th class="table-th text-white">VALOR.O</th>
								<th class="table-th text-white">UTILIDAD</th>
								<th class="table-th text-white">DATE.ORDEN</th>
								<th class="table-th text-white">DATE.ENTRE</th>
								<th class="table-th text-white">VENDEDOR</th>
								<th class="table-th text-white text-center">Acciones</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<!-- modal -->
	<div class="modal fade" id="modal-create-compensado" aria-hidden="true" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content bg-dark text-white">
				<fieldset id="contentDisable">
					<form action="" id="form-compensado-res">
						<div class="modal-header bg-secondary">
							<h4 class="modal-title" style="color: white; font-weight: bold;">ORDENES DE PEDIDOS </h4>
							<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							@include('order.modal_create')
						</div>
						<div class="modal-footer">
							<button type="button" id="btnModalClose" class="btn btn-default" data-dismiss="modal">Cancelar</button>
							<button type="submit" id="btnAddVentaDomicilio" class="btn btn-primary">Aceptar</button>
						</div>
					</form>
				</fieldset>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->
</div>
@endsection
@section('script')
<script src="{{asset('rogercode/js/order/rogercode-order-index.js')}}"></script>
<script src="{{asset('rogercode/js/order/rogercode-create-update.js')}}" type="module"></script>
@endsection
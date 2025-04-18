@extends('layouts.theme.app')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row sales layout-top-spacing">
	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>Recibo de caja | Listado </b>
				</h4>
				<ul class="tabs tab-pills">
					<li>
						<a href="javascript:void(0)" onclick="showModalcreate()" class="tabmenu bg-dark" data-toggle="modal" data-target="#modal-create-pagocliente" title="Nueva venta por domicilio">Pago cliente</a>
						<!-- <a href="javascript:void(0)" class="tabmenu bg-dark ml-2" id="storeVentaMostradorBtn"  title="Nueva venta por mostrador">Mostrador</a> -->
					</li>
					<li>
						<a href="javascript:void(0)" onclick="showModalcreate()" class="tabmenu bg-dark" data-toggle="modal" data-target="#modal-create-recibodecaja" title="Nueva venta por domicilio">Nuevo recibo</a>
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
								<th class="table-th text-white">CLIENTE</th>
								<th class="table-th text-white ">TIPO</th>
								<th class="table-th text-white">ESTADO</th>
								<th class="table-th text-white">VR.T.DEUDA</th>
								<th class="table-th text-white">VR.T.PAGADO</th>
								<th class="table-th text-white">VR.N.SALDO</th>
								<th class="table-th text-white">DIA.HORA</th>
								<th class="table-th text-white">RECIBO</th>

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
	<div class="modal fade" id="modal-create-pagocliente" aria-hidden="true" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content bg-default">
				<fieldset id="contentDisable">
					<form action="{{ route('recibodecajas.payment') }}" id="form-compensado-res" method="POST">
						@csrf
						<div class="modal-header">
							<h4 class="modal-title">Registrar pago a cliente</h4>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<!-- Se incluye aquí el formulario con la tabla #tablePagoCliente -->
							@include('recibodecaja.modal_create_pagocliente')
						</div>
						<div class="modal-footer">
							<button type="button" id="btnModalClose" class="btn btn-default" data-dismiss="modal">Cancelar</button>
							<!-- Botón de envío: tipo "button" para controlar el evento manualmente -->
							<button type="button" id="btnAddCustomerPayment" class="btn btn-primary">Aceptar</button>
						</div>
					</form>
				</fieldset>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->

	<!-- Modal para mostrar el reporte cargado vía fetch -->
	<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">

				<div class="modal-body">
					<!-- Contenedor donde se insertará el HTML obtenido -->
					<div id="reportContent"></div>
				</div>
			</div>
		</div>
	</div>


</div>
{{-- Inyecta el arreglo en una variable global --}}
<script>
    window.formasPago = @json($formapagos);
</script>
@endsection
@section('script')

<script src="{{asset('rogercode/js/recibodecaja/rogercode-recibodecajas-index.js')}}"></script>
<script src="{{asset('rogercode/js/recibodecaja/rogercode-create-update.js')}}" type="module"></script>
@endsection
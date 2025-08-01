@extends('layouts.theme.app')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row sales layout-top-spacing">
	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>Caja | Listado </b>
				</h4>
				<ul class="tabs tab-pills">
					<li>
						<a href="javascript:void(0)" onclick="showModalcreate()" class="tabmenu bg-dark" data-toggle="modal" data-target="#modal-create-alistamiento" title="Crear nuevo turno">Crear Turno</a>
					</li>
				</ul>
			</div>

			<div class="widget-content">
				<div class="table-responsive">
					<table id="tableAlistamiento" class="table table-striped mt-1">
						<thead class="text-white" style="background: #3B3F5C">
							<tr>
								<th class="table-th text-white">T</th>
								<th class="table-th text-white ">CENTROCOSTO</th>
								<th class="table-th text-white ">CAJERO</th>
								<th class="table-th text-white ">BASE</th>
								<th class="table-th text-white ">ESTADO</th>
								<th class="table-th text-white">INICIO</th>
								<th class="table-th text-white">CIERRE</th>
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
	<div class="modal fade" id="modal-create-alistamiento" aria-hidden="true" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content bg-default">
				<fieldset id="contentDisable">
					<form action="" id="form-alistamiento">
						<div class="modal-header">
							<h4 class="modal-title">Crear turno</h4>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							@include('caja.modal_create')
						</div>
						<div class="modal-footer">
							<button type="button" id="btnModalClose" class="btn btn-default" data-dismiss="modal">Cancelar</button>
							<button type="submit" id="btnAddalistamiento" class="btn btn-primary">Aceptar</button>
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
				<!-- <div class="modal-header">
					<h5 class="modal-title">Reporte Validación del Cierre de Caja</h5>
					
					<a href="{{ route('caja.index') }}" class="btn btn-secondary ms-auto me-2" title="Ir a Principal">
						Volver a Caja
					</a>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
				</div>
 -->

				<div class="modal-body">
					<!-- Contenedor donde se insertará el HTML obtenido -->
					<div id="reportContent"></div>
				</div>
			</div>
		</div>
	</div>

</div>
@endsection
@section('script')
<script src="{{asset('rogercode/js/caja/code-index.js')}}"></script>
<script src="{{asset('rogercode/js/caja/rogercode-create-update.js')}}" type="module"></script>
@endsection
@extends('layouts.theme.app')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row sales layout-top-spacing">
	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>Promocion productos</b>
				</h4>
				<ul class="tabs tab-pills">					
					<li>
						<a href="javascript:void(0)"
							class="tabmenu bg-dark ml-2"
							id="storePromotionBtn"
							title="Nueva promocion">
							Crear
						</a>
					</li>					
				</ul>
			</div>

			<div class="widget-content">
				<div class="table-responsive">
					<table id="tableCompensado" class="table table-striped mt-1">
						<thead class="text-white" style="background: #3B3F5C">
							<tr>
								<th class="table-th text-white">#</th>
								<th class="table-th text-white">USUARIO</th>
								<th class="table-th text-white ">ESTADO</th>					
								<th class="table-th text-white">DATE.INICIO</th>
								<th class="table-th text-white">DATE.FINAL</th>								
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
	<div class="modal fade" id="modal-create-sale" aria-hidden="true" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content bg-dark text-white">
				<fieldset id="contentDisable">
					<form action="" id="form-compensado-res">
						<div class="modal-header bg-secondary">
							<h4 class="modal-title" style="color: white; font-weight: bold;">CREAR VENTA AUTOSERVICIO DOMICILIO </h4>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							@include('sale_autoservicio.modal_create')
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
<script src="{{asset('rogercode/js/promotion/index.js')}}"></script>
<script src="{{asset('rogercode/js/promotion/create-update.js')}}" type="module"></script>
@endsection
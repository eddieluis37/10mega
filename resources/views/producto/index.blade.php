@extends('layouts.theme.app')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row sales layout-top-spacing">
	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>Productos | Listado </b>
				</h4>
				<ul class="tabs tab-pills">
					<li>
						<a href="javascript:void(0)" onclick="showModalcreate()" class="tabmenu bg-dark" data-toggle="modal" data-target="#modal-create-producto" title="Crear nuevo producto">Crear Productos</a>
					</li>
				</ul>
			</div>

			<div class="widget-content">
				<div class="table-responsive">
					<table id="tableProducto" class="table table-striped mt-1">
						<thead class="text-white" style="background: #3B3F5C">
							<tr>
								<th class="table-th text-white">ID</th>
								<th class="table-th text-white ">CAT</th>
								<th class="table-th text-white ">FAMILIA</th>
								<th class="table-th text-white ">SUBFAMILIA</th>
								<th class="table-th text-white ">CODE</th>
								<th class="table-th text-white">PRECIO_M</th>
								<th class="table-th text-white">IVA</th>
								<th class="table-th text-white">ISA</th>
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
	<div class="modal fade" id="modal-create-producto" aria-hidden="true" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content bg-dark text-white">
				<fieldset id="contentDisable">
					<form action="" id="form-producto">
						<div class="modal-header bg-secondary">
							<h4 class="modal-title" style="color: white; font-weight: bold;">Productos | Admin </h4>
							<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							@include('producto.modal_create')
						</div>
						<div class="modal-footer">
							<button type="button" id="btnModalClose" class="btn btn-light" data-dismiss="modal">Cancelar</button>
							<button type="submit" id="btnAddproducto" class="btn btn-primary">Aceptar</button>
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
<script src="{{asset('rogercode/js/producto/code-index.js')}}"></script>
<script src="{{asset('rogercode/js/producto/rogercode-create-update.js')}}" type="module"></script>
@endsection
@extends('layouts.theme.app')
@section('content')
<style>
  .table-totales {
    /*border: 2px solid red;*/
  }

  .table-totales,
  th,
  td {
    border: 1px solid #DCDCDC;
  }

  .table-inventario,
  th,
  td {
    border: 1px solid #DCDCDC;
  }

  .tabs {
    list-style-type: none;
    /* Remove default list styles */
    padding: 0;
    /* Remove default padding */
    margin: 0;
    /* Remove default margin */
  }

  .tabs li {
    margin: 0 10px;
    /* Add some margin between the tabs */
  }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row sales layout-top-spacing">
	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>Compras | Productos </b>
				</h4>
				<div class="d-flex justify-content-between">
					<ul class="tabs tab-pills d-flex">
						<li class="me-auto"> <!-- Use me-auto for left alignment -->
							<a href="javascript:void(0)" onclick="showModalcreateLote()" class="tabmenu bg-dark" data-toggle="modal" data-target="#modal-create-lote" title="Crear nuevo lote">Crear Lote</a>
						</li>
						<li>
							<a href="javascript:void(0)" onclick="showModalcreate()" class="tabmenu bg-dark" data-toggle="modal" data-target="#modal-create-compensado" title="Nuevo Compra">Agregar</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="widget-content">
				<div class="table-responsive">
					<table id="tableCompensado" class="table table-striped mt-1">
						<thead class="text-white" style="background: #3B3F5C">
							<tr>
								<th class="table-th text-white">#</th>
								<th class="table-th text-white">Proveedor</th>
								<th class="table-th text-white ">BODEGA</th>
								<th class="table-th text-white">Factura</th>
								<th class="table-th text-white">VALOR</th>
								<th class="table-th text-white">FechaCP</th>
								<th class="table-th text-white">FechaIN</th>
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
			<div class="modal-content bg-default">
				<fieldset id="contentDisable">
					<form action="" id="form-compensado-res">
						<div class="modal-header">
							<h4 class="modal-title">Crear compra de productos</h4>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							@include('compensado.modal_create')
						</div>
						<div class="modal-footer">
							<button type="button" id="btnModalClose" class="btn btn-default" data-dismiss="modal">Cancelar</button>
							<button type="submit" id="btnAddCompensadoRes" class="btn btn-primary">Aceptar</button>
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
<script>
    $(document).ready(function() {
        // Limpiar mensajes de error al cerrar el modal
        $('#modal-create-lote').on('hidden.bs.modal', function() {
            $(this).find('.error-message').text(''); // Limpiar mensaje de error
            $('#loteId').val(0); // Para evitar que al crear nuevo producto se edite el registro anterior editado
        
            $('#lote').val('');
            $('#codigobarra').val('');
            $('#stockalerta').val('');
            $('#impuestoiva').val('');
            $('#isa').val('');
        });

        // Limpiar mensajes de error al seleccionar un campo
        $('#categoria').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#familia').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#nivel').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#subfamilia').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#lote').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#codigobarra').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#marca').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#stockalerta').change(function() {
            $(this).siblings('.error-message').text(''); // Clear error message on blur
        });
        $('#impuestoiva').change(function() {
            $(this).siblings('.error-message').text(''); // Clear error message on blur
        });
        $('#isa').change(function() {
            $(this).siblings('.error-message').text(''); // Clear error message on blur
        });
    });
</script>
@endsection
@section('script')
<script src="{{asset('rogercode/js/compensado/rogercode-res-index.js')}}"></script>
<script src="{{asset('rogercode/js/compensado/rogercode-create-update.js')}}" type="module"></script>
@endsection
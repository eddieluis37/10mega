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
          <b>Cargue Productos | Terminados </b>
        </h4>
        <div class="d-flex justify-content-between">
          <ul class="tabs tab-pills d-flex">
            <li class="me-auto"> <!-- Use me-auto for left alignment -->
              <a href="javascript:void(0)" onclick="showModalcreateLote()" class="tabmenu bg-dark" data-toggle="modal" data-target="#modal-create-lote" title="Crear nuevo lote">Crear Lote</a>
            </li>
            <li>
              <a href="javascript:void(0)" onclick="showModalcreateProducto(); refreshLote();" class="tabmenu bg-dark" data-toggle="modal" data-target="#modal-create-producto" title="Agregar productos">Agregar Productos</a>
            </li>
          </ul>
        </div>
      </div>
      <div class="row g-3 mt-3">
        <div class="col-md-4">
          <div class="task-header">
            <div class="form-group">
              <label for="categoria" class="form-label">Categoria</label>
              <select class="form-control form-control-sm input" name="categoria" id="categoria" required>
                <option value="">Seleccione la categoria</option>
                @foreach($category as $c)
                <option value="{{$c->id}}" {{ $c->id == 1 ? 'selected' : '' }}>{{$c->name}}</option>
                @endforeach
              </select>
              <span class="text-danger error-message"></span>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="task-header">
            <div class="form-group">
              <label for="centrocosto" class="form-label">Centro de costo</label>
              <select class="form-control form-control-sm input" name="centrocosto" id="centrocosto" required>
                <option value="">Seleccione el centro de costo</option>
                @foreach($centros as $option)
                <option value="{{$option->id}}" {{ $option->id == 1 ? 'selected' : '' }}>{{$option->name}}</option>
                @endforeach
              </select>
              <span class="text-danger error-message"></span>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="task-header">
            <div class="form-group">
              <label for="lote" class="form-label">Producto Lote</label>
              <select class="form-control form-control-sm select2Lote" name="lote" id="lote" required>
                <option value="">Seleccione el lote</option>
                @foreach($lote as $option)
                <option value="{{ $option['id'] }}" data="{{ $option }}">{{ $option['name'] }}</option>
                @endforeach
              </select>
              <span class="text-danger error-message"></span>
            </div>
          </div>
        </div>

        <!--  <div class="table-responsive mt-1">
        <form method="GET" action="/descargar-reporte">
          @csrf
         
          <!-- Botón de descarga de reporte en Excel --
          <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary">Descargar Reporte en Excel</button>
          </div>
        </form>
      </div> -->

      </div>
      <div class="table-responsive mt-3">
        <form method="POST" action="/updateCcpInventory">
          @csrf
          <table id="tableInventory" class="table table-striped mt-1">
            <thead class="text-white" style="background: #3B3F5C">
              <tr>
                <th class="table-th text-white">CAT</th>
                <th class="table-th text-white">ID</th>
                <th class="table-th text-white">PRODUCTO</th>
                <th class="table-th text-white">LOTE</th>
                <th class="table-th text-white">FECHA_VENCE</th>
                <th class="table-th text-white">CANTIDAD</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <th>Totales</th>
                <td></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </form>
      </div>
    </div>
  </div>
  <!-- modal -->
  <div class="modal fade" id="modal-create-lote" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content bg-dark text-white">
        <fieldset id="contentDisable">
          <form action="" id="form-lote">
            <div class="modal-header bg-secondary">
              <h4 class="modal-title" style="color: white; font-weight: bold;">Lote | Crear </h4>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              @include('inventory.cargue_products_terminados.modal_create')
            </div>
            <div class="modal-footer">
              <button type="button" id="btnModalClose" class="btn btn-light" data-dismiss="modal">Cancelar</button>
              <button type="submit" id="btnAddlote" class="btn btn-primary">Aceptar</button>
            </div>
          </form>
        </fieldset>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->
  <!-- modal -->
  <div class="modal fade" id="modal-create-producto" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content bg-dark text-white">
        <fieldset id="contentDisable">
          <form action="" id="form-producto">
            <div class="modal-header bg-secondary">
              <h4 class="modal-title" style="color: white; font-weight: bold;">Producto | Asociar | Lote </h4>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              @include('inventory.cargue_products_terminados.modal_create_producto')
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
<script src="{{asset('code/js/inventory/cargue_products_terminados/code-cpt-index.js')}}"></script>
<script src="{{asset('code/js/inventory/cargue_products_terminados/create-update.js')}}" type="module"></script>
@endsection
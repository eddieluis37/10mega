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
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row sales layout-top-spacing">
  <div class="col-sm-12">
    <div class="widget widget-chart-one">
      <div class="card text-center" style="background: #3B3F5C">
        <div class="m-2">
          <h4 style="color:white;"><strong>Cuentas por cobrar</strong></h3>
        </div>
      </div>
      <div class="row g-3 mt-3">
        <div class="col-sm-12 col-md-3">
          <div class="form-group">
            <label for="centrocosto" class="form-label">Cliente</label>
            <select class="form-control form-control-sm select2" name="centrocosto" id="centrocosto" required>
              <option value="">Seleccione cliente</option>
              @foreach($clientes as $option)
              <option value="{{ $option['id'] }}" data-name="{{ $option['name'] }}">{{ $option['name'] }}</option>
              @endforeach
            </select>
            <span class="text-danger error-message"></span>
          </div>
        </div>

        <div class="col-sm-12 col-md-3">
          <div class="form-group">
            <label for="categoria" class="form-label">Vendedor</label>
            <select class="form-control form-control-sm select2" name="categoria" id="categoria" required>
              <option value="">Seleccione vendedor</option>
              @foreach($vendedores as $option)
              <option value="{{ $option['id'] }}" data-name="{{ $option['name'] }}">{{ $option['name'] }}</option>
              @endforeach
            </select>
            <span class="text-danger error-message"></span>
          </div>
        </div>

        <div class="col-sm-12 col-md-3">
          <h6>Fecha y hora inicial</h6>
          <div class="form-group">
            <input type="datetime-local" class="form-control" value="{{ $dateFrom ?? date('Y-m-d') }}T00:00" name="dateFrom" id="dateFrom" required data-bs-toggle="tooltip" title="Selecciona la fecha y hora inicial desde el calendario">
          </div>
        </div>
        <div class="col-sm-12 col-md-3">
          <h6>Fecha y hora final</h6>
          <div class="form-group">
            <input type="datetime-local" class="form-control" value="{{ $dateTo ?? date('Y-m-d') }}T23:59" name="dateTo" id="dateTo" required data-bs-toggle="tooltip" title="Selecciona la fecha y hora final desde el calendario">
          </div>
        </div>

        <div class="table-responsive mt-1" style="overflow-x: auto;">
          <table id="tableInventory" class="table table-success table-striped mt-1">
            <thead class="text-white" style="background: #3B3F5C">
              <tr>
                <th class="table-th text-white" title="" style="text-align: center;">CLIENTE</th>
                <th class="table-th text-white" title="" style="text-align: center;">VENDEDOR</th>
                <th class="table-th text-white" title="" style="text-align: center;">DOMICILIARIO</th>
                <th class="table-th text-white" title="" style="text-align: center;">FACTURA</th>
                <th class="table-th text-white" title="" style="text-align: center;">FECHA_V</th>
                <th class="table-th text-white" title="" style="text-align: center;">DEUDA-INICIAL</th>
                <th class="table-th text-white" title=" " style="text-align: center;">DEUDA.X.COBRAR</th>
                
                <th class="table-th text-white" title="" style="text-align: center;">ACCIONES</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <th>Totales</th>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
  @endsection
  @section('script')  
  <script src="{{asset('code/js/cuentas_por_cobrar/index.js')}} " type="module"></script>
  @endsection


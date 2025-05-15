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
          <h4 style="color:white;"><strong>Reporte de ventas por productos</strong></h3>
        </div>
      </div>
      <div class="row g-3 mt-3">
        <div class="col-sm-12 col-md-3">
          <div class="form-group">
            <label for="centrocosto" class="form-label">Centro costo</label>
            <select class="form-control form-control-sm select2" name="centrocosto" id="centrocosto" required>
              <option value="">Seleccione la categoría</option>
              @foreach($centros as $option)
              <option value="{{ $option['id'] }}" data-name="{{ $option['name'] }}">{{ $option['name'] }}</option>
              @endforeach
            </select>
            <span class="text-danger error-message"></span>
          </div>
        </div>

        <div class="col-sm-12 col-md-3">
          <div class="form-group">
            <label for="categoria" class="form-label">Categoría</label>
            <select class="form-control form-control-sm select2" name="categoria" id="categoria" required>
              <option value="">Seleccione la categoría</option>
              @foreach($category as $option)
              <option value="{{ $option['id'] }}" data-name="{{ $option['name'] }}">{{ $option['name'] }}</option>
              @endforeach
            </select>
            <span class="text-danger error-message"></span>
          </div>
        </div>

        <div class="col-sm-12 col-md-3">
          <h6>Fecha y hora inicial</h6>
          <div class="form-group">
            <input type="datetime-local" class="form-control" value="{{ $startDate ?? date('Y-m-d') }}T00:00" name="startDate" id="startDate" required>
          </div>
        </div>
        <div class="col-sm-12 col-md-3">
          <h6>Fecha y hora final</h6>
          <div class="form-group">
            <input type="datetime-local" class="form-control" value="{{ $endDate ?? date('Y-m-d') }}T23:00" name="endDate" id="endDate" required>
          </div>
        </div>
        <div class="table-responsive mt-1" style="overflow-x: auto;">
          <table id="tableInventory" class="table table-success table-striped mt-1">
            <thead class="text-white" style="background: #3B3F5C">
              <tr>
                <th class="table-th text-white" title="product_id" style="text-align: center;">PRODUCTO</th>
                <th class="table-th text-white" title="codigo de lote_id" style="text-align: center;">LOTE</th>
                <th class="table-th text-white" title="Cantidad" style="text-align: center;">QT</th>
                <th class="table-th text-white" title="precio antes de desc e impuesto" style="text-align: center;">$_base</th>              
                <th class="table-th text-white" title="Dinero base" style="text-align: center;">T_base</th>
                <th class="table-th text-white" title="Descuento por producto" style="text-align: center;">D_pr</th>
                <th class="table-th text-white" title="Descuento por cliente" style="text-align: center;">D_cl</th>
                <th class="table-th text-white" title="iva" style="text-align: center;">T_iva</th>
                <th class="table-th text-white" title="impuesto ultra procesado" style="text-align: center;">T_up</th>
                <th class="table-th text-white" title="Impuesto al consumo" style="text-align: center;">T_ic</th>
                <th class="table-th text-white" title="Total venta" style="text-align: center;">T_venta</th>
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
                <td align="center">0.00</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
  @endsection
  @section('script')
  <script src="{{asset('code/js/reportes/code-consolidado-index.js')}} " type="module"></script>
  @endsection
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
        <div class="col-sm-6 col-md-3">
          <h6>Fecha y hora inicial</h6>
          <div class="form-group">
            <input type="datetime-local" class="form-control" value="{{ $dateFrom ?? date('Y-m-d') }}T00:00" name="dateFrom" id="dateFrom" required data-bs-toggle="tooltip" title="Selecciona la fecha y hora inicial desde el calendario">
          </div>
        </div>
        <div class="col-sm-6 col-md-3">
          <h6>Fecha y hora final</h6>
          <div class="form-group">
            <input type="datetime-local" class="form-control" value="{{ $dateTo ?? date('Y-m-d') }}T23:59" name="dateTo" id="dateTo" required data-bs-toggle="tooltip" title="Selecciona la fecha y hora final desde el calendario">
          </div>
        </div>

        <div class="col-sm-6 col-md-2 mt-3">
          <button class="btn btn-dark btn-block" onclick="exportarExcel()">
            <i class="far fa-file-excel"></i> Exportar a Excel
          </button>
        </div>

        <div class="col-sm-6 col-md-2 mt-3">
          <button onclick="window.location.reload();" class="btn btn-danger" data-bs-toggle="tooltip" title="Solo en caso que requiera">Limpiar</button>
        </div>

        @can('Cerrar_Inventario')

        @endcan

        <div class="table-responsive mt-1" style="overflow-x: auto;">
          <table id="tableInventory" class="table table-success table-striped mt-1">
            <thead class="text-white" style="background: #3B3F5C">
              <tr>
                <th class="table-th text-white" title="" style="text-align: center;">ID</th>
                <th class="table-th" title="Nombre del cliente" style="text-align: center;">CLIENTE</th>
                <th class="table-th" title="Numero consecutivo de la venta" style="text-align: center;">FACTURA</th>
                <th class="table-th text-white" title="PE PENDIENTE, PG PAGADA" style="text-align: center;">ESTADO</th>
                <th class="table-th text-white" title="Fecha vencimiento de la tabla cuentas_por_cobrars" style="text-align: center;">$FECHA_VENCIMIENTO</th>
                <th class="table-th text-white" title="campo deuda_inicial tabla cuentas_por_cobrars" style="text-align: center;">DEUDA_INICIAL</th>
                <th class="table-th text-white" title="campo valor_total tabla notacreditos" style="text-align: center;">NOTACREDITO</th>
                <th class="table-th text-white" title="campo valor_total tabla notadebitos " style="text-align: center;">$NOTADEBITO</th>
                <th class="table-th text-white" title=" " style="text-align: center;">RECIBO.CAJA</th>
                <th class="table-th text-white" title=" " style="text-align: center;">SALDO</th>
                <th class="table-th text-white" title=" " style="text-align: center;">ACCIONES</th>                
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <th>Totales</th>
                <td>                 
                </td>
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


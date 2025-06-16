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
          <h4 style="color:white;"><strong>Reporte de ajuste de inventario</strong></h3>
        </div>
      </div>
      <div class="row g-3 mt-3">
        <div class="col-sm-12 col-md-3">
          <div class="form-group">
            <label for="centrocosto" class="form-label">Bodega</label>
            <select class="form-control form-control-sm select2" name="centrocosto" id="centrocosto" required>
              <option value="">Seleccione la bodega</option>
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
                <th class="table-th text-white" title="Codigo producto" style="text-align: center;">DIA.HORA.AJUST</th>
                <th class="table-th text-white" title="Categoria de productos" style="text-align: center;">CATEGORIA</th>
                <th class="table-th text-white" title="Identificador del Producto" style="text-align: center;">ID.P</th>
                <th class="table-th text-white" title="Nombre del Producto" style="text-align: center;">PRODUCTO</th>
                <th class="table-th text-white" title="Stock Ideal antes de Ajuste" style="text-align: center;">SI</th>
                 <th class="table-th text-white" title="Bodega" style="text-align: center;">BODEGA</th>
                <th class="table-th text-white" title="Codigo lote" style="text-align: center;">LOTE</th>
                <th class="table-th text-white" title="Fecha de vencimiento del lote" style="text-align: center;">FEC_VENC</th>
                <th class="table-th text-white" title="Stock Fisica" style="text-align: center;">SF</th>
                <th class="table-th text-white" title="Cantidad diferencia" style="text-align: center;">DIF</th>
                <th class="table-th text-white" title="Costo inicial total" style="text-align: center;">COSTO</th>                
                <th class="table-th text-white" title="Costo total ajuste" style="text-align: center;">SUBTOTAL</th>  
                <th class="table-th text-white" title="Costo total ajuste" style="text-align: center;">USUARIO</th>             
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <th>Totales</th>
                <td>
                  <div class="col-sm-6 col-md-2 mt-3">
                    <a class="btn btn-dark btn-block {{(2) < 1 ? 'disabled' : '' }}" href="{{ url('report_compras_x_prod/excel' . '/' . $dateFrom. '/' . $dateTo) }}" target="_blank">
                      <i class="far fa-file-excel"></i>
                    </a>
                  </div>
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
  <script src="{{asset('code/js/reportes/ajuste-de-inventarios-index.js')}} " type="module"></script>
  @endsection

  <script>
    // Función para exportar a Excel
    function exportarExcel() {
      const dateFrom = $("#dateFrom").val();
      const dateTo = $("#dateTo").val();
      const url = `../report_compras_x_prod/excel/${dateFrom}/${dateTo}`;
      window.open(url, "_blank");
    }
  </script>
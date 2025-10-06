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
          <h4 style="color:white;"><strong>Reporte de ventas por productos & clientes</strong></h3>
        </div>
      </div>
      <div class="row g-3 mt-3">
        <div class="col-sm-12 col-md-4">
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
        <div class="col-sm-12 col-md-4">
          <div class="form-group">
            <label for="categoria" class="form-label">Vendedor</label>
            <select class="form-control form-control-sm select2" name="vendedor" id="vendedor" required>
              <option value="">Seleccione vendedor</option>
              @foreach($vendedores as $option)
              <option value="{{ $option['id'] }}" data-name="{{ $option['name'] }}">{{ $option['name'] }}</option>
              @endforeach
            </select>
            <span class="text-danger error-message"></span>
          </div>
        </div>

        <div class="col-sm-12 col-md-4">
          <div class="form-group">
            <label for="categoria" class="form-label">Domiciliario</label>
            <select class="form-control form-control-sm select2" name="domiciliario" id="domiciliario" required>
              <option value="">Seleccione domiciliario</option>
              @foreach($domiciliarios as $option)
              <option value="{{ $option['id'] }}" data-name="{{ $option['name'] }}">{{ $option['name'] }}</option>
              @endforeach
            </select>
            <span class="text-danger error-message"></span>
          </div>
        </div>

        <div class="col-sm-6 col-md-4">

        </div>

        <div class="col-sm-6 col-md-4">
          <h6>Fecha y hora inicial</h6>
          <div class="form-group">
            <input type="datetime-local" class="form-control" value="{{ $startDate ?? date('Y-m-d') }}T00:00" name="startDate" id="startDate" required>
          </div>
        </div>
        <div class="col-sm-6 col-md-4">
          <h6>Fecha y hora final</h6>
          <div class="form-group">
            <input type="datetime-local" class="form-control" value="{{ $endDate ?? date('Y-m-d') }}T23:00" name="endDate" id="endDate" required>
          </div>
        </div>


        <div class="table-responsive mt-1" style="overflow-x: auto;">
          <table id="tableInventory" class="table table-success table-striped mt-1">
            <thead class="text-black" style="background: #3B3F5C">
              <tr>
                <!-- NUEVAS PRIMERAS COLUMNAS -->
                <th class="table-th text-white" style="text-align:center;">FACTURA</th>
                <th class="table-th text-white" style="text-align:center;">DIRECCIÓN</th>
                <th class="table-th text-white" style="text-align:center;">TELÉFONO</th>
                <th class="table-th text-white" style="text-align:center;">VENDEDOR</th>
                <th class="table-th text-white" style="text-align:center;">CAJERO</th>
                <th class="table-th text-white" style="text-align:center;">DOMICILIARIO</th>

                <!-- COLUMNAS EXISTENTES (ajustadas tras el añadido) -->
                <th class="table-th text-white" title="Identificación cliente" style="text-align: center;">ID_CLIENTE</th>
                <th class="table-th" title="Nombre del cliente" style="text-align: center;">NOM_CLIENTES</th>
                <th class="table-th text-white" title="Categoria" style="text-align: center;">COD</th>
                <th class="table-th" title="Productos" style="text-align: center;">PRODUCTO</th>
                <th class="table-th text-white" title="Categoria" style="text-align: center;">CAT</th>
                <th class="table-th text-white" title="Cantidades vendidas" style="text-align: center;">CV</th>
                <th class="table-th text-white" title="Cantidades acreditadas" style="text-align: center;">CN</th>
                <th class="table-th text-white" title="Cantidad debitadas" style="text-align: center;">CD</th>
                <th class="table-th text-white" title="Cantidad real" style="text-align: center;">CR</th>
                <th class="table-th text-white" title="Dinero venta real" style="text-align: center;">$VR</th>
                <th class="table-th text-white" title="Descuento por producto" style="text-align: center;">DP</th>
                <th class="table-th text-white" title="Descuento por cliente" style="text-align: center;">DC</th>
                <th class="table-th text-white" title="" style="text-align: center;">SUBTOTAL</th>
                <th class="table-th text-white" title="" style="text-align: center;">IS</th>
                <th class="table-th text-white" title="Impuesto IVA" style="text-align: center;">IVA</th>
                <th class="table-th text-white" title="Impuesto al consumo" style="text-align: center;">IC</th>
                <th class="table-th text-white" title="" style="text-align: center;">TOTAL</th>
              </tr>
            </thead>

            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <th>Totales</th>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
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
  <script src="{{asset('code/js/reportes/code-client-index.js')}} " type="module"></script>
  @endsection
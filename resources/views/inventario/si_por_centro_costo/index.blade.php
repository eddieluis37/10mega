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
          <h4 style="color:white;"><strong>Stock ideal por CentroCosto</strong></h3>
        </div>
      </div>
      <div class="row g-3 mt-3">
        <div class="col-sm-12 col-md-3">
          <label for="inputcentro" class="form-label">Centrocosto</label>
          <select id="inputcentro" class="form-select select2">
            <option value="">Todos los centro costos</option>
            @foreach($centros as $c)
            <option value="{{$c->id}}" {{ $c->id == 1 ? 'selected' : '' }}>{{$c->name}}</option>
            @endforeach
          </select>
          <span class="text-danger error-message"></span>
        </div>
        <div class="col-sm-12 col-md-3">
          <label for="inputstore" class="form-label">Bodega</label>
          <select id="inputstore" class="form-select select2">
            <option value="">Todas las bodegas</option>
            @foreach($stores as $option)
            <option value="{{ $option['id'] }}" data="{{ $option }}">{{ $option['name'] }}</option>
            @endforeach
          </select>
          <span class="text-danger error-message"></span>
        </div>
        {{-- Lote --}}
        <div class="col-md-3">
          <label for="inputlote" class="form-label">Lote</label>
          <select id="inputlote" class="form-select select2">
            <option value="">Todos los lotes</option>
            {{-- Se llena vía AJAX --}}
          </select>
        </div>

        <div class="col-sm-12 col-md-3">
          <label for="inputcategoria" class="form-label">Categoria</label>
          <select id="inputcategoria" class="form-select select2">
            <option value="-1">Seleccione categoria</option>
            <option value="">Todas las categorias</option>
            @foreach($categorias as $option)
            <option value="{{ $option->id }}" data="{{ $option }}">{{ $option->name }}</option>
            @endforeach
          </select>
          <span class="text-danger error-message"></span>
        </div>
      </div>

      <div class="card border-0">
        <div class="m-3">

          <div class="row">
            <div class="col-3 mb-1 bg-success">
              <span>Total ingresos</span><br>
              <span>Total salidas</span><br>
              <span>Total stock ideal</span><br>
              <span>Total conteo fisico</span>
            </div>
            <div class="col-3 mb-1 bg-success">
              <div id="totalIngresos">0,00</div>
              <div id="totalSalidas">0,00</div>
              <div id="totalStock">0,00</div>
              <div id="totalConteoFisico">0,00</div>
            </div>
            <div class="col-3 mb-1 bg-primary text-center">
              <span>Diferencia en kilos</span><br>
              <div id="diferenciaKilos">0,00</div>
            </div>
            <div class="col-3 mb-1 bg-warning text-center">
              <span>Dif. En kilos permitida</span><br>
              <div id="difKilosPermitidos">0,00</div>
            </div>
            <div class="col-3 mb-1 bg-info text-center">
              <span>% Merma</span><br>
              <div id="porcMerma">0,00</div>
            </div>
            <div class="col-3 mb-1 bg-warning text-center">
              <span>% Merma permitida</span><br>
              <div id="porcMermaPermitida">0,00</div>
            </div>
            <div class="col-3 mb-1 bg-info text-center">
              <span>Dif en kilos</span><br>
              <div id="difKilos">0,00</div>
            </div>
            <div class="col-3 mb-1 bg-success text-center">
              <span>Dif. en %</span><br>
              <div id="difPorcentajeMerma">0,00</div>
            </div>
          </div>

        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-sm table-inventario">
          <thead class="text-white" style="background: #3B3F5C">
            <tr>
              <td colspan="5" class="text-center" style="background: green">Ingresos</td>
              <td colspan="2" class="text-center" style="background: red">Salidas</td>
              <td colspan="4" class="text-center" style="background: orange">Inventario</td>
            </tr>
            <tr>
              <td style="background: green">
                InvIni
                <div id="totalInvInicial">0,00</div>
              </td>
              <td style="background: green">
                ComLot
                <div id="totalCompraLote">0,00</div>
              </td>
              <td style="background: green">
                Alist
                <div id="totalAlistamiento">0,00</div>
              </td>
              <td style="background: green">
                Compen
                <div id="totalCompensados">0,00</div>
              </td>
              <td style="background: green">
                TrasIn
                <div id="totalTrasladoing">0,00</div>
              </td>
              <td style="background: red">
                TVR
                <div id="totalVenta">0,00</div>
              </td>
              <td style="background: red">
                TotTrS
                <div id="totalTrasladoSal">0,00</div>
              </td>
              <td style="background: orange">
                <!--  StocIde
                <div id="StocIde">0,00</div> -->
              </td>
              <td style="background: orange">
                <!--  ContFis
                <div id="contFis">0,00</div>
              </td> -->
              <td style="background: orange">
                <!-- DifeKg
                <div id="totalInvInicial">0,00</div> -->
              </td>
              <td style="background: orange">
                <!-- Decomi
                <div id="decomisos">0,00</div> -->
              </td>
            </tr>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
            <tr>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="table-responsive mt-3">
        <table id="tableInventory" class="table table-success table-striped mt-1">
          <thead class="text-white" style="background: #3B3F5C">
            <tr>
              <th class="table-th text-white" title="Nombre de la bodega">Bodega</th>
              <th class="table-th text-white" title="Lote">Lote</th>
              <th class="table-th text-white" title="Fecha de vencimiento lote">FVENC</th>
              <th class="table-th text-white" title="Categoria">CAT</th>
              <th class="table-th text-white" title="Categoria">CODE</th>
              <th class="table-th text-white" title="Productos">PRODUCTO</th>
              <th class="table-th text-white" title="Stock Ideal">SI</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
            <tr>
              <th>Total</th>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
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
<script src="{{asset('code/js/inventario/si-por-centro-costo-index.js')}} " type="module"></script>
@endsection
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <title>Reporte Validación del Cierre de Caja</title>
  <style>
    /* Estilos Generales */
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background: #f4f7f8;
      color: #333;
    }

    .container {
      width: 95%;
      max-width: 1200px;
      margin: 2rem auto;
      background: #fff;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      overflow: hidden;
    }

    /* Encabezado Principal */
    .header {
      background: linear-gradient(to right, #007bff, #00c8ff);
      color: #fff;
      /*   padding: 1.5rem; */
      text-align: center;
    }

    .header h1 {
      margin: 0;
      font-size: 1.5rem;
      letter-spacing: 1px;
    }

    /* Sección de Información General */
    .info-section {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      padding: 1rem;
      background: #fafafa;
      border-bottom: 1px solid #ddd;
    }

    .info-item {
      flex: 1 1 200px;
      margin: 0.5rem 0;
      padding: 0.5rem;
      font-size: 0.95rem;
    }

    .info-item strong {
      color: #007bff;
    }

    /* Tabla */
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.75rem;
    }

    thead {
      background: #007bff;
      color: #fff;
    }

    thead th {
      padding: 0.75rem;
      text-align: left;
      border-right: 1px solid #0060c0;
    }

    thead th:last-child {
      border-right: none;
    }

    tbody tr {
      border-bottom: 1px solid #ddd;
    }

    tbody tr:nth-child(even) {
      background: #f9f9f9;
    }

    tbody td {
      padding: 0.75rem;
      border-right: 1px solid #eee;
    }

    tbody td:last-child {
      border-right: none;
    }

    /* Fila de Totales */
    tfoot tr {
      background: #ffeb99;
      font-weight: bold;
    }

    tfoot td {
      padding: 0.75rem;
      border-right: 1px solid #eee;
    }

    tfoot td:last-child {
      border-right: none;
    }
  </style>

  <!-- styles y contenedores para la tabla con barra espejo -->
<style>
  /* Contenedor principal */
  .table-container {
    width: 100%;
    overflow: hidden;
    position: relative;
    padding-bottom: 8px; /* espacio para la barra espejo */
  }

  /* Contenedor que hace scroll real para la tabla */
  .table-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    background: #fff;
    border-radius: 6px;
  }

  /* Tu tabla - puedes mantener tus clases existentes */
  table.mi-tabla {
    width: 100%;
    min-width: 900px; /* ajustar según tus columnas; si no usar 100% */
    border-collapse: collapse;
  }

  table.mi-tabla th,
  table.mi-tabla td {
    padding: 12px 10px;
    border: 1px solid #eee;
    text-align: left;
    white-space: nowrap; /* evita quiebre de texto en columnas */
  }

  /* Pie: barra espejo */
  .mirror-scroll {
    height: 14px;
    overflow-x: auto;
    overflow-y: hidden;
    margin-top: 8px;
    display: none; /* visible sólo si la tabla necesita scroll */
  }
  .mirror-inner {
    height: 1px;
    background: transparent;
  }

  /* Estilo de scrollbar (WebKit) */
  .mirror-scroll::-webkit-scrollbar { height: 10px; }
  .mirror-scroll::-webkit-scrollbar-track { background: #f3f3f3; border-radius: 8px; }
  .mirror-scroll::-webkit-scrollbar-thumb { background: #cfcfcf; border-radius: 8px; }
  .mirror-scroll::-webkit-scrollbar-thumb:hover { background: #b3b3b3; }

  /* Firefox */
  .mirror-scroll { scrollbar-width: thin; scrollbar-color: #cfcfcf #f3f3f3; }
</style>


</head>

<body>
  <div class="container">
    <!-- Encabezado Principal -->
    <div class="header">
      <h1>REPORTE PARA VALIDACIÓN DEL CIERRE DE CAJA</h1>
    </div>

    <!-- Información General de la Caja -->
    <div class="info-section">
      <div class="info-item"><strong>Centro Costo:</strong> {{ $caja->namecentrocosto }}</div>
      <div class="info-item"><strong>Cajero:</strong> {{ $caja->namecajero }}</div>
      <div class="info-item"><strong>Cantidad facturas:</strong> {{ $caja->cantidad_facturas }}</div>
      <div class="info-item">
        <strong>Fecha:</strong>
        {{ $caja->fecha_hora_inicio ? \Carbon\Carbon::parse($caja->fecha_hora_inicio)->format('d/m/Y H:i') : 'N/A' }}
      </div>
      <div class="info-item"><strong>Turno:</strong> {{ $caja->id }}</div>
      <div class="info-item"><strong>Diferencia:</strong> ${{ number_format($caja->diferencia, 0) }}</div>
    </div>
    <!-- Tabla de Detalle de Facturas -->
   <div class="table-container">
  <!-- area con scroll real donde está la tabla -->
  <div class="table-scroll" id="tableScroll">
    <table class="mi-tabla" id="myTable">
      <thead>
        <tr>
          <th>CLIENTE</th>
          <th>#FACTURA</th>
          <th>TOTAL FACTURA</th>
          <th>EFECTIVO</th>

          {{-- Encabezados de TARJETA: solo tarjetas activas y solo posiciones con valores --}}
          @if(!empty($activeTarjetas) && count($activeTarjetas))
            @foreach($activeTarjetas as $tarjeta)
              @foreach($tarjetaColumns[$tarjeta->id] ?? [] as $pos)
                @php
                  $label = $tarjeta->nombre;
                  if($pos === 'tarjeta2') $label = $tarjeta->nombre . ' 2';
                  if($pos === 'tarjeta3') $label = $tarjeta->nombre . ' 3';
                @endphp
                <th>{{ $label }}</th>
              @endforeach
            @endforeach
          @endif

          {{-- Crédito --}}
          @if($showCredito)
            <th>CREDITO</th>
          @endif

          {{-- Encabezados dinámicos de DEV + siglas --}}
          @foreach($creditForms as $fp)
            <th>DEV {{ strtoupper($fp->nombre) }}</th>
          @endforeach
        </tr>
      </thead>

      <tbody>
        @foreach($caja->sales as $sale)
        <tr>
          <td>{{ $sale->tercero->name ?? 'Sin Nombre' }}</td>
          <td>{{ $sale->consecutivo }}</td>
          <td>${{ number_format($sale->total_valor_a_pagar,0,',','.') }}</td>
          <td>${{ number_format(($sale->valor_a_pagar_efectivo ?? 0) - ($sale->cambio ?? 0),0,',','.') }}</td>

          {{-- Valores de TARJETA: iterar mismas tarjetas/posiciones que el encabezado --}}
          @if(!empty($activeTarjetas) && count($activeTarjetas))
            @foreach($activeTarjetas as $tarjeta)
              @foreach($tarjetaColumns[$tarjeta->id] ?? [] as $pos)
                <td>
                  @if($pos === 'tarjeta1')
                    @if(optional($sale->formaPagoTarjeta)->id === $tarjeta->id)
                      ${{ number_format($sale->valor_a_pagar_tarjeta ?? 0,0,',','.') }}
                    @else
                      $0
                    @endif
                  @elseif($pos === 'tarjeta2')
                    @if(optional($sale->formaPagoTarjeta2)->id === $tarjeta->id)
                      ${{ number_format($sale->valor_a_pagar_tarjeta2 ?? 0,0,',','.') }}
                    @else
                      $0
                    @endif
                  @elseif($pos === 'tarjeta3')
                    @if(optional($sale->formaPagoTarjeta3)->id === $tarjeta->id)
                      ${{ number_format($sale->valor_a_pagar_tarjeta3 ?? 0,0,',','.') }}
                    @else
                      $0
                    @endif
                  @else
                    $0
                  @endif
                </td>
              @endforeach
            @endforeach
          @endif

          {{-- Valor de CRÉDITO --}}
          @if($showCredito)
            <td>
              @if($sale->formaPagoCredito)
                ${{ number_format($sale->valor_a_pagar_credito ?? 0,0,',','.') }}
              @else
                $0
              @endif
            </td>
          @endif

          {{-- Valor de DEV (nota de crédito) --}}
          @foreach($creditForms as $fp)
            <td>
              @if($sale->notacredito && $sale->notacredito->formaPago->id === $fp->id)
                ${{ number_format($sale->notacredito->total ?? 0,0,',','.') }}
              @else
                $0
              @endif
            </td>
          @endforeach
        </tr>
        @endforeach
      </tbody>

      <tfoot>
        <tr>
          <td colspan="2"><strong>TOTALES</strong></td>
          <td>${{ number_format($totalFactura ?? 0,0,',','.') }}</td>
          <td>${{ number_format($totalEfectivo ?? 0,0,',','.') }}</td>

          {{-- Totales TARJETA por posiciones activas --}}
          @if(!empty($activeTarjetas) && count($activeTarjetas))
            @foreach($activeTarjetas as $tarjeta)
              @foreach($tarjetaColumns[$tarjeta->id] ?? [] as $pos)
                <td>
                  @php
                    $val = $totalesTarjeta[$tarjeta->id][$pos] ?? 0;
                  @endphp
                  ${{ number_format($val,0,',','.') }}
                </td>
              @endforeach
            @endforeach
          @endif

          {{-- Total CRÉDITO --}}
          @if($showCredito)
            <td>${{ number_format($totalCredito ?? 0,0,',','.') }}</td>
          @endif

          {{-- Totales DEV por forma de pago --}}
          @foreach($creditForms as $fp)
            <td>${{ number_format($totalesDevolucion[$fp->id] ?? 0,0,',','.') }}</td>
          @endforeach
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- Barra espejo sincronizada -->
  <div class="mirror-scroll" id="mirrorScroll" aria-hidden="true">
    <div class="mirror-inner" id="mirrorInner"></div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tableScroll = document.getElementById('tableScroll');
    const mirrorScroll = document.getElementById('mirrorScroll');
    const mirrorInner = document.getElementById('mirrorInner');
    const table = document.getElementById('myTable');

    if (!table || !tableScroll || !mirrorScroll || !mirrorInner) return;

    function updateMirrorWidth() {
      // ancho total de la tabla incluidas las columnas que se desbordan
      const totalWidth = table.scrollWidth;
      mirrorInner.style.width = totalWidth + 'px';

      // mostrar u ocultar la barra espejo según si hay overflow
      if (tableScroll.scrollWidth > tableScroll.clientWidth) {
        mirrorScroll.style.display = 'block';
      } else {
        mirrorScroll.style.display = 'none';
      }
    }

    // sincronización bidireccional sin bucles
    let syncingFrom = null;

    tableScroll.addEventListener('scroll', function () {
      if (syncingFrom === 'mirror') return;
      syncingFrom = 'table';
      mirrorScroll.scrollLeft = tableScroll.scrollLeft;
      window.requestAnimationFrame(() => syncingFrom = null);
    });

    mirrorScroll.addEventListener('scroll', function () {
      if (syncingFrom === 'table') return;
      syncingFrom = 'mirror';
      tableScroll.scrollLeft = mirrorScroll.scrollLeft;
      window.requestAnimationFrame(() => syncingFrom = null);
    });

    // actualizar al cargar y al redimensionar ventana
    updateMirrorWidth();
    window.addEventListener('resize', updateMirrorWidth);

    // Si tu tabla se actualiza dinámicamente (AJAX / Livewire), llama a updateMirrorWidth() después de cambiar filas/columnas.
    // Ejemplo con Livewire: Livewire.on('tablaActualizada', () => updateMirrorWidth());
  });
</script>
  </div>
</body>

</html>
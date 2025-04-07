@extends('layouts.theme.app')
@section('content')


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
      /*   background: linear-gradient(to right, #007bff, #00c8ff);
      color: #fff; 
      padding: 1.5rem;*/
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
      font-size: 0.95rem;
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
    <table>
      <thead>
        <tr>
          <th>CLIENTE</th>
          <th>#FACTURA</th>
          <th>TOTAL FACTURA</th>
          <th>EFECTIVO</th>

          <!-- Columnas dinámicas para cada forma de pago TARJETA -->
          @foreach($tarjetas as $tarjeta)
          <th>{{ $tarjeta->nombre }}</th>
          @endforeach

          <!-- Columna única para CRÉDITO -->
          <th>CREDITO</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($caja->sales as $sale)
        <tr>
          <td>{{ $sale->tercero->name ?? 'Sin Nombre' }}</td>
          <td>{{ $sale->consecutivo }}</td>
          <td>$ {{ number_format($sale->total_valor_a_pagar, 0, ',', '.') }}</td>
          <td>$ {{ number_format($sale->valor_a_pagar_efectivo, 0, ',', '.') }}</td>

          <!-- Para cada tarjeta, validamos si la venta coincide con esa forma de pago -->
          @foreach($tarjetas as $tarjeta)
          <td>
            @if(optional($sale->formaPagoTarjeta)->id === $tarjeta->id)
            ${{ number_format($sale->valor_a_pagar_tarjeta, 0, ',', '.') }}
            @else
            $0
            @endif
          </td>
          @endforeach

          <!-- Para la columna de CREDITO (consolidado) -->
          <td>
            {{-- Si la venta tiene una forma de pago crédito, mostramos valor_a_pagar_credito; de lo contrario 0 --}}
            @if($sale->formaPagoCredito)
            ${{ number_format($sale->valor_a_pagar_credito, 0) }}
            @else
            $0
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>

      <tfoot>
        @php
        // Totales
        $totalFactura = $caja->sales->sum('total_valor_a_pagar');
        $totalEfectivo = $caja->sales->sum('valor_a_pagar_efectivo');

        // Calculamos totales de cada tarjeta
        $totalesTarjeta = [];
        foreach($tarjetas as $tar) {
        $totalesTarjeta[$tar->id] = 0;
        }

        // Total para créditos
        $totalCredito = 0;

        // Recorremos las ventas para sumar
        foreach($caja->sales as $venta) {
        // Sumar a la tarjeta correspondiente
        if($venta->formaPagoTarjeta && array_key_exists($venta->formaPagoTarjeta->id, $totalesTarjeta)) {
        $totalesTarjeta[$venta->formaPagoTarjeta->id] += $venta->valor_a_pagar_tarjeta;
        }
        // Sumar a crédito
        if($venta->formaPagoCredito) {
        $totalCredito += $venta->valor_a_pagar_credito;
        }
        }
        @endphp
        <tr>
          <td colspan="2">TOTALES</td>
          <td>${{ number_format($totalFactura, 0, ',', '.') }}</td>
          <td>${{ number_format($totalEfectivo, 0, ',', '.') }}</td>

          <!-- Mostramos la sumatoria para cada tarjeta en el mismo orden -->
          @foreach($tarjetas as $tarjeta)
          <td>${{ number_format($totalesTarjeta[$tarjeta->id], 0, ',', '.') }}</td>
          @endforeach

          <!-- Total de crédito -->
          <td>${{ number_format($totalCredito, 0, ',', '.') }}</td>
        </tr>
      </tfoot>
    </table>
  </div>
</body>

</html>
@endsection
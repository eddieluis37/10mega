<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Validación del Cierre de Caja</title>
    <style>
        /* Estilos básicos para el reporte */
        .container { width: 90%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .info-section { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; }
        .info-item { flex: 1 1 200px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 8px; text-align: center; }
        tfoot td { font-weight: bold; }
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
        <div class="info-item"><strong>Fecha:</strong> {{ $caja->fecha_hora_inicio ? $caja->fecha_hora_inicio->format('d/m/Y H:i') : 'N/A' }}</div>
        <div class="info-item"><strong>Turno:</strong> {{ $caja->id }}</div>
        <div class="info-item"><strong>Diferencia:</strong> {{ number_format($caja->diferencia, 2) }}</div>
    </div>

    <!-- Tabla de Detalle de Facturas -->
    <table>
        <thead>
            <tr>
                <th>NOMBRE CLIENTE</th>
                <th>#FACTURA</th>
                <th>TOTAL FACTURA</th>
                <th>EFECTIVO</th>
                <th>OTROS MEDIOS</th>
                <!-- Puedes agregar más columnas si necesitas diferenciar métodos de pago -->
            </tr>
        </thead>
        <tbody>
            @foreach ($caja->sales as $sale)
            <tr>
                <td>
                    {{-- Suponiendo que la venta tiene relación con un cliente (tercero) --}}
                    {{ $sale->tercero->name ?? 'Sin Nombre' }}
                </td>
                <td>{{ $sale->consecutivo }}</td>
                <td>${{ number_format($sale->total_valor_a_pagar, 2) }}</td>
                <td>${{ number_format($sale->valor_a_pagar_efectivo, 2) }}</td>
                <td>
                    {{-- Aquí podrías sumar otros métodos de pago o mostrarlos individualmente --}}
                    ${{ number_format($sale->valor_a_pagar_tarjeta + $sale->valor_a_pagar_otros + $sale->valor_a_pagar_credito, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            @php
                $totalFactura = $caja->sales->sum('total_valor_a_pagar');
                $totalEfectivo = $caja->sales->sum('valor_a_pagar_efectivo');
                $totalOtros = $caja->sales->sum(function($sale) {
                    return $sale->valor_a_pagar_tarjeta + $sale->valor_a_pagar_otros + $sale->valor_a_pagar_credito;
                });
            @endphp
            <tr>
                <td colspan="2">TOTALES</td>
                <td>${{ number_format($totalFactura, 2) }}</td>
                <td>${{ number_format($totalEfectivo, 2) }}</td>
                <td>${{ number_format($totalOtros, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Resumen Cierre Diario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 4px;
        }

        th {
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .small {
            font-size: 10px;
            color: #666;
        }

        .border-top {
            border-top: 1px solid #000;
        }

        .border-strong {
            border-top: 2px solid #000;
        }

        .spacer {
            height: 8px;
        }
    </style>
</head>

<body>

    <h2 style="text-align:center; margin-bottom:4px;">
        RESUMEN CIERRE DIARIO<br>
        {{ strtoupper($fechaCierre) }}
    </h2>

    <table>

        {{-- 1. Totales por forma de pago --}}
        <tr class="spacer">
            <td colspan="3"></td>
        </tr>
        <tr>
            <th>Efectivo</th>
            <td class="right">${{ number_format($valorEfectivo,0,',','.') }}</td>
        </tr>
        <tr>
            <th>QR</th>
            <td class="right">${{ number_format($sumQR,0,',','.') }}</td>
        </tr>
        <tr>
            <th>Crédito</th>
            <td class="right">${{ number_format($sumCredito,0,',','.') }}</td>
        </tr>
        <tr class="border-top">
            <th>Total Venta</th>
            <td class="right bold">${{ number_format($totalVenta,0,',','.') }}</td>
           <!--  <td class="small">= EFECTIVO + QR + CRÉDITO</td> -->
        </tr>

        {{-- Clientes a Crédito --}}
        <tr>
            <td colspan="3" style="height:8px;"></td>
        </tr>
        <tr>
            <th colspan="2">Clientes a Crédito</th>
        </tr>

        @forelse($creditos as $c)
        <tr>
            <td>{{ $c['cliente'] }}</td>
            <td class="right">${{ number_format($c['monto'], 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="2" style="text-align:center; color:#666;">
                No hay ventas a crédito en este turno
            </td>
        </tr>
        @endforelse

        <tr class="border-top">
            <th>TOTAL CRÉDITOS</th>
            <td class="right bold">${{ number_format($totalCreditos, 0, ',', '.') }}</td>
         <!--    <td class="small">= SUMA Créditos</td> -->
        </tr>


        {{-- 3. Resumen de caja --}}
        <tr class="spacer">
            <td colspan="3"></td>
        </tr>
        <tr>
            <th>Base Inicial</th>
            <td class="right">${{ number_format($caja->base,0,',','.') }}</td>
        </tr>
        <tr>
            <th>Efectivo Real</th>
            <td class="right">${{ number_format($valorEfectivo,0,',','.') }}</td>
        </tr>
        <tr>
            <th>Total Efectivo en Caja</th>
            <td class="right">${{ number_format($totalEfectivoCaja,0,',','.') }}</td>
           <!--  <td class="small">= BASE + EFECTIVO</td> -->
        </tr>

        {{-- Bloque: Recibos de Caja --}}
        <tr>
            <td colspan="3" style="height:8px;"></td>
        </tr>
        <tr>
            <th colspan="2">Resumen Recibos de Caja</th>
        </tr>

        @forelse($recibos as $r)
        <tr>
            <td>{{ $r->third?->name ?? $r->user->name }}</td>
            <td class="right">
                $ {{ number_format($r->vr_total_pago, 0, ',', '.') }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="2" style="text-align:center; color:#666;">
                No hay recibos de caja en este turno
            </td>
        </tr>
        @endforelse

        {{-- --- Pagos recibidos POR FORMA DE PAGO --- --}}
        <tr>
            <td colspan="3" style="height:8px;"></td>
        </tr>
        <tr>
            <th colspan="2">Pagos recibidos por forma</th>
        </tr>

        @forelse($pagosPorForma as $p)
        <tr>
            <td>{{ $p['forma'] }}</td>
            <td class="right">
                $ {{ number_format($p['total'], 0, ',', '.') }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="2" style="text-align:center; color:#666;">
                No hay pagos registrados
            </td>
        </tr>
        @endforelse

        {{-- Luego tu fila de TOTAL RECIBOS --}}
        <tr class="border-top">
            <th>TOTAL RECAUDOS RECIBIDOS</th>
            <td class="right bold">
                $ {{ number_format($totalRecibos, 0, ',', '.') }}
            </td>
           <!--  <td class="small">= SUMA Pagos Recibidos</td> -->
        </tr>


        {{-- 4. Salidas de dinero --}}
        <tr class="spacer">
            <td colspan="3"></td>
        </tr>
        <tr>
            <th colspan="2">Salida de Dinero</th>
        </tr>
        @foreach($salidas as $g)
        <tr>
            <td>{{ $g->concepto }}</td>
            <td class="right">${{ number_format($g->vr_efectivo,0,',','.') }}</td>
        </tr>
        @endforeach
        <tr class="border-top">
            <th>TOTAL GASTOS</th>
            <td class="right bold">${{ number_format($totalGastos,0,',','.') }}</td>
            <!-- <td class="small">= SUMA Gastos</td> -->
        </tr>

        <tr class="border-top">
            <th>Resumen Final</th>
        </tr>

        <tr>
            <th>Total Efectivo</th>
            <td class="right">${{ number_format($valorEfectivo,0,',','.') }}</td>
        </tr>
        <tr>
            <th>Total Recaudo Efectivo</th>
            <td class="right bold">
                $ {{ number_format($totalRecaudoPagoEfectivo, 0, ',', '.') }}
            </td>
        </tr>
         <tr>
            <th>Total Salida Dinero</th>
            <td class="right bold">
                $ {{ number_format($totalGastos, 0, ',', '.') }}
            </td>
        </tr>

        {{-- 5. Balance final --}}
        <tr class="spacer">
            <td colspan="3"></td>
        </tr>
        <tr>
            <th>EFECTIVO A ENTREGAR</th>
            <td class="right bold">${{ number_format($efectivoAEntregar,0,',','.') }}</td>
           <!--  <td class="small">= TOTAL EN CAJA – GASTOS</td> -->
        </tr>
        <tr>
            <th>TOTAL PAGOS CON CODIGO QR</th>
            <td class="right">${{ number_format($totalPagosConQR,0,',','.') }}</td>           
        </tr>
        <tr>
            <th>TOTAL CREDITOS DEL DIA</th>
            <td class="right">${{ number_format($sumCredito,0,',','.') }}</td>          
        </tr>

    </table>
</body>

</html>
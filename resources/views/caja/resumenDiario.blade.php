<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="application/pdf">
    <title>Resumen Cierre Diario</title>

    <link rel="stylesheet" href="{{ public_path('css/pos_custom_pdf.css') }}">
    <link rel="stylesheet" href="{{ public_path('css/pos_custom_page.css') }}">

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
    <section style="top: 0px;">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td class="text-center">
                    <span style="font-size: 17px; font-weight: bold; display: block; margin: 0;">RESUMEN CIERRE DE CAJA</span>
                    <img src="{{ public_path('assets/img/logo/logo-mega.jpg') }}" alt="" width="33%" style="padding-top: -70px; position: relative">
                    <span style="font-size: 17px; font-weight: bold; display: block; margin: 0;">{{ $caja->centroCosto->name ?? '' }}</span>
                    <span style="font-size: 13px; font-weight: bold; display: block; margin: 0;">Caja: {{ $caja->cajero->name ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td><span style="font-size: 13px; font-weight: bold; display: block; margin-top: 10;">Turno: {{ $caja->id }}</span></td>
            </tr>
            <tr>
                <td width="100%" class="text-left text-company" style="vertical-align: top; padding-top: 7px">
                    <span style="font-size: 11px; font-weight: bold; display: block; margin: 2;">Fecha y hora:<strong> {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</strong></span>
                    <span style="font-size: 11px; font-weight: bold; display: block; margin: 2;"> Actualización: <strong>{{ \Carbon\Carbon::parse($caja->updated_at)->format('Y-m-d H:i') }}</strong></span>
                </td>
            </tr>
        </table>
    </section>

    <h3 style="text-align:center; margin-bottom:4px;">
        {{ strtoupper($fechaCierre) }}
    </h3>

    <table>
        {{-- 1. Totales por forma de pago --}}
        <tr class="spacer">
            <td colspan="3"></td>
        </tr>

        <tr>
            <th>Efectivo</th>
            <td class="right">{{ number_format($arrayTotales['valorEfectivo'] ?? 0,0,',','.') }}</td>
        </tr>
        <tr>
            <th>Medios Electronicos</th>
            <td class="right">{{ number_format($arrayTotales['valorApagarTarjeta'] ?? 0,0,',','.') }}</td>
        </tr>
        <tr>
            <th>Crédito</th>
            <td class="right">{{ number_format($arrayTotales['valorApagarCredito'] ?? 0,0,',','.') }}</td>
        </tr>
        <tr class="border-top">
            <th>Total Venta</th>
            <td class="right bold">{{ number_format($arrayTotales['valorTotal'] ?? 0,0,',','.') }}</td>
        </tr>

        {{-- Clientes a Crédito (si corresponde) --}}
        <tr>
            <td colspan="3" style="height:8px;"></td>
        </tr>
        <tr>
            <th colspan="2">Clientes a Crédito</th>
        </tr>

        @php
        // Si necesitas listar clientes a crédito, puedes pasarlos desde controller; aquí mostramos total créditos
        @endphp
        <tr>
            <td>Total Créditos del Día</td>
            <td class="right">{{ number_format($arrayTotales['valorApagarCredito'] ?? 0,0,',','.') }}</td>
        </tr>

        <tr class="border-top">
            <th>RESUMEN: Base y Efectivo</th>
        </tr>

        <tr>
            <th>Base Inicial</th>
            <td class="right">{{ number_format($caja->base ?? 0,0,',','.') }}</td>
        </tr>
        <tr>
            <th>Efectivo Real (ajustado)</th>
            <td class="right">{{ number_format($arrayTotales['valorEfectivo'] ?? 0,0,',','.') }}</td>
        </tr>
        <tr>
            <th>Total Efectivo en Caja</th>
            <td class="right">{{ number_format(($caja->base ?? 0) + ($arrayTotales['valorEfectivo'] ?? 0),0,',','.') }}</td>
        </tr>

        {{-- Recibos de Caja --}}
        <tr>
            <td colspan="3" style="height:8px;"></td>
        </tr>
        <tr>
            <th colspan="2">Resumen Recibos de Caja</th>
        </tr>

        @forelse($recibos as $r)
        <tr>
            <td>{{ $r->third?->name ?? $r->user->name }}</td>
            <td class="right">{{ number_format($r->vr_total_pago ?? 0, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="2" style="text-align:center; color:#666;">No hay recibos de caja en este turno</td>
        </tr>
        @endforelse

        {{-- Pagos recibidos por forma --}}
        <tr>
            <td colspan="3" style="height:8px;"></td>
        </tr>
        <tr>
            <th colspan="2">Pagos recibidos por forma</th>
        </tr>

        @forelse($pagosPorForma as $p)
        <tr>
            <td>{{ $p['forma'] }}</td>
            <td class="right">{{ number_format($p['total'], 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="2" style="text-align:center; color:#666;">No hay pagos registrados</td>
        </tr>
        @endforelse

        <tr class="border-top">
            <th>TOTAL RECAUDOS RECIBIDOS</th>
            <td class="right bold">{{ number_format($totalRecibos ?? 0, 0, ',', '.') }}</td>
        </tr>

        {{-- Notas de crédito (Devoluciones) por forma --}}
        <tr>
            <td colspan="3" style="height:8px;"></td>
        </tr>
        <tr>
            <th colspan="2">Notas de Crédito - Devoluciones (por forma)</th>
        </tr>
        @if(!empty($arrayTotales['totalesDevolucion_porForma_nombre']))
        @foreach($arrayTotales['totalesDevolucion_porForma_nombre'] as $fp)
        <tr>
            <td>{{ $fp['nombre'] }}</td>
            <td class="right">{{ number_format($fp['total'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr class="border-top">
            <th>TOTAL NOTAS CRÉDITO</th>
            <td class="right bold">
                {{ number_format(collect($arrayTotales['totalesDevolucion_porForma'])->sum() ?? 0, 0, ',', '.') }}
            </td>
        </tr>
        @else
        <tr>
            <td colspan="2" style="text-align:center; color:#666;">
                No hay notas de crédito registradas en este turno
            </td>
        </tr>
        @endif
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
            <td class="right">{{ number_format($g->vr_efectivo ?? 0,0,',','.') }}</td>
        </tr>
        @endforeach
        <tr class="border-top">
            <th>TOTAL GASTOS</th>
            <td class="right bold">{{ number_format($totalGastos ?? 0,0,',','.') }}</td>
        </tr>

        {{-- Resumen final y balance --}}
        <tr class="border-top">
            <th>Resumen Final</th>
        </tr>

        <tr>
            <th>Total Efectivo</th>
            <td class="right">{{ number_format($arrayTotales['valorEfectivo'] ?? 0,0,',','.') }}</td>
        </tr>
        <tr>
            <th>Total Recaudo Efectivo (recibos)</th>
            <td class="right bold">{{ number_format($totalRecaudoPagoEfectivo ?? 0,0,',','.') }}</td>
        </tr>
        <tr>
            <th>Total Salida Dinero</th>
            <td class="right bold">{{ number_format($totalGastos ?? 0,0,',','.') }}</td>
        </tr>

        <tr class="spacer">
            <td colspan="3"></td>
        </tr>
        <tr>
            <th>EFECTIVO A ENTREGAR</th>
            <td class="right bold">
                {{ number_format(  ($arrayTotales['valorEfectivo'] ?? 0) - ($totalGastos ?? 0), 0, ',', '.') }}
            </td>
        </tr>

        <tr>
            <th>TOTAL PAGOS ELECTRONICOS</th>
            <td class="right">{{ number_format(($arrayTotales['valorApagarTarjeta'] ?? 0),0,',','.') }}</td>
        </tr>

        <tr>
            <th>TOTAL CREDITOS DEL DIA</th>
            <td class="right">{{ number_format($arrayTotales['valorApagarCredito'] ?? 0,0,',','.') }}</td>
        </tr>

    </table>
</body>

</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salida de Efectivo #{{ $salida->id }}</title>
    <link rel="stylesheet" href="{{ public_path('css/pos_custom_pdf.css') }}">
    <link rel="stylesheet" href="{{ public_path('css/pos_custom_page.css') }}">
</head>
<body>
    <section>
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td class="text-center">
                    <span style="font-size:18px;font-weight:bold;">SALIDA DE EFECTIVO</span><br>
                    <img src="{{ public_path('assets/img/logo/logo-mega.jpg') }}" alt="Logo" width="30%">
                </td>
            </tr>
            <tr>
                <td style="padding-top:10px;">
                    <strong>Turno Caja N°:</strong> {{ optional($salida->caja)->id ?? 'N/A' }}<br>
                    <strong>Cajero:</strong> {{ optional($salida->caja)->namecajero ?? 'N/A' }}<br>
                    <strong>Centro de Costo:</strong> {{ optional($salida->caja)->namecentrocosto ?? 'N/A' }}<br>
                    <strong>Salida N°:</strong> {{ $salida->id }}<br>
                    <strong>Tercero:</strong> {{ optional($salida->tercero)->name ?? 'N/A' }}<br>
                    <strong>Identificación:</strong> {{ optional($salida->tercero)->identification ?? 'N/A' }}<br>
                    <strong>Fecha y hora salida:</strong> {{ $fechaSalida }}<br>
                    <strong>Registrado:</strong> {{ $fechaRegistro }}<br>
                    <strong>Estado:</strong> {{ $salida->status == 1 ? 'Activo' : 'Pendiente' }}<br>
                </td>
            </tr>
        </table>
    </section>
    <hr>
    <section>
        <table width="100%">
            <tr>
                <th style="text-align:left; width: 40%;">Valor Efectivo:</th>
                <td style="text-align:right; width: 60%;">$ {{ number_format($salida->vr_efectivo, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th style="text-align:left; vertical-align: top;">Concepto:</th>
                <td style="text-align:right;">{{ $salida->concepto }}</td>
            </tr>
        </table>
    </section>
    <hr width="60%" color="black" size="2">
</body>
</html>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<style>
		@page {
			size: legal;
			margin: .5in
		}

		body {
			font-family: Arial, sans-serif;
			font-size: 8pt
		}
		
		th {
        background-color: #f2f2f2;
    }

		.header,
		.footer {
			width: 100%;
		}

		.header .left,
		.header .right {
			display: inline-block;
			vertical-align: top;
		}

		.header .left {
			width: 60%
		}

		.header .right {
			width: 38%;
			text-align: right
		}

		.items-table {
			width: 100%;
			border-collapse: collapse;
			font-size: 7pt;
		}

		.items-table th,
		.items-table td {
			border: 1px solid #000;
			padding: 4px
		}

		.totals {
			width: 100%;
			margin-top: .5rem
		}

		.totals .label {
			font-weight: bold
		}
	</style>
</head>

<body>
	{{-- Encabezado --}}
	<div class="header">
		<div class="left">
			<h4>MEGACHORIZOS SAS</h4>
			<p>NIT: 900490684-3 • CL 35 SUR No 70B-79 • Bogotá D.C.</p>
			<p>Tel: 4614266 • contamegachorizos@gmail.com</p>
		</div>
		<div class="right">
			<p><strong>RECIBO DE CAJA No.</strong> {{ $recibo->id }}</p>
			<p><strong>Fecha:</strong> {{ $recibo->fecha_elaboracion->format('Y-m-d H:i') }}</p>
			<p><strong>USUARIO</strong> {{ $recibo->user->name }}</p>
		</div>
	</div>

	{{-- Datos cliente/tercero --}}
	<p><strong>Cliente:</strong> {{ $recibo->third->name }}</p>
	<p><strong>Nit:</strong> {{ $recibo->third->identification }}</p>

	{{-- Tabla de detalles --}}
	<table class="items-table">
		<thead>
			<tr>
				<th>Fecha Venta</th>
				<th>Factura</th>
				<th>Forma Pago</th>
				<th>Deuda</th>
				<th>Pago</th>
				<th>Saldo</th>
			</tr>
		</thead>
		<tbody>
			@foreach($recibo->details as $d)
			@php
			$ctc = $d->cuentaPorCobrar;
			$sale = optional($ctc)->sale;
			@endphp
			<tr>
				<td>{{ $sale->fecha_venta->format('Y-m-d') }}</td>
				<td>{{ optional($sale)->consecutivo ?? 'N/A' }}</td>
				<td>{{ optional($d->paymentMethod)->nombre ?? 'N/A' }}</td>
				<td align="right">{{ number_format($d->vr_deuda, 0, ',', '.') }}</td>
				<td align="right">{{ number_format($d->vr_pago, 0, ',', '.') }}</td>
				<td align="right">{{ number_format($d->nvo_saldo, 0, ',', '.') }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	{{-- Totales --}}
	<table>
    <thead>
        <tr>
            <th>Concepto</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Pagado:</td>
            <td>{{ number_format($recibo->vr_total_pago, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Saldo Total:</td>
            <td>{{ number_format($recibo->nvo_total_saldo, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

	{{-- Observaciones y pie --}}
	@if($recibo->observations)
	<p><strong>Observaciones:</strong> {{ $recibo->observations }}</p>
	@endif

	<div class="footer">
		<p>Firma: ____________________________</p>
	</div>
</body>
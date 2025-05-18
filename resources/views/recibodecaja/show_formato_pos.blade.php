<!DOCTYPE html>
<html lang="es">

<head>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="application/pdf">

	<title>Recibo de caja</title>

	<!-- cargar a través de la url del sistema -->
	<!--
		<link rel="stylesheet" href="{{ asset('css/custom_pdf.css') }}">
		<link rel="stylesheet" href="{{ asset('css/custom_page.css') }}">
	-->
	<!-- ruta física relativa OS -->
	<link rel="stylesheet" href="{{ public_path('css/pos_custom_pdf.css') }}">
	<link rel="stylesheet" href="{{ public_path('css/pos_custom_page.css') }}">

</head>

<body>
	<section class="" style="top: 0px;">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td class="text-center">
					<span style="font-size: 17px; font-weight: bold; display: block; margin: 0;">RECIBO DE CAJA</span>

					<img src="{{ public_path('assets/img/logo/logo-mega.jpg') }}" alt="" class="invoice-logo" width="33%" style="padding-top: -70px; position: relative">
				</td>
			</tr>
			<tr>

			</tr>
			<tr>
				<td>
					<span style="font-size: 13px; font-weight: bold; display: block; margin-top: 10;">RECIBO DE CAJA No.: {{$recibo->id}}</span>
				</td>
			</tr>
			<tr>
				<td width="100%" class="text-left text-company" style="vertical-align: top; padding-top: 7px">
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 2;">Fecha hora actual:<strong> {{\Carbon\Carbon::now()->format('Y-m-d H:i')}}</strong></span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 2;">CLIENTE:<strong> {{$recibo->third->name}}</strong></span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 2;">NIT:<strong> {{$recibo->third->identification}}</strong></span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 2;">CAJERO:<strong> {{$recibo->user->name}}</strong></span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 2;">Fecha elaboración: <strong>{{ \Carbon\Carbon::parse($recibo->fecha_elaboracion)->format('Y-m-d') }}</strong></span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 2;">Fecha actualizacion: <strong>{{ \Carbon\Carbon::parse($recibo->updated_at_at)->format('Y-m-d H:i') }}</strong></span>
					
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 2;">Estado:
						{{-- Display "Cerrada" if status is 1 --}}
						{{-- Display "Pendiente" if status is 0 --}}
						<strong>{{ $recibo->status == 1 ? 'Cerrado' : 'Pendiente' }}</strong>
					</span>

				</td>
			</tr>
		</table>
	</section>
	<hr>

	
	{{-- Tabla de detalles --}}
	<table class="items-table">
		<thead>
			<tr>
				<th>Fact</th>
				<th>FPago</th>
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
				<td>{{ optional($sale)->consecutivo ?? 'N/A' }}</td>
				<td>{{ optional($d->paymentMethod)->nombre ? substr(optional($d->paymentMethod)->nombre, 0, 4) : 'N/A' }}</td>  
				<td align="right">{{ number_format($d->vr_deuda, 0, ',', '.') }}</td>
				<td align="right">{{ number_format($d->vr_pago, 0, ',', '.') }}</td>
				<td align="right">{{ number_format($d->nvo_saldo, 0, ',', '.') }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<hr width="60mm" color="black" size="3">


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

	<p class="text-center" style="font-size: 12px;">
		<span><strong></strong></span>
	</p>



</body>

</html>
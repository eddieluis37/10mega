<!DOCTYPE html>
<html lang="es">

<head>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="application/pdf">

	<title>Despacho de Ventas</title>

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
	<section class="" style="top: 20px;">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td class="text-center">
					<span style="font-size: 29px; font-weight: bold; display: block; margin: 0;">MEGACHORIZOS</span>
					<span style="font-size: 27px; font-weight: bold; display: block; margin: 0;">COMANDA</span>
					<br>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">{{$sale->namecentrocosto}}</span>
					<span style="font-size: 27px; font-weight: bold; display: block; margin: 0;">#{{$sale->turno_diario}}</span>






					<img src="{{ public_path('assets/img/logo/logo-mega.jpg') }}" alt="" class="invoice-logo" width="33%" style="padding-top: -70px; position: relative">
				</td>
			</tr>
			<tr>

			</tr>

			<tr>
				<td width="100%" class="text-left text-company" style="vertical-align: top; padding-top: 7px">
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Fecha y hora:<strong> {{\Carbon\Carbon::now()->format('Y-m-d H:i')}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Cajero:<strong> {{$sale->nameuser}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Cliente:<strong> {{$sale->namethird}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Nit / C.C.:<strong> {{ number_format($sale->identification,0, ',', '.')}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Dirección envio:<strong> {{$sale->direccion_envio}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Factura interna:<strong> {{$sale->consecutivo}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">
						Estado_Comanda:
						<strong>
							@if($sale->status == 0)
							Abierta
							@elseif($sale->status == 1)
							Cerrada
							@elseif($sale->status == 2)
							Cancelada
							@elseif($sale->status == 3)
							Devuelta
							@else
							Desconocido
							@endif
						</strong>
						<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">
							Items: <strong>{{ $saleDetailCount }}</strong>
						</span>
				</td>
			</tr>
		</table>
	</section>
	<hr>

	<table>
		<tbody>
			@foreach($saleDetails as $item)
			<tr @if($item->is_component) class="ps-4 text-muted" @endif>
				<td align="left">
					@if($item->is_component) &mdash; @endif
					<strong>{{ $item->nameprod }}</strong>

					{{-- Solo para líneas “base” mostramos lote y vencimiento --}}
					@if(!$item->is_component && $item->lote_codigo)
					<br>Lt: {{ $item->lote_codigo }}
					<br>Fv: {{ \Carbon\Carbon::parse($item->lote_fecha_vencimiento)->format('d/m/y') }}
					@endif
				</td>
				<td align="center"><strong>{{ number_format($item->quantity, 0, ',', '.') }}</strong></td>

				
			</tr>
			@endforeach
		</tbody>
		<tfoot>
			<tr>
				<td><b>TOTAL</b></td>
				<td align="center"><strong>{{ $totalQuantity }}</strong></td>
			</tr>
		</tfoot>
	</table>


	<br>
	<br>



	<section class="footer">
		<table cellpadding="0" cellspacing="0" class="table-items" width="100%">
			<tr>
				<td width="20%">
					<span>OBSERVACIONES: </span>
				</td>
				<td width="60%" class="text-center">

				</td>
				<td class="text-center" width="20%">

				</td>

			</tr>
		</table>
	</section>
</body>

</html
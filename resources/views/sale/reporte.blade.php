<!DOCTYPE html>
<html lang="es">

<head>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="application/pdf">

	<title>Reporte de Ventas</title>

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
					<span style="font-size: 17px; font-weight: bold; display: block; margin: 0;">MEGACHORIZOS SAS</span>
					<!-- <span style="font-size: 11px; font-weight: bold; display: block; margin: 2;">900.490.684-3</span> -->
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">CL 35 SUR # 70B - 79</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">{{$sale->namecentrocosto}}</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">Bogotá - Tels: 01-3178302986</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">Resolución DIAN 18764064061708</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">Autorizada el: 2025/01/20 :</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">Prefijo ERPC Del 1 AL 10000</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">Vigencia: 6</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">Responsable de IVA</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">Actividad Economica 4620 Tartifa</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">11.04 Maquina contamegachorizos@gmail.com </span>
					<img src="{{ public_path('assets/img/logo/logo-mega.jpg') }}" alt="" class="invoice-logo" width="33%" style="padding-top: -70px; position: relative">
				</td>
			</tr>
			<tr>

			</tr>
			<tr>
				<td colspan=" 2" class="">
					<span style="font-size: 13px; font-weight: bold; display: block; margin-top: 3;">Sistema POS: {{$sale->resolucion}}</span>
				</td>
			</tr>
			<tr>
				<td width="100%" class="text-left text-company" style="vertical-align: top; padding-top: 7px">
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Fecha y hora:<strong> {{\Carbon\Carbon::now()->format('Y-m-d H:i')}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Cajero:<strong> {{$sale->nameuser}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Cliente:<strong> {{$sale->namethird}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Nit / C.C.:<strong> {{ number_format($sale->identification,0, ',', '.')}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Dirección:<strong> {{$sale->direccion}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Factura interna:<strong> {{$sale->consecutivo}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">
						Estado_Factura:
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
					</span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Items:<strong>{{$sale->sum('items')}}</strong></span>
				</td>
			</tr>
		</table>
	</section>
	<hr>

	<table align="left">
		<thead>
			<tr>
				<th width="70%">Descripción</th>
				<th width="7%">Cant.</th>
				<th width="10%">Vr.unit</th>
				<th width="10%">Vr.Total</th>
			</tr>
		</thead>
		<tbody>
			@foreach($saleDetails as $item)
			<tr>
				<td align="left">
					<strong>{{$item->nameprod}}</strong>
					@if($item->lote_codigo)
					Lt:{{$item->lote_codigo}}<br>
					Fv:{{ \Carbon\Carbon::parse($item->lote_fecha_vencimiento)->format('d/m/y') }}
					@endif
				</td>
				<td align="center"><strong>{{$item->quantity}}</strong></td>
				<td align="center"><strong>{{ number_format($item->price, 0, ',', '.') }}</strong></td>
				<td align="left"><strong>{{ number_format($item->total, 0, ',', '.') }}</strong></td>
			</tr>
			@endforeach
		</tbody>
		<tfoot>
			<tr>
				<td>
					<span><b>TOTAL</b></span>
				</td>
				<td align="left">
					<span><strong>{{ $quantity = $item->where('sale_id', '=', $item->sale_id)->sum('quantity') }}</strong></span>
				</td>
				<td></td>
				<td align="left">
					<span><strong>{{ number_format($sale->sum('total_valor_a_pagar'), 0, ',', '.') }}</strong></span>
				</td>
			</tr>
		</tfoot>
	</table>

	<hr>******************
	<p class="text-center" style="font-size: 12px;">
		<span><strong>Forma de pago</strong></span>
	</p>

	@if($sale->valor_a_pagar_efectivo != 0)
	<p class="text-right" style="font-size: 12px;">
		<strong>
			<span>EFECTIVO: </span>
			<span>{{ number_format($sale->valor_a_pagar_efectivo, 0, ',', '.') }}</span>
		</strong>
	</p>
	@endif

	@if($sale->valor_a_pagar_tarjeta != 0)
	<p class="text-right" style="font-size: 12px;">
		<strong>
			<span>{{ $sale->formapago1 }}: </span>
			<span>{{ number_format($sale->valor_a_pagar_tarjeta, 0, ',', '.') }}</span>
		</strong>
	</p>
	@endif

	@if($sale->valor_a_pagar_otros != 0)
	<p class="text-right" style="font-size: 12px;">
		<strong>
			<span>{{ $sale->formapago2 }}: </span>
			<span>{{ number_format($sale->valor_a_pagar_otros, 0, ',', '.') }}</span>
		</strong>
	</p>
	@endif

	@if($sale->valor_a_pagar_credito != 0)
	<p class="text-right" style="font-size: 12px;">
		<strong>
			<span>{{ $sale->formapago3 }}: </span>
			<span>{{ number_format($sale->valor_a_pagar_credito, 0, ',', '.') }}</span>
		</strong>
	</p>
	@endif

	<p class="text-right" style="font-size: 12px;">
		<span><strong>Cambio: {{ number_format($sale->cambio,0, ',', '.')}}</strong></span>
	</p>
	<hr width="60mm" color="black" size="3">
	<p align="center" style="font-size: 11px; margin-top: 8px;"><strong>A esta factura de venta aplican las normas relativas a la letra de cambio (artículo 5 Ley 1231 de 2008). Con esta el Comprador declara haber recibido real y materialmente las mercancías o prestación de servicios descritos en este título - Valor. Número Autorización 18764064061708 aprobado en 20240120 prefijo ERPC desde el número 1 al 10000, del dia 20 de enero de 2024, Vigencia: 6 Meses</strong></p>
	<p align="center" style="font-size: 11px; margin: -8px;"><strong>Responsable de IVA - Actividad Económica 4620 Comercio al por mayor de materias primas agropecuarias; animales vivos Tarifa 11.04</strong></p>

	<!-- <section class="footer">
		<table cellpadding="0" cellspacing="0" class="table-items" width="100%">
			<tr>
				<td width="20%">
					<span>Sistema PuraCarnes v1</span>
				</td>
				<td width="60%" class="text-center">
					Admin@puracarnes.com
				</td>
				<td class="text-center" width="20%">
					página <span class="pagenum"></span>
				</td>

			</tr>
		</table>
	</section> -->
</body>

</html>
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
	<link rel="stylesheet" href="{{ public_path('css/pos_custom_page_arial.css') }}">

</head>

<body>
	<section class="" style="top: 0px;">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td class="text-center">
					<span style="font-size: 17px; font-weight: bold; display: block; margin: 0;">MEGACHORIZOS SAS</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 2;">NIT 900.490.684-3</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">{{ $sale->direccion }}</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">Bogotá - Tels: 01-3178302986</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">{{ $sale->namecentrocosto }}</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">HABILITACION CON</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">AUTORIZACION DE FACTURACION {{ $sale->resolucion_dian }}</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">
						DE: {{ \Carbon\Carbon::parse($sale->fecha_inicial)->format('d-m-Y') }} DESDE {{ $sale->prefijo }} {{ $sale->desde }}
					</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">
						HASTA {{ $sale->hasta }}
					</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">CON VIGENCIA DE 24 MESES</span>
					<!-- <span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">
						Vigencia: {{ \Carbon\Carbon::parse($sale->fecha_final)->format('d-m-Y') }}
					</span> -->
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">Responsable de IVA</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">Actividad Economica 4620 Tarifa</span>
					<span style="font-size: 11px; font-weight: bold; display: block; margin: 0;">11.04 Maquina contamegachorizos@gmail.com</span>
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
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Fecha hora actual:<strong> {{\Carbon\Carbon::now()->format('Y-m-d H:i')}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Fecha hora ventas:<strong> {{$sale->created_at->format('Y-m-d H:i')}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Cajero:<strong> {{$sale->nameuser}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Cliente:<strong> {{$sale->namethird}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Celular:<strong> {{$sale->celularcliente}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Nit / C.C.:<strong> {{ number_format($sale->identification,0, ',', '.')}}</strong></span>
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">Dirección:<strong> {{$sale->direccion_envio}}</strong></span>
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
					<span style="font-size: 11px; font-weight: lighter; display: block; margin: 2;">
						Items: <strong>{{ $saleDetailCount }}</strong>
					</span>

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
					Lt:{{$item->lote_codigo}}@endif
					@if($sale->tipo <> 4) Fv:{{ \Carbon\Carbon::parse($item->lote_fecha_vencimiento)->format('d/m/y') }}@endif					
				</td>
				<td align="center"><strong>{{$item->quantity}}</strong></td>
				<td align="center"><strong>{{ number_format($item->price_venta, 0, ',', '.') }}</strong></td>
				<td align="right"><strong>{{ number_format($item->total_bruto, 0, ',', '.') }}</strong></td>
			</tr>
			@endforeach
		</tbody>
		<tfoot>
			@if($totalIva != 0)
			<tr>
				<td>
					<span><b>IVA</b></span>
				</td>
				<td></td>
				<td></td>
				<td align="right">
					<span><strong>{{ number_format($totalIva, 0, '.', '.' )}}</strong></span>
				</td>
			</tr>
			@endif
			@if($totalOtroImp != 0)
			<tr>
				<td>
					<span><b>UP</b></span>
				</td>
				<td></td>
				<td></td>
				<td align="right">
					<span><strong>{{ number_format($totalOtroImp, 0, '.', '.' )}}</strong></span>
				</td>
			</tr>
			@endif
			@if($totalIC != 0)
			<tr>
				<td>
					<span><b>IC</b></span>
				</td>
				<td></td>
				<td></td>
				<td align="right">
					<span><strong>{{ number_format($totalIC, 0, '.', '.' )}}</strong></span>
				</td>
			</tr>
			@endif
			@if($totalDesProd != 0)
			<tr>
				<td>
					<span><b>Desc_Producto</b></span>
				</td>
				<td></td>
				<td></td>
				<td align="right">
					<span><strong>-{{ number_format($totalDesProd, 0, '.', '.' )}}</strong></span>
				</td>
			</tr>
			@endif
			@if($totalDesClient != 0)
			<tr>
				<td>
					<span><b>Desc_Cliente</b></span>
				</td>
				<td></td>
				<td></td>
				<td align="right">
					<span><strong>-{{ number_format($totalDesClient, 0, '.', '.' )}}</strong></span>
				</td>
			</tr>
			@endif
			<tr>
				<td>
					<span><b>TOTAL</b></span>
				</td>
				<td align="left">
					<span><strong>{{ $totalQuantity }}</strong></span>
				</td>
				<td></td>
				<td align="right">
					<span><strong>{{ number_format($totalApagar, 0, '.', '.' )}}</strong></span>
				</td>
			</tr>
		</tfoot>
	</table>


	<table style="width: 100%; font-size: 12px; border-collapse: collapse;">
		<thead>
			<tr>
				<th colspan="2" style="text-align: center; border-bottom: 1px solid #000;">
					<strong>Forma de pago</strong>
				</th>
			</tr>
		</thead>
		<tbody>
			@if($sale->valor_a_pagar_efectivo != 0)
			<tr>
				<td style="text-align: right; padding: 5px;"><strong>EFECTIVO:</strong></td>
				<td style="text-align: right; padding: 5px;"><strong>{{ number_format($sale->valor_a_pagar_efectivo, 0, ',', '.') }}</strong></td>
			</tr>
			@endif

			@if($sale->valor_a_pagar_tarjeta != 0)
			<tr>
				<td style="text-align: right; padding: 5px;"><strong>{{ $sale->formapago1 }}:</strong></td>
				<td style="text-align: right; padding: 5px;"><strong>{{ number_format($sale->valor_a_pagar_tarjeta, 0, ',', '.') }}</strong></td>
			</tr>
			@endif

			@if($sale->valor_a_pagar_tarjeta2 != 0)
			<tr>
				<td style="text-align: right; padding: 5px;"><strong>{{ $sale->formapagot2 }}:</strong></td>
				<td style="text-align: right; padding: 5px;"><strong>{{ number_format($sale->valor_a_pagar_tarjeta2, 0, ',', '.') }}</strong></td>
			</tr>
			@endif

			@if($sale->valor_a_pagar_tarjeta3 != 0)
			<tr>
				<td style="text-align: right; padding: 5px;"><strong>{{ $sale->formapagot3 }}:</strong></td>
				<td style="text-align: right; padding: 5px;"><strong>{{ number_format($sale->valor_a_pagar_tarjeta3, 0, ',', '.') }}</strong></td>
			</tr>
			@endif

			@if($sale->valor_a_pagar_otros != 0)
			<tr>
				<td style="text-align: right; padding: 5px;"><strong>{{ $sale->formapago2 }}:</strong></td>
				<td style="text-align: right; padding: 5px;"><strong>{{ number_format($sale->valor_a_pagar_otros, 0, ',', '.') }}</strong></td>
			</tr>
			@endif

			@if($sale->valor_a_pagar_credito != 0)
			<tr>
				<td style="text-align: right; padding: 5px;"><strong>{{ $sale->formapago3 }}:</strong></td>
				<td style="text-align: right; padding: 5px;"><strong>{{ number_format($sale->valor_a_pagar_credito, 0, ',', '.') }}</strong></td>
			</tr>
			@endif

			<tr>
				<td style="text-align: right; padding: 5px;"><strong>Cambio:</strong></td>
				<td style="text-align: right; padding: 5px;"><strong>{{ number_format($sale->cambio, 0, ',', '.') }}</strong></td>
			</tr>
		</tbody>
	</table>


	<hr width="60mm" color="black" size="3">
	<p align="center" style="font-size: 11px; margin-top: 8px;"><strong>A esta factura de venta aplican las normas relativas a la letra de cambio (artículo 5 Ley 1231 de 2008). Con esta el Comprador declara haber recibido real y materialmente las mercancías o prestación de servicios descritos en este título - Valor. Número Autorización 18764073449011 aprobado en GUAA prefijo desde el número 1 al 6000, del dia 20 de Junio de 2024, Vigencia: 12 Meses</strong></p>
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
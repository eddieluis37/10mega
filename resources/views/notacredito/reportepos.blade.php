{{-- resources/views/notacredito_ticket_pdf.blade.php --}}
<!doctype html>
<html lang="es">

<head>
	<meta charset="utf-8" />
	<title>Nota Crédito - Ticket PDF</title>
	<style>
		/*
      AJUSTE: Cambiar 80mm a 58mm si tu papel es de 58mm.
      DOMPDF respeta @page size en mm.
    */
		@page {
			size: 80mm auto;
			/* <-- Cambia a 58mm si usas 58mm */
			margin: 2mm 2mm 2mm 2mm;
			/* pequeños márgenes para que no corte */
		}

		html,
		body {
			margin: 0;
			padding: 0;
			-webkit-print-color-adjust: exact;
			color: #000;
			font-family: "DejaVu Sans", "Arial", sans-serif;
			/* DejaVu soporta acentos en DOMPDF */
			font-size: 10px;
			/* tamaño base, ajustar si es necesario */
		}

		/* Contenedor principal al ancho del ticket */
		.ticket {
			width: 76mm;
			/* algo menor que el page width para respetar márgenes */
			margin: 0 auto;
			padding: 0;
			box-sizing: border-box;
			line-height: 1.05;
		}

		.center {
			text-align: center;
		}

		.left {
			text-align: left;
		}

		.right {
			text-align: right;
		}

		.company {
			font-weight: 700;
			font-size: 12px;
			margin-bottom: 2px;
		}

		.small {
			font-size: 9px;
		}

		.tiny {
			font-size: 8px;
		}

		.sep {
			border-top: 1px dashed #000;
			margin: 6px 0;
			padding: 0;
		}

		/* Metadatos */
		.meta {
			font-size: 9px;
			margin: 4px 0;
		}

		.meta .row {
			display: flex;
			justify-content: space-between;
		}

		/* Items: columnas compactas */
		table.items {
			width: 100%;
			border-collapse: collapse;
			font-size: 9px;
			margin-top: 4px;
		}

		table.items thead td {
			font-weight: 700;
			padding: 3px 0;
		}

		table.items tbody td {
			padding: 2px 0;
			vertical-align: top;
		}

		.qty {
			width: 14%;
		}

		.desc {
			width: 56%;
			padding-left: 4px;
			word-break: break-word;
		}

		.unit {
			width: 15%;
			text-align: right;
			padding-left: 4px;
		}

		.total {
			width: 15%;
			text-align: right;
			padding-left: 4px;
		}

		/* Totales */
		.totales {
			margin-top: 6px;
			font-size: 9px;
		}

		.totales .row {
			display: flex;
			justify-content: space-between;
			padding: 1px 0;
		}

		.totales .grand {
			font-weight: 700;
			font-size: 11px;
		}

		/* Pie/cude */
		.footer {
			margin-top: 6px;
			font-size: 8px;
			text-align: center;
		}

		/* Evitar saltos extra entre tablas en DOMPDF */
		.no-break {
			page-break-inside: avoid;
		}
	</style>
</head>

<body>
	<div class="ticket">

		{{-- ENCABEZADO --}}
		<div class="center">
			<div class="company">{{ strtoupper($sale->company_name ?? 'MEGACHORIZOS SAS') }}</div>
			<div class="small">{{ $sale->company_nit ?? 'NIT: 900.490.684 - 3' }}</div>
			<div class="small">{{ $sale->company_address ?? 'CL 35 SUR No 70B 79 BOGOTÁ DC' }}</div>
			<div class="small">{{ $sale->company_phone ?? 'Tel: 4614266' }}</div>
			@if(!empty($sale->company_website))
			<div class="small">{{ $sale->company_website }}</div>
			@endif
			<div class="sep"></div>

			<div class="big" style="font-weight:700;">NOTA CRÉDITO</div>
			<div class="small">N° <strong>{{ $sale->consecutivo }}</strong></div>
		</div>

		{{-- METADATOS CLIENTE / FECHA --}}
		<div class="meta">
			<div style="display:flex; justify-content:space-between;">
				<div class="left small"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($sale->created_at ?? now())->format('Y-m-d H:i') }}</div>
				<div class="right small"><strong>Vence:</strong> {{ $sale->due_date ?? '-' }}</div>
			</div>
			<div class="small"><strong>Cliente:</strong> {{ $sale->namethird }}</div>
			<div style="display:flex; justify-content:space-between;">
				<div class="small"><strong>Nit:</strong> {{ $sale->identification }}</div>
				<div class="small"><strong>Vendedor:</strong> {{ $sale->nameuser ?? '-' }}</div>
			</div>
			<div class="small"><strong>Dirección:</strong> {{ $sale->direccion ?? '-' }}</div>
		</div>

		<div class="sep"></div>

		{{-- TABLA ITEMS --}}
		<table class="items no-break" role="presentation">
			<thead>
				<tr>
					<td class="qty small">CANT</td>
					<td class="desc small">DESCRIPCIÓN</td>
					<td class="unit small">V. UNIT</td>
					<td class="total small">TOTAL</td>
				</tr>
			</thead>
			<tbody>
				@foreach($detalleItems as $d)
				<tr>
					<td class="qty small right">
						{{-- Quitar ceros innecesarios: 1.00 -> 1 --}}
						{{ rtrim(rtrim(number_format($d->cantidad, 2, ',', '.'), '0'), ',') }}
					</td>
					<td class="desc small left">
						{{-- Limitar largo para que no rompa el diseño --}}
						{{ \Illuminate\Support\Str::limit($d->name, 48) }}
						@if(!empty($d->code)) <div class="tiny">Cód: {{ $d->code }}</div> @endif
					</td>
					<td class="unit small right">${{ number_format($d->unitario ?? $d->price ?? 0, 0, ',', '.') }}</td>
					<td class="total small right">${{ number_format($d->line_total ?? 0, 0, ',', '.') }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>

		<div class="sep"></div>

		{{-- TOTALES --}}
		<div class="totales no-break">
			<div class="row">
				<div class="left small">SUBTOTAL BASE</div>
				<div class="right small">${{ number_format($totales['subtotal_base'] ?? 0, 0, ',', '.') }}</div>
			</div>
			<div class="row">
				<div class="left small">IVA</div>
				<div class="right small">${{ number_format($totales['iva'] ?? 0, 0, ',', '.') }}</div>
			</div>
			<div class="row">
				<div class="left small">ULTRAPROC.</div>
				<div class="right small">${{ number_format($totales['ultra'] ?? 0, 0, ',', '.') }}</div>
			</div>
			<div class="row">
				<div class="left small">IMP. CONSUMO</div>
				<div class="right small">${{ number_format($totales['impoconsumo'] ?? 0, 0, ',', '.') }}</div>
			</div>
			<div class="row">
				<div class="left small">DESCUENTO</div>
				<div class="right small">- ${{ number_format($totales['descuento'] ?? 0, 0, ',', '.') }}</div>
			</div>

			<div class="sep"></div>
			<div class="row grand">
				<div class="left">TOTAL A DEVOLVER</div>
				<div class="right grand">${{ number_format($totales['total_devolver'] ?? ($sale->total ?? 0), 0, ',', '.') }}</div>
			</div>
		</div>

		<div class="sep"></div>

		{{-- FORMA DE PAGO / CUDE --}}
		<div class="meta small">
			<div><strong>Forma Pago Factura:</strong> {{ $salePaymentName ?? 'N/A' }}</div>
			<div><strong>Forma Pago NC:</strong> {{ $ncPaymentName ?? 'N/A' }}</div>
		</div>

		<div class="footer tiny">
			<div><strong>REPRESENTACIÓN GRÁFICA</strong></div>
			<div class="tiny">CUDE: {{ $sale->cude ?? '...' }}</div>
			<div class="sep"></div>
			<div>Documento generado electrónicamente - No requiere firma</div>
			<div class="tiny">Gracias por su compra</div>
		</div>

	</div>
</body>

</html>
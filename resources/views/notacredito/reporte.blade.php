<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Nota Crédito Electrónica</title>
	<!-- Bootstrap 5 CSS -->
	<link
		href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
		rel="stylesheet">
	<style>
		/********************************************************
     * CONFIGURACIÓN DE PÁGINA PARA IMPRESIÓN (TAMAÑO OFICIO)
     ********************************************************/
		@page {
			size: 8.5in 13in;
			/* Tamaño oficio */
			margin: 0.5in;
			/* Ajusta este margen según lo necesites */
		}

		body {
			margin: 0;
			padding: 0;
			font-family: Arial, sans-serif;
			font-size: 5pt;
			-webkit-print-color-adjust: exact;
			/* Para conservar colores en impresión */
		}

		/* Contenedor principal que simula la hoja */
		.page {
			width: 100%;
			box-sizing: border-box;
			margin: 0 auto;
			padding: 0;
		}

		/* Encabezado: parte superior con datos de la empresa a la izquierda y logo a la derecha */
		.header-left h5 {
			font-weight: bold;
			margin-bottom: 0.2rem;
		}

		.header-left p {
			margin-bottom: 0.2rem;
			line-height: 1.2;
			font-size: 8pt;
		}

		.header-right {
			text-align: right;
		}

		.header-right img {
			max-height: 50px;
			/* Ajusta si deseas otro tamaño para el logo */
		}

		/* Título de la Nota */
		.nota-titulo {
			font-size: 14pt;
			font-weight: bold;
			margin: 0;
			line-height: 1.2;
		}

		.nota-numero {
			margin: 0;
			line-height: 1.2;
			font-size: 10pt;
		}

		/* Separador horizontal */
		.separator {
			border-top: 1px solid #000;
			margin: 0.5rem 0;
		}

		/* Bloque de información (cliente, fechas, etc.) */
		.info-block p {
			margin-bottom: 0.3rem;
			line-height: 1.2;
		}

		.info-block p strong {
			font-weight: bold;
		}

		/* Bloque de despacho / observaciones (si lo deseas) */
		.despacho-block p {
			margin-bottom: 0.3rem;
			line-height: 1.2;
		}

		/* Tabla de ítems */
		.items-table {
			width: 100%;
			border-collapse: collapse;
			font-size: 9pt;
			/* Ajusta si quieres más grande/pequeño */
			margin-bottom: 1rem;
		}

		.items-table thead {
			background-color: #f2f2f2;
		}

		.items-table th,
		.items-table td {
			border: 1px solid #000;
			padding: 4px 6px;
			vertical-align: middle;
		}

		.items-table th {
			font-weight: bold;
			text-align: left;
		}

		/* Ajusta anchos si necesitas precisión */
		.items-table th:nth-child(1) {
			width: 8%;
		}

		/* CÓDIGO */
		.items-table th:nth-child(2) {
			width: 28%;
		}

		/* DESCRIPCIÓN */
		.items-table th:nth-child(3) {
			width: 8%;
		}

		/* UND */
		.items-table th:nth-child(4) {
			width: 10%;
		}

		/* Vr UNIT */
		.items-table th:nth-child(5) {
			width: 8%;
		}

		/* CANT */
		.items-table th:nth-child(6) {
			width: 6%;
		}

		/* %DSC */
		.items-table th:nth-child(7) {
			width: 6%;
		}

		/* %IVA */
		.items-table th:nth-child(8) {
			width: 6%;
		}

		/* %RTF */
		.items-table th:nth-child(9) {
			width: 20%;
		}

		/* VALOR TOTAL */

		/* Totales (subtotales, impuestos, etc.) */
		.totales-block {
			margin-top: 0.5rem;
			margin-bottom: 0.5rem;
		}

		.totales-block .row>div {
			margin-bottom: 0.2rem;
		}

		.totales-block p {
			margin: 0;
			line-height: 1.2;
		}

		.totales-left {
			/* Ajusta si deseas un ancho distinto */
		}

		.totales-right {
			text-align: right;
		}

		/* Pie de página / disclaimers */
		.footer {
			margin-top: 0.5rem;
			text-align: left;
			font-size: 8pt;
		}

		.footer p {
			margin: 0.2rem 0;
			line-height: 1.2;
		}

		.footer .cude {
			font-size: 7pt;
		}
	</style>
</head>

<body>
	<div class="page">
		<!-- ENCABEZADO -->
		<div class="container-fluid">
			<div class="row">
				<!-- Columna izquierda: Datos de la empresa -->
				<div class="col-8 header-left">
					<h5>MEGACHORIZOS SAS</h5>
					<p>NIT: 900.490.684 - 3 REGIMEN COMÚN</p>
					<p>CL 35 SUR No 70B 79 BOGOTÁ DC</p>
					<p>Teléfonos: 4614266</p>
					<p>Actividad Económica 1011 Tarifa AutoRenta 0.40%</p>
					<p>No somos Autorretenedores - No somos Grandes contribuyentes</p>
					<p>WWW.MEGACHORIZOS.CO &nbsp; contamegachorizos@gmail.com</p>
					<p>
						Habilitación con autorización de facturación N 18764078050070 de 27/08/2024 desde FEM 5.001 hasta FEM 10.000 con vigencia 24 meses
					</p>
				</div>
				<!-- Columna derecha: Logo + Nota -->
				<div class="col-4 header-right">
					<!-- Logo (si lo tienes): 
        <img src="ruta-al-logo.png" alt="Logo" class="mb-2">
        -->
					<!-- 	<p class="nota-titulo">NOTA CRÉDITO ELECTRÓNICA</p>
					<p class="nota-numero">N° <strong>165</strong></p> -->
				</div>
			</div>

			<!-- <div class="separator"></div> -->

			<!-- BLOQUE DE INFORMACIÓN (Cliente, Fechas, etc.) -->
			<style>
				/* Reiniciamos márgenes y definimos estilos base */
				body {
					margin: 0;
					padding: 0;
					font-family: Arial, sans-serif;
					font-size: 10pt;
				}

				/* Tabla externa que ocupa el 100% del ancho */
				.outer-table {
					width: 100%;
					border: 1px solid #000;
					border-collapse: collapse;
				}

				.outer-table td {
					border: 0px solid #000;
					padding: 6px;
					vertical-align: top;
				}

				/* Tablas internas */
				.inner-table {
					width: 100%;
					border-collapse: collapse;
				}

				.inner-table td {
					padding: 4px;
				}

				/* Etiquetas para los campos */
				.label {
					font-weight: bold;
					width: 30%;
					white-space: nowrap;
				}

				/* Encabezado de la nota */
				.nota-encabezado {
					text-align: center;
					font-weight: bold;
				}
			</style>

			<table class="outer-table">
				<!-- Fila superior: Datos generales (Cliente y Dirección) y Nota en paralelo -->
				<tr>
					<td colspan="2">
						<table class="inner-table">
							<tr>
								<td class="label">Cliente :</td>
								<td>{{$sale[0]->namethird}}</td>
							</tr>
							<tr>
								<td class="label">Nit :</td>
								<td>{{$sale[0]->identification}}</td>
							</tr>
							<tr>
								<td class="label">Dirección :</td>
								<td>{{$sale[0]->direccion}}d</td>
							</tr>
						</table>
					</td>
					<td rowspan="2" style="width: 35%;">
						<table class="inner-table">
							<tr>
								<td colspan="2" class="nota-encabezado">NOTA CRÉDITO ELECTRONICA</td>
							</tr>
							<tr>
								<td class="label">Nro. :</td>
								<td>{{$sale[0]->consecutivo}}</td>
							</tr>
							<tr>
								<td class="label">Fecha :</td>
								<td>{{\Carbon\Carbon::now()->format('Y-m-d H:i')}}</td>
							</tr>
							<tr>
								<td class="label">Vence :</td>
								<td>19 Octubre 2024</td>
							</tr>
							<tr>
								<td class="label">Pago :</td>
								<td>CRÉDITO 1</td>
							</tr>
						</table>
					</td>
				</tr>
				<!-- Fila inferior: Datos que se emparejan en dos columnas -->
				<tr>
					<!-- Primera columna (datos de la antigua izquierda) -->
					<td style="width: 50%;">
						<table class="inner-table">
							<tr>
								<td class="label">Ciudad :</td>
								<td>BOGOTÁ</td>
							</tr>
							<tr>
								<td class="label">País :</td>
								<td>COLOMBIA</td>
							</tr>
							<tr>
								<td class="label">Teléfonos :</td>
								<td>3118447200</td>
							</tr>
							<tr>
								<td class="label">Vendedor :</td>
								<td>{{$sale[0]->nameuser}}</td>
							</tr>
						</table>
					</td>
					<!-- Segunda columna (datos de la antigua central) -->
					<td style="width: 50%;">
						<table class="inner-table">

							<tr>
								<td class="label">Zona :</td>
								<td>[Opcional]</td>
							</tr>
							<tr>
								<td class="label">FAX :</td>
								<td>[Opcional]</td>
							</tr>
							<tr>
								<td class="label">E-mail :</td>
								<td>{{$sale[0]->email}}</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>

			<br>
			<!-- TABLA DE ÍTEMS -->
			<div class="row">
				<div class="col-12">
					<table class="items-table">
						<thead>
							<tr>
								<th>CÓDIGO</th>
								<th>DESCRIPCIÓN</th>
								<th>UND</th>
								<th>Vr_UNITARIO</th>
								<th>CANTIDAD</th>
								<th>%DSC</th>
								<th>%IVA</th>
								<th>%RTF</th>
								<th>VALOR_TOTAL</th>
							</tr>
						</thead>
						<tbody>
							@foreach($saleDetails as $d)
							@php
							// --- Ajusta nombres de campos si es necesario ---
							$unitario = $d->price ?? $d->valor_unitario ?? 0; // precio unitario en notacredito_details
							$cantidad = $d->quantity ?? $d->cantidad ?? 0;
							// Porcentaje de descuento puede venir por detalle o por la cabecera sale (third.porc_descuento)
							$porcDesc = $d->porc_desc ?? ($sale->porc_descuento ?? 0);
							$porcIva = $d->iva ?? 0; // porcentaje IVA (ej: 5)
							$porcRtf = $d->porc_otro_impuesto ?? 0; // porcentaje de retención/otro impuesto

							// Cálculos
							$subtotal = $unitario * $cantidad;
							$descuento = $subtotal * ($porcDesc / 100);
							$base = $subtotal - $descuento;
							$iva = $base * ($porcIva / 100);
							$rtf = $base * ($porcRtf / 100);

							// Valor total por línea (puedes elegir sumar o restar impuestos según tu regla)
							$valorTotal = $base + $iva - $rtf;

							// Formateo para presentación (coma decimal y punto miles)
						//	function fmt($n){ return number_format((float)$n, 0, ',', '.'); }
						 	function fmt($n){ return number_format((float)$n, 2, ',', '.'); }
							@endphp

							<tr>
								<td>{{ $d->code ?? '' }}</td>
								<td>{{ $d->nameprod ?? $d->descripcion ?? '' }}</td>
								<td>{{ $d->unitofmeasure_id ?? '' }}</td>
								<td style="text-align:right;">{{ fmt($unitario) }}</td>
								<td style="text-align:right;">{{ fmt($cantidad) }}</td>
								<td style="text-align:right;">{{ number_format($porcDesc, 2, ',', '.') }}%</td>
								<td style="text-align:right;">{{ number_format($porcIva, 2, ',', '.') }}%</td>
								<td style="text-align:right;">{{ number_format($porcRtf, 2, ',', '.') }}%</td>
								<td style="text-align:right;">{{ fmt($valorTotal) }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>


			<!-- TOTALES -->
			<div class="row totales-block">
				<div class="col-6 totales-left">
					<p><strong>Subtotal:</strong> $7,385,111.51</p>
					<p><strong>Imp. Ultraprocesados:</strong> $0.00</p>
					<p><strong>Imp. al consumo:</strong> $0.00</p>
					<p><strong>Retefuente:</strong> $0.00</p>
					<p><strong>ICA Retenido:</strong> $0.00</p>
				</div>
				<div class="col-6 totales-right">
					<p><strong>I.V.A. (19%):</strong> $369,256.00</p>
					<p><strong>IVA Retenido:</strong> $184,627.79</p>
					<p><strong>Descuento:</strong> $30,574.36</p>
					<p><strong>Total Neto:</strong> $7,385,112.00</p>
					<p><strong>Total a Pagar:</strong> $7,539,165.00</p>
				</div>
			</div>

			<!-- PIE DE PÁGINA / NOTAS FINALES -->
			<div class="row footer">
				<div class="col-12">
					<p>SOMOS RETENEDORES DE ICA A RÉGIMEN COMÚN Y SIMPLIFICADO</p>
					<p>
						<strong>REPRESENTACIÓN GRÁFICA DE FACTURA</strong><br>
						<span class="cude">
							CUDE: 5b78168b91641243163fa6fb6bb9f81656d09a5127d2f383645e3179d87d8f80892c480e7cf
						</span>
					</p>
					<p>
						Las partes acuerdan que cualquier problema jurídico o prejurídico se resuelve en la ciudad de BOGOTÁ D.C.<br>
						Favor consignar a la cuenta corriente Nº 22978458294 de Bancolombia o a la cuenta corriente Nº 17901648 del BBVA a nombre de MEGACHORIZOS SAS.
					</p>
					<p>
						<strong>El (los) comprador(es) la afirma(n) en señal de aceptación y de haber recibido real y materialmente la mercancía y/o el servicio.</strong>
					</p>
				</div>
			</div>
		</div>
	</div>

	<!-- Bootstrap 5 JS (Opcional) -->
	<script
		src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
	</script>
</body>

</html>
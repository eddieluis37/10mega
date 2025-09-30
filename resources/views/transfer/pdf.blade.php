<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title>Traslado #{{ $transfer->id }}</title>
	<link rel="stylesheet" href="{{ public_path('css/pos_custom_pdf.css') }}">
	<link rel="stylesheet" href="{{ public_path('css/pos_custom_page_arial.css') }}">
</head>

<body>
	<header class="text-center">
		<span style="font-size: 29px; font-weight: bold; display: block; margin: 0;">MEGACHORIZOS</span>
		<span style="font-size: 27px; font-weight: bold; display: block; margin: 0;">TRASLADO {{ $transfer->id }}</span>
		<p><strong>Bodega Origen:</strong> {{ $transfer->bodegaOrigen->name }}</p>
		<p><strong>Bodega Destino:</strong> {{ $transfer->bodegaDestino->name }}</p>
		<img src="{{ public_path('assets/img/logo/logo-mega.jpg') }}" width="100px" style="margin-top:10px">
		<hr>
		<table width="100%" cellpadding="2">
			<tr>
				<td><small>Fecha/Hora:</small> <strong>{{ $transfer->fecha_tranfer }}</strong></td>
				<td><small>Usuario:</small> <strong>{{ $transfer->user->name }}</strong></td>
				<td><small>Estado:</small>
					<strong>
						{{ $transfer->status ? 'Activo' : 'Inactivo' }}
						({{ ucfirst($transfer->inventario) }})
					</strong>
				</td>
			</tr>
		</table>
		<hr>
	</header>

	<main>
		<table width="100%" cellspacing="0" cellpadding="4">
			<thead>
				<tr>
					<th align="left">Descripción</th>
					<th align="center">Cantidades</th>
				</tr>
			</thead>
			<tbody>
				@foreach($details as $item)
				<tr>
					<td>
						<strong>{{ $item->product->name }}<br>
						<small>Código: {{ $item->product->code }}</small><br>
						@if($item->lote)
						<small>
							Lt: {{ $item->lote->codigo }}
							Fv: {{ \Carbon\Carbon::parse($item->lote->fecha_vencimiento)->format('d/m/Y') }}
						</small></strong>
						@endif
					</td>
					<td align="center"><strong>{{ number_format($item->kgrequeridos, 2, '.', '.') }}</strong></td>
				</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td><strong>Items:</strong> {{ $detailCount }}</td>
					<td><strong>Total:</strong> {{ number_format($totalKilos, 2, '.', '.') }}</td>
				</tr>
			</tfoot>
		</table>
	</main>

	<section class="footer">
		<table cellpadding="0" cellspacing="0" class="table-items" width="100%">
			<tr>
				<td width="20%">
					<span>OBSERVACIONES: </span>
				</td>
				<td width="60%" class="text-center">
					{{ $transfer->observaciones ?? '—' }}
				</td>

				<td class="text-center" width="20%">

				</td>

			</tr>
			<tr>
				<td>{{ $now }}</td>
			</tr>
		</table>
	</section>
</body>

</html>
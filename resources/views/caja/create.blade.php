@extends('layouts.theme.app')
@section('content')
<style>
	.table-totales {
		border: 2px solid red;
	}

	.table-inventario,
	th,
	td {
		border: 0px solid #DCDCDC;
		text-align: center;
	}

	.input {
		height: 38px;
	}

	td {
		text-align: right;
		font-weight: bold;
		color: black;
	}
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row sales layout-top-spacing">
	<div class="col-sm-12">
		<br>
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>Caja | Cuadre</b>
				</h4>
				<ul class="tabs tab-pills">
					<li>
						<a href="{{ url('/caja') }}" class="tabmenu bg-dark" title="Regresa al listado">Volver</a>
					</li>
				</ul>
			</div>

			<!-- Asegúrate de incluir Bootstrap en tu layout o en este archivo -->
			<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

			<style>
				/* Estilos personalizados para el widget de caja */
				.widget-content {
					margin-top: 1.5rem;
				}

				.card {
					border: none;
					border-radius: 10px;
					box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
				}

				.task-header {
					padding: 1rem;
					background-color: #f8f9fa;
					border-radius: 5px;
					transition: background-color 0.3s ease;
				}

				.task-header:hover {
					background-color: #e9ecef;
				}

				.form-label {
					font-weight: 600;
					color: #6c757d;
				}

				.task-header p {
					font-size: 1rem;
					margin: 0;
					color: #343a40;
				}

				/* Ajustes para dispositivos pequeños */
				@media (max-width: 768px) {
					.task-header {
						margin-bottom: 1rem;
					}
				}
			</style>

			<div class="widget-content mt-3">
				<div class="card">
					<div class="card-body">
						<div class="row g-3">
							<div class="col-md-3">
								<div class="task-header">
									<div class="form-group">
										<label class="form-label">Fecha hora inicio turno</label>
										<p>{{ $caja->fecha_hora_inicio }}</p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="task-header">
									<div class="form-group">
										<label class="form-label">Turno</label>
										<p>{{ $caja->id }}</p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="task-header">
									<div class="form-group">
										<label class="form-label">Centro de costo</label>
										<p>{{ $caja->namecentrocosto }}</p>
									</div>
								</div>
							</div>
							<!-- <div class="col-md-3">
								<div class="task-header">
									<div class="form-group">
										<label class="form-label">Fecha hora cierre turno</label>
										<p>{{ $caja->fecha_hora_cierre }}</p>
									</div>
								</div>
							</div> -->
							<div class="col-md-3">
								<div class="task-header">
									<div class="form-group">
										<label class="form-label">Cajero</label>
										<p>{{ $caja->namecajero }}</p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="task-header">
									<div class="form-group">
										<label class="form-label">Estado</label>
										<p>{{ $caja->estado }}</p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="task-header">
									<div class="form-group">
										<label class="form-label">N° Facturas</label>
										<p>{{ $caja->cantidad_facturas }}</p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="task-header">
									<div class="form-group">
										<label class="form-label">Factura Inicial</label>
										<p>{{ $caja->factura_inicial }}</p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="task-header">
									<div class="form-group">
										<label class="form-label">Factura Final</label>
										<p>{{ $caja->factura_final }}</p>
									</div>
								</div>
							</div>
						</div><!-- /.row -->
					</div>
				</div>
			</div>


			<div class="row justify-content-center mt-2">
				<div class="col-md-6">
					<div class="widget widget-chart-one">
						<div class="widget-content mt-0">
							<div class="card-body">
								<form action="" method="POST" enctype="multipart/form-data">
									@csrf
									<input type="hidden" id="ventaId" name="ventaId" value="{{ $caja->id }}">
									<div class="table-responsive">
										<table class="table">
											<thead>
												<tr>
													<th scope="col" style="text-align: center; vertical-align: middle;">CONCEPTO</th>
													<th scope="col" style="text-align: center; vertical-align: middle;">VALOR</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<th scope="row" style="text-align: left">Medios electrónicos</th>
													<td>
														<input type="text" id="valor_a_pagar_tarjeta" name="valor_a_pagar_tarjeta"
															value="$ {{ number_format($arrayTotales['valorApagarTarjeta'], 0, ',', '.') }}"
															style="text-align: right; font-weight: bold; color: black" readonly>
													</td>
												</tr>
												<tr>
													<th scope="row" style="text-align: left">Otros</th>
													<td>
														<input type="text" id="valor_a_pagar_otros" name="valor_a_pagar_otros"
															value="$ {{ number_format($arrayTotales['valorApagarOtros'], 0, ',', '.') }}"
															style="text-align: right; font-weight: bold; color: black" readonly>
													</td>
												</tr>
												<tr>
													<th scope="row" style="text-align: left">Crédito</th>
													<td>
														<input type="text" id="valor_a_pagar_credito" name="valor_a_pagar_credito"
															value="$ {{ number_format($arrayTotales['valorApagarCredito'], 0, ',', '.') }}"
															style="text-align: right; font-weight: bold; color: black" readonly>
													</td>
												</tr>
												<tr>
													<th scope="row" style="text-align: left">Total</th>
													<td>
														<input type="text" name="valorTotal"
															value="$ {{ number_format($arrayTotales['valorTotal'], 0, ',', '.') }}"
															style="text-align: right; font-weight: bold; color: black" readonly>
													</td>
												</tr>
											</tbody>
										</table>
									</div><!-- /.table-responsive -->
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-6">
					<div class="widget widget-chart-one">
						<div class="widget-content mt-0">
							<div class="card-body">
								<input type="hidden" id="ventaId" name="ventaId" value="{{ $caja->id }}">
								@csrf
								<div class="table-responsive">
									<table class="table">
										<thead>
											<tr>
												<th scope="col" style="text-align: center; vertical-align: middle;">CONCEPTO</th>
												<th scope="col" style="text-align: center; vertical-align: middle;">VALOR</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<th scope="row" style="text-align: left">Base inicial</th>
												<td>
													<input type="text" id="base" name="base"
														value="$ {{ number_format($caja->base, 0, ',', '.') }}"
														style="text-align: right; font-weight: bold; color: black" readonly>
												</td>
											</tr>
											<tr>
												<th scope="row" style="text-align: left">Efectivo</th>
												<td>
													<input type="text" id="efectivo" name="efectivo"
														value="$ {{ number_format($arrayTotales['valorEfectivo'], 0, ',', '.') }}"
														style="text-align: right; font-weight: bold; color: black" readonly>
												</td>
											</tr>
											<tr>
												<th scope="row" style="text-align: left">Salida de caja</th>
												<td>
													<input type="text" id="salidaefectivo" name="salidaefectivo"
														value=" {{ number_format($arrayTotales['valorTotalSalidaEfectivo'], 0, ',', '.') }}"
														style="text-align: right; font-weight: bold; color: black" readonly>
												</td>
											</tr>
											<tr>
												<th scope="row" style="text-align: left">Total</th>
												<td>
													<input type="text" id="total" name="total"
														value=""
														style="text-align: right; font-weight: bold; color: black" readonly>
												</td>
											</tr>
											<tr>
												<th scope="row" style="text-align: left">Valor real ingresado</th>
												<td>
													<input type="text" id="valor_real" name="valor_real"
														value=""
														style="text-align: right; font-weight: bold; color: black">
												</td>
											</tr>
											<tr>
												<th scope="row" style="text-align: left">Diferencia</th>
												<td>
													<input type="text" id="diferencia" name="diferencia"
														value=""
														style="text-align: right; font-weight: bold; color: black" readonly>
												</td>
											</tr>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="2">
													<div class="form-group">
														@if($caja->status == 0)
														<button type="submit" class="btn btn-success btn-block" id="btnGuardar" disabled>Guardar e imprimir</button>
														@endif
														<button type="button" class="btn btn-primary btn-block" onclick="history.back()">Volver</button>
													</div>
												</th>
											</tr>
										</tfoot>
									</table>
								</div><!-- /.table-responsive -->
								</form>
							</div>
						</div>
					</div>
				</div>
			</div><!-- /.row justify-content-center -->
		</div><!-- /.widget -->
	</div><!-- /.col-sm-12 -->
</div><!-- /.row -->

@endsection

@section('script')
<script src="{{ asset('rogercode/js/caja/rogercode-create.js') }}" type="module"></script>
<script src="{{ asset('rogercode/js/caja/code-formulas.js') }}"></script>
@endsection
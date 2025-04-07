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

			<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

			<div class="widget-content mt-3">
				<div class="card">
					<div class="card-body">
						<div class="row g-3">
							<div class="col-md-3">
								<div class="task-header">
									<div class="form-group">
										<label class="form-label">Fecha hora inicio turno</label>
										<p>{{ $caja->fecha_hora_inicio ? $caja->fecha_hora_inicio->format('d/m/Y H:i') : 'N/A' }}</p>
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
													<th scope="col">CONCEPTO</th>
													<th scope="col">VALOR</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<th style="text-align: left">Medios electrónicos</th>
													<td>
														<input type="text" id="valor_a_pagar_tarjeta" name="valor_a_pagar_tarjeta"
															value="$ {{ number_format($arrayTotales['valorApagarTarjeta'], 0, ',', '.') }}"
															style="text-align: right; font-weight: bold;" readonly>
													</td>
												</tr>
												<tr>
													<th style="text-align: left">Otros</th>
													<td>
														<input type="text" id="valor_a_pagar_otros" name="valor_a_pagar_otros"
															value="$ {{ number_format($arrayTotales['valorApagarOtros'], 0, ',', '.') }}"
															style="text-align: right; font-weight: bold;" readonly>
													</td>
												</tr>
												<tr>
													<th style="text-align: left">Crédito</th>
													<td>
														<input type="text" id="valor_a_pagar_credito" name="valor_a_pagar_credito"
															value="$ {{ number_format($arrayTotales['valorApagarCredito'], 0, ',', '.') }}"
															style="text-align: right; font-weight: bold;" readonly>
													</td>
												</tr>
												<tr>
													<th style="text-align: left">Total</th>
													<td>
														<input type="text" name="valorTotal"
															value="$ {{ number_format($arrayTotales['valorTotal'], 0, ',', '.') }}"
															style="text-align: right; font-weight: bold;" readonly>
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
												<th scope="col">CONCEPTO</th>
												<th scope="col">VALOR</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<th style="text-align: left">Base inicial</th>
												<td>
													<input type="text" id="base" name="base"
														value="$ {{ number_format($caja->base, 0, ',', '.') }}"
														style="text-align: right; font-weight: bold;" readonly>
												</td>
											</tr>
											<tr>
												<th style="text-align: left">Efectivo</th>
												<td>
													<input type="text" id="efectivo" name="efectivo"
														value="$ {{ number_format($arrayTotales['valorEfectivo'], 0, ',', '.') }}"
														style="text-align: right; font-weight: bold;" readonly>
												</td>
											</tr>
											<tr>
												<th style="text-align: left">Retiro de caja</th>
												<td>
													<input type="number" id="retiro_caja" name="retiro_caja" class="form-control" value="0">
												</td>
											</tr>
											<tr>
												<th style="text-align: left">Total</th>
												<td>
													<input type="text" id="total" name="total"
														value="$ {{ number_format($caja->total ?? 0, 0, ',', '.') }}"
														style="text-align: right; font-weight: bold;" readonly>
												</td>
											</tr>
											<tr>
												<th style="text-align: left">Valor real ingresado</th>
												<td>
													<input type="number" step="0.01" id="valor_real" name="valor_real"
														value=""
														style="text-align: right; font-weight: bold;">
												</td>
											</tr>
											<tr>
												<th style="text-align: left">Diferencia</th>
												<td>
													<input type="text" id="diferencia" name="diferencia"
														value=""
														style="text-align: right; font-weight: bold;" readonly>
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

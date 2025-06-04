<div class="card">
	<div class="card-body">
		<div>
			<input type="hidden" value="0" name="alistamientoId" id="alistamientoId">
		</div>
		<div class="row g-3">
			<div class="col-md-6">
				<div class="task-header">
					<div class="form-group">
						<label for="date1" class="form-label">Fecha</label>
						<input type="date" class="form-control" name="fecha" id="fecha" placeholder="Last name" aria-label="Last name">
						<span class="text-danger error-message"></span>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="task-header">
					<label for="inputstore" class="form-label">Bodega</label>
					<select id="inputstore" name="inputstore" class="form-select select2">
						<option value="">Todas las bodegas</option>
						@foreach($stores as $option)
						<option value="{{ $option['id'] }}" data="{{ $option }}">{{ $option['name'] }}</option>
						@endforeach
					</select>
					<span class="text-danger error-message"></span>
				</div>
			</div>
			<div class="col-md-10">
				<div class="task-header">
					<div class="form-group">
						<label for="producto" class="form-label">Buscar producto</label>

						<!-- Campos ocultos para enviar datos adicionales -->
						<input type="hidden" id="lote_id" name="lote_id" value="">
						<input type="hidden" id="product_id" name="product_id" value="">
						<input type="hidden" id="inventario_id" name="inventario_id" value="">
						<input type="hidden" id="stock_ideal" name="stock_ideal" value="">

						<select class="form-control form-control-sm select2Prod" name="producto" id="producto" required>
							<option value="">Seleccione el producto</option>
							@foreach ($results as $result)
							<option value="{{ $result['inventario_id'] }}"
								data-product-id="{{ $result['product_id'] }}"
								data-lote-id="{{ $result['lote_id'] }}"
								data-inventario-id="{{ $result['inventario_id'] }}"
								data-stock-ideal="{{ $result['stock_ideal'] }}"
								data-info="{{ $result['text'] }}">
								{{ $result['text'] }}
							</option>
							@endforeach
						</select>

						<span class="text-danger error-message"></span>
					</div>
				</div>
			</div>

			<div class="col-md-2">
				<div class="task-header">
					<div class="form-group">
						<label for="sell_price" class="form-label mt-0">QT a convertir</label>
						<div class="input-group flex-nowrap">
							<input type="text" name="cantidadprocesar" id="cantidadprocesar" class="form-control" value="0" placeholder="0" aria-describedby="helpId" step="0.01">
							<span class="input-group-text" id="addon-wrapping">QT</span>
						</div>
						<span class="text-danger error-message"></span>
					</div>
				</div>
			</div>
		</div>
	</div>
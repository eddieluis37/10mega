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
			<div class="col-md-4">
				<div class="task-header">
					<div class="form-group">
						<label for="inputlote" class="form-label mt-3">Lote</label>
						<select id="inputlote" name="inputlote" class="form-select select2">
							<option value="">Seleccione un lote</option>
						</select>
						<span class="text-danger error-message"></span>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="task-header">
					<div class="form-group">
						<label for="select2corte" class="form-label mt-3">Selecciona producto</label>
						<select id="select2corte" name="select2corte" class="form-select select2">
							<option value="">Seleccione un producto</option>
						</select>
						<span class="text-danger error-message"></span>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="task-header">
					<div class="form-group">
						<label for="sell_price" class="form-label mt-3">QT a convertir</label>
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
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">Crear Alistamiento</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
		</div>
		<div class="modal-body">

			<div>
				<label for="date1" class="form-label">Fecha</label>
				<input type="date" class="form-control" name="fecha" id="fecha" placeholder="Last name" aria-label="Last name">
				<span class="text-danger error-message"></span>
			</div>
			<!-- Bodega -->
			<label for="inputstore" class="form-label">Bodega</label>
			<select id="inputstore" class="form-select select2">
				<option value="">Todas las bodegas</option>
				@foreach($stores as $option)
				<option value="{{ $option['id'] }}" data="{{ $option }}">{{ $option['name'] }}</option>
				@endforeach
			</select>
			<span class="text-danger error-message"></span>

			<!-- Lote -->
			<label for="inputlote" class="form-label mt-3">Lote</label>
			<select id="inputlote" class="form-select select2">
				<option value="">Seleccione un lote</option>
			</select>
			<span class="text-danger error-message"></span>

			<!-- Productos -->
			<label for="select2corte" class="form-label mt-3">Seleccionar corte padre</label>
			<select id="select2corte" class="form-select select2">
				<option value="">Seleccione un producto</option>
			</select>
			<span class="text-danger error-message"></span>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
			<button type="button" class="btn btn-primary">Guardar</button>
		</div>
	</div>
</div>
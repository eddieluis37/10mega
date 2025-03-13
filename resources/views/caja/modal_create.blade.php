<div class="card">
	<div class="card-body">
		<div>
			<input type="hidden" value="0" name="alistamientoId" id="alistamientoId">
		</div>
		<div class="row g-3">

			<div class="col-md-4">
				<div class="task-header">
					<div class="form-group">
						<label for="centrocosto" class="form-label">Centro costo</label>
						<select class="form-control form-control-sm input" name="centrocosto" id="centrocosto" required>
							<option value="">Seleccione el centro de costo</option>
							@foreach($centroCostoUser as $cencosto)
							<option value="{{ $cencosto->id }}" {{ $cencosto->id == $defaultCentroCostoId ? 'selected' : '' }}>
								{{ $cencosto->name }}
							</option>
							@endforeach
						</select>
						<span class="text-danger error-message"></span>
					</div>
				</div>
			</div>

			<div class="col-md-4">
				<div class="task-header">
					<div class="form-group">
						<label for="" class="form-label">Cajero</label>
						<select class="form-control form-control-sm input " name="cajero" id="cajero" required>
							<option value="">Seleccione el cajero</option>
							@foreach($usuario as $option)
							<option value="{{ $option['id'] }}" data="{{$option}}">{{ $option['name'] }}</option>
							@endforeach
						</select>
						<span class="text-danger error-message"></span>
					</div>
				</div>
			</div>

			<div class="col-sm-6 col-md-4">
				<div class="task-header">
					<div class="form-group">
						<label for="" class="form-label">Base inicial</label>
						<div class="input-group flex-nowrap">
							<span class="input-group-text" id="addon-wrapping">$</span>
							<input type="text" name="base" id="base" class="form-control" "aria-describedby=" helpId" placeholder="0" required="" min="1" step="1">
						</div>
						<span class="text-danger error-message"></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	document.addEventListener("DOMContentLoaded", function() {
		const costoInput = document.getElementById("base");

		// Función para formatear el número con puntos
		function formatCurrency(value) {
			return value
				.replace(/\D/g, "") // Elimina caracteres que no sean dígitos
				.replace(/\B(?=(\d{3})+(?!\d))/g, "."); // Agrega puntos como separadores de miles
		}

		costoInput.addEventListener("input", function(e) {
			const value = e.target.value;
			e.target.value = formatCurrency(value);
		});

		costoInput.addEventListener("blur", function(e) {
			// Opcional: Agrega un "0" si el campo está vacío al salir
			if (!e.target.value) {
				e.target.value = "0";
			}
		});
	});
</script>
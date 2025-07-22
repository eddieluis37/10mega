<div class="card">
	<div class="card-body">
		<div>
			<input type="hidden" value="0" name="compensadoId" id="compensadoId">
		</div>
		<div class="row g-3">
			<div class="col-md-4">
				<div class="task-header">
					<div class="form-group">
						<label for="" class="form-label">Proveedor</label>
						<select class="form-control form-control-sm select2Provider " name="provider" id="provider" required>
							<option value="">Seleccione el proveedor</option>
							@foreach($providers as $option)
							<option value="{{ $option['id'] }}" data="{{$option}}">{{ $option['name'] }}</option>
							@endforeach
						</select>
						<span class="text-danger error-message"></span>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="task-header">
					<div class="form-group">
						<label for="" class="form-label">Bodega</label>
						<select class="form-control form-control-sm select2Store " name="store" id="store" required>
							<option value="">Seleccione la bodega</option>
							@foreach($bodegas as $option)
							<option value="{{ $option['id'] }}" data="{{$option}}">{{ $option['name'] }}</option>
							@endforeach
						</select>
						<span class="text-danger error-message"></span>
					</div>
				</div>
			</div>
			<!-- <div class="col-md-4">
				<div class="task-header">
					<div class="form-group">
						<label for="store" class="form-label">Bodega</label>
						<select class="form-control form-control-sm input" name="store" id="store" required>
							<option value="">Seleccione la Bodega</option>
							@foreach($bodegas as $option)
							<option value="{{$option->id}}" {{ $option->id == 1 ? 'selected' : '' }}>{{$option->name}}</option>
							@endforeach
						</select>
						<span class="text-danger error-message"></span>
					</div>
				</div>
			</div> -->
			<div class="col-md-4">
				<div class="task-header">
					<div class="form-group">
						<label for="" class="form-label">Factura</label>
						<input type="text" class="form-control" id="factura" name="factura" required="true">
						<span class="text-danger error-message"></span>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="task-header">
					<div class="form-group">
						<label for="date1" class="form-label">Fecha orden de compra</label>
						<input type="date" class="form-control" name="fecha_compensado" id="fecha_compensado" placeholder="Last name" aria-label="Last name" value="{{date('Y-m-d')}}">
						<span class="text-danger error-message"></span>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="task-header">
					<div class="form-group">
						<label for="date1" class="form-label">Fecha ingreso</label>
						<input type="date" class="form-control" name="fecha_ingreso" id="fecha_ingreso" placeholder="Last name" aria-label="Last name" value="{{date('Y-m-d')}}">
						<span class="text-danger error-message"></span>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="form-group">
					<label for="observations">Observación general</label>
					<textarea class="form-control" id="observacion" name="observacion" rows="3"></textarea>
				</div>
			</div>
		</div>
	</div>
</div>
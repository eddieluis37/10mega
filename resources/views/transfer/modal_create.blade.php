<div class="card">
  <div class="card-body">
    <div>
      <input type="hidden" value="0" name="transferId" id="transferId">
    </div>
    <div class="row g-3 justify-content-center">
      <div class="col">
        <div class="task-header">
          <div class="form-group">
            <label for="bodegaOrigen" class="form-label">Bodega origen</label>
            <select class="form-control form-control-sm input select2" name="bodegaOrigen" id="bodegaOrigen" required>
              <option value="">Seleccione el centro de costo</option>
              @foreach($bodegaOrigen as $option)
              <option value="{{ $option->id }}">{{ $option->name }}</option>
              @endforeach
            </select>
            <span class="text-danger error-message"></span>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="task-header">
          <div class="form-group">
            <label for="bodegaDestino" class="form-label">Bodega destino</label>
            <select class="form-control form-control-sm input select2" name="bodegaDestino" id="bodegaDestino" required>
              <option value="">Seleccione el centro de costo</option>
              @foreach($stores as $option)
              <option value="{{ $option->id }}">{{ $option->name }}</option>
              @endforeach
            </select>
            <span class="text-danger error-message"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

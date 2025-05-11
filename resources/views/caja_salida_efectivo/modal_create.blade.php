  <style>
      /* Aplica a todos los label dentro del formulario con id #formSalidaEfectivo */
      #formSalidaEfectivo label {
          color: #fff !important;
      }
  </style>
  <div class="row mb-3 align-items-center">
      <div class="col-md-6">

          <label for="vr_efectivo" class="form-label text-white fw-bold">VALOR</label>
      </div>
      <div class="col-md-6">
          <div class="task-header">
              <div class="form-group">
                  <div class="input-group">
                      <span class="input-group-text">$</span>
                      <input type="text" class="form-control" id="vr_efectivo" name="vr_efectivo" placeholder="0" required>
                  </div>
                  <span class="text-danger error-message"></span>
              </div>
          </div>
      </div>
  </div>
  <div class="row mb-3">
      <div class="col-12">
          <div class="task-header">
              <div class="form-group">
                  <div class="form-floating">
                      <label for="concepto" class=" text-white fw-bold">CONCEPTO</label>
                      <textarea
                          class="form-control"
                          placeholder="Descripción detallada del concepto"
                          id="concepto"
                          name="concepto"
                          style="height: 150px"
                          required></textarea>
                  </div>
                  <span class="text-danger error-message"></span>
              </div>
          </div>
      </div>
  </div>
  <div class="row mb-3">
      <div class="col-md-12">
          <div class="task-header">
              <div class="form-group">
                  <label for="third_id" class="form-label text-white fw-bold">RECIBE</label>
                  <select class="form-select selectTercero" id="third_id" name="third_id" required>
                      <option value="">Buscar un tercero...</option>
                      @foreach ($terceros as $p)
                      <option value="{{ $p->id }}">{{ $p->name }}</option>
                      @endforeach
                  </select>
                  <span class="text-danger error-message"></span>
              </div>
          </div>
      </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
      $(document).ready(function() {
          // Limpiar mensajes de error al cerrar el modal
          $('#modal-create-salida').on('hidden.bs.modal', function() {
              $(this).find('.error-message').text(''); // Limpiar mensaje de error
              $('#productoId').val(0); // Para evitar que al crear nuevo producto se edite el registro anterior editado
              $('#vr_efectivo').val(''); // Opcional: limpiar la selección del campo
              $('#concepto').val('');
              $('#third_id').val('');

          });

          // Limpiar mensajes de error al seleccionar un campo
          $('#vr_efectivo').change(function() {
              $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
          });
          $('#concepto').change(function() {
              $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
          });
          $('#third_id').change(function() {
              $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
          });          

      });
  </script>
  <script>
      document.addEventListener("DOMContentLoaded", function() {
          // Array con los IDs de los campos que requieren el formateo
          const inputIds = [
              "vr_efectivo",
          ];

          // Función para formatear el número con puntos como separadores de miles
          function formatCurrency(value) {
              return value
                  .replace(/\D/g, "") // Elimina cualquier caracter que no sea dígito
                  .replace(/\B(?=(\d{3})+(?!\d))/g, "."); // Agrega puntos cada tres dígitos
          }

          // Función para asignar los event listeners a cada campo del array
          inputIds.forEach(id => {
              const inputElement = document.getElementById(id);
              if (inputElement) {
                  // Al ingresar datos se aplica el formateo en tiempo real
                  inputElement.addEventListener("input", function(e) {
                      e.target.value = formatCurrency(e.target.value);
                  });
                  // Al salir del campo, si está vacío se asigna "0"
                  inputElement.addEventListener("blur", function(e) {
                      if (!e.target.value) {
                          e.target.value = "0";
                      }
                  });
              }
          });

      });
  </script>
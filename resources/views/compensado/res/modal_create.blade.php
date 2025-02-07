<div class="row">
    <div class="col-sm-12">
        <div class="connect-sorting-content">
            <div class="card simple-title-task ui-sortable-handle">
                <div class="card-body">
                    <div class="btn-toolbar justify-content-between">
                        <div>
                            <input type="hidden" value="0" name="loteId" id="loteId">
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>Lote</label>
                                    <input type="text" class="form-control" name="lote" id="lote" placeholder="ej: 021124T1" required>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="task-header">
                                <div class="form-group">
                                    <label for="date1" class="form-label">Fecha vencimiento</label>
                                    <input type="date" class="form-control" name="fecha_vencimiento" id="fecha_vencimiento" placeholder="Last name" aria-label="Last name" value="{{date('Y-m-d')}}">
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Limpiar mensajes de error al cerrar el modal
        $('#modal-create-lote').on('hidden.bs.modal', function() {
            $(this).find('.error-message').text(''); // Limpiar mensaje de error
            $('#loteId').val(0); // Para evitar que al crear nuevo producto se edite el registro anterior editado
        
            $('#lote').val('');
            $('#codigobarra').val('');
            $('#stockalerta').val('');
            $('#impuestoiva').val('');
            $('#isa').val('');
        });

        // Limpiar mensajes de error al seleccionar un campo
        $('#categoria').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#familia').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#nivel').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#subfamilia').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#lote').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#codigobarra').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#marca').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#stockalerta').change(function() {
            $(this).siblings('.error-message').text(''); // Clear error message on blur
        });
        $('#impuestoiva').change(function() {
            $(this).siblings('.error-message').text(''); // Clear error message on blur
        });
        $('#isa').change(function() {
            $(this).siblings('.error-message').text(''); // Clear error message on blur
        });
    });
</script>
<!-- <script>
    function updatePercentage() {
        const ivaInput = document.getElementById('impuestoiva').value;
        const percentageDisplay = document.getElementById('percentageDisplay');

        // Verifica si el input no está vacío
        if (ivaInput) {
            percentageDisplay.textContent = ivaInput + '%'; // Muestra el valor seguido del símbolo de porcentaje
        } else {
            percentageDisplay.textContent = ''; // Limpia el display si no hay valor
        }
    }
</script> -->
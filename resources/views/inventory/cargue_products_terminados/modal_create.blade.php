<div class="row">
    <div class="col-sm-12">
        <div class="connect-sorting-content">
            <div class="card simple-title-task ui-sortable-handle">
                <div class="card-body">
                    <div class="btn-toolbar justify-content-between">
                        <div>
                            <input type="hidden" value="0" name="productoId" id="productoId">
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>Categoria</label>
                                    <div>
                                        <select class="form-control selectCategory" name="categoria" id="categoria" required="">
                                            <option value="">Seleccione la categoria</option>
                                          
                                        </select>
                                        <span class="text-danger error-message"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>Marca</label>
                                    <select class="form-control selectMarca" name="marca" id="marca" required="">
                                       
                                    </select>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>Nivel</label>
                                    
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>Presentacion</label>
                                    
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>Familia</label>
                                   
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>Subfamilia</label>
                                    <input type="text" class="form-control" name="subfamilia" id="subfamilia" placeholder="ej: Chorizo" required="">
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>Código</label>
                                    <input type="text" class="form-control" name="code" id="code" placeholder="ej: RE001" required>
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
        $('#modal-create-producto').on('hidden.bs.modal', function() {
            $(this).find('.error-message').text(''); // Limpiar mensaje de error
            $('#productoId').val(0); // Para evitar que al crear nuevo producto se edite el registro anterior editado
            $('#categoria').val(''); // Opcional: limpiar la selección del campo
            $('#selectMarca').val('');
            $('#marca').val('');
            $('#nivel').val('');
            $('#familia').val('');
            $('#subfamilia').val('');
            $('#code').val('');
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
        $('#code').change(function() {
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
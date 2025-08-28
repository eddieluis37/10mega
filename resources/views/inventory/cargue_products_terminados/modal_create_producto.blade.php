<div class="row">
    <div class="col-sm-12">
        <div class="connect-sorting-content">
            <div class="card simple-title-task ui-sortable-handle">
                <div class="card-body">
                    <div class="btn-toolbar justify-content-between">
                        <div>
                            <input type="hidden" value="0" name="productloteId" id="productloteId">
                            <input type="hidden" value="1" name="store_id" id="store_id">
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="task-header">
                                <div class="form-group">
                                    <label for="" class="form-label">Buscar bodega</label>
                                    <select class="form-control form-control-sm select2Store" name="bodega" id="bodega" required="">
                                        <option value="">Seleccione la bodega</option>
                                        @foreach ($bodegas as $p)
                                        <option value="{{$p->id}}">{{$p->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="task-header">
                                <div class="form-group">
                                    <label for="loteProd" class="form-label">Buscar lote</label>
                                    <select class="form-control form-control-sm select2Lote" name="loteProd" id="loteProd" required="">
                                        <option value="">Seleccione el lote</option>
                                        @foreach($lote as $option)
                                        <option value="{{ $option['id'] }}" data="{{ $option }}">{{ $option['codigo'] }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="task-header">
                                <div class="form-group">
                                    <label for="" class="form-label">Buscar producto</label>
                                    <select class="form-control form-control-sm select2Prod" name="producto" id="producto" required="">
                                        <option value="">Seleccione el producto</option>
                                        @foreach ($prod as $p)
                                        <option value="{{$p->id}}">{{$p->name}} - Cod:{{$p->code}}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>    
                        <div class="col-sm-6 col-md-4">
                            
                        </div>                    
                        <div class="col-sm-6 col-md-4">
                            <div class="task-header">
                                <div class="form-group">
                                    <label for="" class="form-label">Cantidad</label>
                                    <div class="input-group flex-nowrap">
                                        <input type="text" name="quantity" id="quantity" class="form-control" aria-describedby="helpId" placeholder="0" step="0.01" required="">
                                        <span class="input-group-text" id="addon-wrapping">QT</span>
                                    </div>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="task-header">
                                <div class="form-group">
                                    <label for="" class="form-label">Costo</label>
                                    <div class="input-group flex-nowrap">
                                        <span class="input-group-text" id="addon-wrapping">$</span>
                                        <input type="text" name="costo" id="costo" class="form-control" "aria-describedby=" helpId" placeholder="0" required="" min="1" step="1">
                                    </div>
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
            $('#loteId').val(0); // Para evitar que al crear nuevo producto se edite el registro anterior editado

            $('#producto').val('').trigger('change'); // Limpiar el select2 y actualizar
            $('#bodega').val('').trigger('change'); // Limpiar el select2 y actualizar
            $('#loteProd').val('').trigger('change');
            $('#quantity').val('').trigger('change');

        });

        // Limpiar mensajes de error al seleccionar un campo
        $('#bodega').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#producto').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#loteProd').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const costoInput = document.getElementById("costo");

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
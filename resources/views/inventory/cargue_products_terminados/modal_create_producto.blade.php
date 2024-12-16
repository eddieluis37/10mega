<div class="row">
    <div class="col-sm-12">
        <div class="connect-sorting-content">
            <div class="card simple-title-task ui-sortable-handle">
                <div class="card-body">
                    <div class="btn-toolbar justify-content-between">
                        <div>
                            <input type="hidden" value="0" name="productloteId" id="productloteId">
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="task-header">
                                <div class="form-group">
                                    <label for="" class="form-label">Buscar producto</label>
                                    <select class="form-control form-control-sm select2Prod" name="producto" id="producto" required="">
                                        <option value="">Seleccione el producto</option>
                                        @foreach ($prod as $p)
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
                                        <option value="{{ $option['id'] }}" data="{{ $option }}">{{ $option['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="task-header">
                                <div class="form-group">
                                    <label for="" class="form-label">Cantidad</label>
                                    <div class="input-group flex-nowrap">
                                        <input type="text" name="quantity" id="quantity" class="form-control" aria-describedby="helpId" placeholder="0" step="0.01" required="">
                                        <span class="input-group-text" id="addon-wrapping"></span>
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
            $('#loteProd').val('').trigger('change');
            $('#quantity').val('').trigger('change');
       
        });

        // Limpiar mensajes de error al seleccionar un campo
        $('#producto').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#loteProd').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });       
    });
</script>
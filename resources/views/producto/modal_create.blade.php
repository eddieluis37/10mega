<div class="row">
    <div class="col-sm-12">
        <div class="connect-sorting-content">
            <div class="card simple-title-task ui-sortable-handle">
                <div class="card-body">
                    <div class="btn-toolbar justify-content-between">
                        <div>
                            <input type="hidden" value="0" name="productoId" id="productoId">
                        </div>
                        <!-- Toolbar inicial -->
                        <div class="col-sm-12 mb-3 d-flex flex-column flex-md-row justify-content-between">

                            <div class="mb-2 mb-md-0 w-30 me-md-2">
                                <label for="product_type" class="form-label">Tipo</label>
                                <select name="product_type" id="product_type" class="form-control">
                                    <option value="simple">Simple</option>
                                    <option value="combo">Combo</option>
                                    <option value="receta">Receta</option>
                                </select>
                            </div>

                            <div class="w-70">
                                <label for="product-selector">Agregar producto</label>
                                <select id="product-selector" class="form-control"></select>
                            </div>
                        </div>

                        <!-- Tabla de productos -->
                        <div class="table-responsive mb-4">
                            <table class="table" id="product-table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <!-- Acordeón: Campos adicionales dependiendo del tipo de producto -->
                        <div id="simpleFields" class="product-type-fields hidden mt-4">
                            <h4>Datos de producto simple</h4>

                        </div>


                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>Categoria ERP</label>
                                    <div>
                                        <select class="form-control selectCategory" name="categoria" id="categoria" required="">
                                            <option value="">Seleccione la categoria</option>
                                            @foreach ($categorias as $c)
                                            <option value="{{$c->id}}" {{ $c->id == 1 ? 'selected' : '' }}>{{$c->name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-message"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>Categoria WEB</label>
                                    <div>
                                        <select class="form-control selectCategory" name="categoria" id="categoria" required="">
                                            <option value="">Seleccione</option>
                                            @foreach ($categoriasComerciales as $c)
                                            <option value="{{$c->id}}" {{ $c->id == 1 ? 'selected' : '' }}>{{$c->name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-message"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>SubCategoria WEB</label>
                                    <div>
                                        <select class="form-control selectCategory" name="categoria" id="categoria" required="">
                                            <option value="">Seleccione</option>
                                            @foreach ($SubcategoriasComerciales as $c)
                                            <option value="{{$c->id}}" {{ $c->id == 1 ? 'selected' : '' }}>{{$c->name}}</option>
                                            @endforeach
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
                                        <option value="">Buscar un proveedor</option>
                                        @foreach ($brandsThirds as $p)
                                        <option value="{{$p->id}}">{{$p->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>Nivel</label>
                                    <select class="form-control selectPieles" name="nivel" id="nivel">
                                        <option value="2">No aplica</option>
                                        @foreach ($niveles as $p)
                                        <option value="{{$p->id}}">{{$p->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>Presentación</label>
                                    <select class="form-control selectVisceras" name="presentacion" id="presentacion" required="">
                                        <option value="">Buscar una presentacion</option>
                                        @foreach ($presentaciones as $p)
                                        <option value="{{$p->id}}">{{$p->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>Cantidad</label>
                                    <input type="text" class="form-control" name="quantity" id="quantity" placeholder="ej: 1" required>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>Familia</label>
                                    <select class="form-control selectProvider" name="familia" id="familia" required="">
                                        <option value="">Buscar una familia</option>
                                        @foreach ($familias as $p)
                                        <option value="{{$p->id}}">{{$p->name}}</option>
                                        @endforeach
                                    </select>
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
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>Código de Barras</label>
                                    <input type="text" class="form-control" name="codigobarra" id="codigobarra" placeholder="ej: 777666999222333" required>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>%IVA</label>
                                    <div>
                                        <select class="form-control form-control-sm" name="impuestoiva" id="impuestoiva" required="">
                                            <option value="">Seleccione</option>
                                            <option value="0">0%</option>
                                            <option value="5">5%</option>
                                            <option value="19">19%</option>
                                        </select>
                                        <span class="text-danger error-message"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>%Ultraproceso</label>
                                    <div>
                                        <select class="form-control form-control-sm" name="isa" id="isa" required="">
                                            <option value="">Seleccione</option>
                                            <option value="0">0%</option>
                                            <option value="20">20%</option>
                                        </select>
                                        <span class="text-danger error-message"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>ImpoConsumo</label>
                                    <div>
                                        <select class="form-control form-control-sm" name="impoconsumo" id="impoconsumo" required="">
                                            <option value="">Seleccione</option>
                                            <option value="0">0%</option>
                                            <option value="5">5%</option>
                                            <option value="8">8%</option>
                                            <option value="19">19%</option>
                                            <option value="20">20%</option>
                                        </select>
                                        <span class="text-danger error-message"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="mb-3">
                                <label for="formFile" class="form-label">Seleccione la imagen</label>
                                <input class="form-control" type="file" id="formFile">
                                <span class="text-danger error-message"></span>
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
            $('#presentacion').val('');
            $('#quantity').val('');
            $('#familia').val('');
            $('#subfamilia').val('');
            $('#code').val('');
            $('#codigobarra').val('');
            $('#impuestoiva').val('');
            $('#isa').val('');
            $('#impoconsumo').val('');
            $('#product-table').val('');
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
        $('#impoconsumo').change(function() {
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
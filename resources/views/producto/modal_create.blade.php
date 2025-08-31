<div class="row">
    <div class="col-sm-12">
        <div class="connect-sorting-content">
            <div class="card simple-title-task ui-sortable-handle">
                <div class="card-body">
                    <div class="btn-toolbar justify-content-between">
                        <div>
                            <input type="hidden" value="0" name="productoId" id="productoId">
                        </div>

                        <div class="mb-2 mb-md-0 w-30 me-md-2">
                            <label for="product_type" class="form-label">Tipo</label>
                            <select name="product_type" id="product_type" class="form-control">
                                <option value="simple">Simple</option>
                                <option value="combo">Combo</option>
                                <option value="receta">Receta</option>
                            </select>
                        </div>

                        <!-- Bloque único para combo y receta -->
                        <div id="combo_receta_fields" class="product-type-fields hidden mt-4 col-sm-12">
                            <h4 id="combo_receta_title">Datos de producto</h4>

                            <div class="col-sm-12 mb-3 d-flex flex-column flex-md-row justify-content-between">
                                <div class="w-70">
                                    <label for="product-selector">Agregar producto</label>
                                    <select id="product-selector" class="form-control"></select>
                                </div>
                            </div>

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
                                        <select class="form-control select2CategoryErp" name="categoriaerp" id="categoriaerp" required>
                                            <option value="">Seleccione la categoria</option>
                                            @foreach ($categorias as $option)
                                            <option value="{{ $option['id'] }}" data="{{$option}}">{{ $option['name'] }}</option>
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
                                    <label>SubCategoria ERP</label>
                                    <div>
                                        <select class="form-control select2SubCategoryErp" name="subcategoriaerp" id="subcategoriaerp" required>
                                            <option value="">Seleccione</option>
                                            @foreach ($familias as $option)
                                            <option value="{{ $option['id'] }}" data="{{$option}}">{{ $option['name'] }}</option>
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
                                        <select class="form-control select2CategoryWeb" name="categoriaweb" id="categoriaweb" required>
                                            <option value="">Seleccione</option>
                                            @foreach ($categoriasComerciales as $option)
                                            <option value="{{ $option['id'] }}" data="{{$option}}">{{ $option['name'] }}</option>
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
                                    <label for="" class="form-label">SubCategoria WEB</label>
                                    <select class="form-control select2SubCategoryWeb" name="subcategoriaweb" id="subcategoriaweb" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($SubcategoriasComerciales as $option)
                                        <option value="{{ $option['id'] }}" data="{{$option}}">{{ $option['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label for="" class="form-label">Marca</label>
                                    <select class="form-control select2Marca" name="marca" id="marca" required>
                                        <option value="">Buscar un proveedor</option>
                                        @foreach ($brandsThirds as $option)
                                        <option value="{{ $option['id'] }}" data="{{$option}}">{{ $option['name'] }}</option>
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
                                    <select class="form-control" name="nivel" id="nivel">
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
                                    <label>TipoInsumo</label>
                                     <select class="form-control form-control" name="tipoinsumo" id="tipoinsumo">
                                            <option value="">Seleccione</option>
                                            <option value="1">CARNICO</option>
                                            <option value="2">NO CARNICO</option>
                                            <option value="3">VEGETAL</option>
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
                        <!--  <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>Cantidad</label>
                                    <input type="text" class="form-control" name="quantity" id="quantity" placeholder="ej: 1" required>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
 -->
                        <div class="col-sm-12 col-md-3">
                            <div class="task-header">
                                <div class="form-group">
                                    <label>NombreProducto</label>
                                    <input type="text" class="form-control" name="nameproducto" id="nameproducto" placeholder="ej: Chorizo" required>
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
                        <div class="col-sm-12 col-md-12">
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

            const mySelectCategoriaerp = $("#categoriaerp");
            mySelectCategoriaerp.val("").trigger("change");

            const mySelectSubcategoriaerp = $("#subcategoriaerp");
            mySelectSubcategoriaerp.val("").trigger("change");

            const mySelectCategoriaweb = $("#categoriaweb");
            mySelectCategoriaweb.val("").trigger("change");

            const mySelectSubcategoriaweb = $("#subcategoriaweb");
            mySelectSubcategoriaweb.val("").trigger("change");

            const mySelectMarca = $("#marca");
            mySelectMarca.val("").trigger("change");

            $('#nivel').val('');
            $('#presentacion').val('');
            $('#quantity').val('');
            $('#subfamilia').val('');
            $('#code').val('');
            $('#codigobarra').val('');
            $('#impuestoiva').val('');
            $('#isa').val('');
            $('#impoconsumo').val('');
            $('#product-table').val('');
        });

        // Limpiar mensajes de error al seleccionar un campo
        $('#categoriaerp').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#subcategoriaerp').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#categoriaweb').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#subcategoriaweb').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });

        $('#nivel').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#presentacion').change(function() {
            $(this).siblings('.error-message').text(''); // Limpiar mensaje de error
        });
        $('#nameproducto').change(function() {
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
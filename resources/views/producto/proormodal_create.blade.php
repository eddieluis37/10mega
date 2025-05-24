<style>
    .hidden {
        display: none;
    }
    .form-group label {
        font-weight: bold;
    }
</style>

<!-- Usa contenedores Bootstrap para mantener una estructura responsive -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form>
                        <!-- Toolbar inicial -->
                        <div class="mb-3 d-flex flex-column flex-md-row justify-content-between">
                            <input type="hidden" value="0" name="productoId" id="productoId">

                            <div class="mb-2 mb-md-0 w-100 me-md-2">
                                <label for="product_type" class="form-label">Tipo de producto:</label>
                                <select name="product_type" id="product_type" class="form-control">
                                    <option value="simple">Simple</option>
                                    <option value="combo">Combo</option>
                                    <option value="receta">Receta</option>
                                </select>
                            </div>

                            <div class="w-100">
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

                        <!-- Campos de formulario organizados en filas -->
                        <div class="row g-3">
                            <!-- Repite este bloque col-md-4 por cada campo -->
                            <div class="col-sm-12 col-md-4">
                                <label for="categoria" class="form-label">Categoría ERP</label>
                                <select class="form-control" name="categoria" id="categoria">
                                    <option value="">Seleccione la categoría</option>
                                    @foreach ($categorias as $c)
                                    <option value="{{$c->id}}" {{ $c->id == 1 ? 'selected' : '' }}>{{$c->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Agrega aquí más campos similares adaptados al diseño de Bootstrap -->

                            <!-- Campo imagen -->
                            <div class="col-sm-12 col-md-4">
                                <label for="formFile" class="form-label">Seleccione la imagen</label>
                                <input class="form-control" type="file" id="formFile">
                            </div>
                        </div>

                        <!-- Acordeón: Campos adicionales dependiendo del tipo de producto -->
                        <div id="simpleFields" class="product-type-fields hidden mt-4">
                            <h4>Datos de producto simple</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label>Precio:</label>
                                    <input type="number" name="simple_price" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label>Stock:</label>
                                    <input type="number" name="simple_stock" class="form-control">
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

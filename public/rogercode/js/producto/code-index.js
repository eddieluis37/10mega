console.log("Starting");
const btnAddProducto = document.querySelector("#btnAddproducto");
const formProducto = document.querySelector("#form-producto");
const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
const btnClose = document.querySelector("#btnModalClose");

const selectCategory = document.querySelector("#categoria");

const selectCentrocosto = document.querySelector("#centrocosto");
const producto_id = document.querySelector("#productoId");
const contentform = document.querySelector("#contentDisable");

const fechaalistamiento = document.querySelector("#fecha");
const productosCombo = [];

$(document).ready(function () {
    $(function () {
        $("#tableProducto").DataTable({
            paging: true,
            pageLength: 5,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "/showproducto",
                type: "GET",
            },
            columns: [
                { data: "id", name: "id" },
                { data: "namecategorias", name: "namecategorias" },
                { data: "type", name: "type" },
                { data: "name", name: "name" },
                { data: "code", name: "code" },
                {
                    data: "price_fama",
                    name: "price_fama",
                    render: function (data, type, row) {
                        return "$" + formatCantidadSinCero(data);
                    },
                },
                {
                    data: "iva",
                    name: "iva",
                    render: function (data, type, row) {
                        return formatCantidadSinCero(data) + "%";
                    },
                },
                {
                    data: "otro_impuesto",
                    name: "otro_impuesto",
                    render: function (data, type, row) {
                        return formatCantidadSinCero(data) + "%";
                    },
                },
                {
                    data: "impoconsumo",
                    name: "impoconsumo",
                    render: function (data, type, row) {
                        return formatCantidadSinCero(data) + "%";
                    },
                },
                { data: "action", name: "action" },
            ],
            order: [[0, "DESC"]],
            language: {
                processing: "Procesando...",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                sInfo: "Mostrando del _START_ al _END_ de total _TOTAL_ registros",
                infoEmpty:
                    "Mostrando registros del 0 al 0 de un total de 0 registros",
                infoFiltered: "(filtrado de un total de _MAX_ registros)",
                search: "Buscar:",
                infoThousands: ",",
                loadingRecords: "Cargando...",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior",
                },
            },
        });
    });
    $(".select2CategoryErp").select2({
        placeholder: "Busca una categoria Erp",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $("#modal-create-producto"),
    });
    $(".select2SubCategoryErp").select2({
        placeholder: "Busca una Subcategoria Erp",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $("#modal-create-producto"),
    });
    $(".select2CategoryWeb").select2({
        placeholder: "Busca un proveedor",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $("#modal-create-producto"),
    });
    $(".select2SubCategoryWeb").select2({
        placeholder: "Busca un proveedor",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $("#modal-create-producto"),
    });
    $(".select2Marca").select2({
        placeholder: "Busca un proveedor",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $("#modal-create-producto"),
    });
});

// El llenado de `productosCombo` depende del contenido de `data.componentes` que proviene del backend
// Validemos paso a paso el proceso y puntos clave donde puede estar fallando:

const edit = async (id) => {
    console.log(id);
    const response = await fetch(`/producto-edit/${id}`); // 1. Petición al controlador
    const data = await response.json(); // 2. Espera estructura: { listadoproductos: {...}, componentes: [...] }
    console.log(data);
    if (contentform.hasAttribute("disabled")) {
        contentform.removeAttribute("disabled");
        $("#cliente").prop("disabled", false);
    }
    showForm(data); // 3. Enviamos la data al form
};

const showForm = (data) => {
    let resp = data.listadoproductos;
    console.log(resp);

    // Log de tipos y valores antes de la conversion
    console.log("Antes de conversion:");
    console.log("alerta:", resp.alerts, "Type:", typeof resp.alerts);
    console.log("iva:", resp.iva, "Type:", typeof resp.iva);
    console.log(
        "otro_impuesto:",
        resp.otro_impuesto,
        "Type:",
        typeof resp.otro_impuesto
    );
    console.log(
        "impoconsumo:",
        resp.impoconsumo,
        "Type:",
        typeof resp.impoconsumo
    );

    // Convierte a numero
    const ivaNumber = Number(resp.iva);
    const otroImpuestoNumber = Number(resp.otro_impuesto);
    const impoconsumoNumber = Number(resp.impoconsumo);

    // Log de tipos y valores despues de conversion
    console.log("Despues de conversion:");
    console.log("iva:", ivaNumber, "Type:", typeof ivaNumber);
    console.log(
        "otro_impuesto:",
        otroImpuestoNumber,
        "Type:",
        typeof otroImpuestoNumber
    );
    console.log(
        "impoconsumo:",
        impoconsumoNumber,
        "Type:",
        typeof impoconsumoNumber
    );

    producto_id.value = resp.id;
    $("#categoriaerp").val(resp.category_id).trigger("change");
    $("#subcategoriaerp").val(resp.meatcut_id).trigger("change");
    $("#categoriaweb").val(resp.categories_comerciales_id).trigger("change");
    $("#subcategoriaweb")
        .val(resp.subcategory_comerciales_id)
        .trigger("change");
    $("#marca").val(resp.brand_id).trigger("change");
    $("#nivel").val(resp.level_product_id).trigger("change");
    $("#presentacion").val(resp.unitofmeasure_id).trigger("change");
    $("#quantity").val(resp.quantity).trigger("change");

    $("#nameproducto").val(resp.name).trigger("change");
    $("#code").val(resp.code).trigger("change");
    $("#codigobarra").val(resp.barcode).trigger("change");
    $("#alerta").val(resp.alerts).trigger("change");
    $("#impuestoiva").val(ivaNumber).trigger("change");
    $("#isa").val(otroImpuestoNumber).trigger("change");
    $("#impoconsumo").val(impoconsumoNumber).trigger("change");
    $("#product_type").val(resp.type).trigger("change");

    // Cargar productos en la tabla si existen
    // Asegúrate que data.componentes tenga estructura
    console.log("componentes recibidos:", data.componentes);

    const productos = data.componentes || []; // 4. Asegura que sea array
    const tbody = $("#product-table tbody");
    tbody.empty();
    productosCombo.length = 0; // 5. Limpia array global antes de rellenar

    productos.forEach((producto, index) => {
        productosCombo.push(producto.product_id); // 6. Relleno del array
        const row = `
            <tr data-id="${producto.product_id}">
                <td>${producto.product_name}
                    <input type="hidden" data-index="${index}" name="componentes[${index}][product_id]" value="${producto.product_id}">
                </td>
                <td>
                    <input type="number" data-index="${index}"
                           name="componentes[${index}][cantidad]"
                           class="form-control component-qty" value="${producto.cantidad}" min="1" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-product">Eliminar</button>
                </td>
            </tr>`;
        tbody.append(row); // 7. Inserta en tabla
    });

    const modal = new bootstrap.Modal(
        document.getElementById("modal-create-producto")
    );
    modal.show();
};

// Limpiar mensajes de error al cerrar la ventana modal
$("#modal-create-producto").on("hidden.bs.modal", function () {
    $(".error-message").text("");
});

// script.js
$(document).ready(function () {
     $('#product_type').on('change', function () {
        const tipo = $(this).val();

        // Ocultar todos los campos
        $('.product-type-fields').addClass('hidden');

        if (tipo === 'combo' || tipo === 'receta') {
            $('#combo_receta_fields').removeClass('hidden');

            // Cambiar título según el tipo
            const titulo = tipo === 'combo' ? 'Datos de producto combo' : 'Datos de producto receta';
            $('#combo_receta_title').text(titulo);

            // cargar productos específicos por tipo si es necesario
        } else if (tipo === 'simple') {
            $('#simpleFields').removeClass('hidden');
        }
    });

    // Activar el estado inicial si ya hay un tipo preseleccionado
    $('#product_type').trigger('change');
    // Initialize Select2
    $("#product-selector")
        .select2({
            ajax: {
                url: "/productos/select2",
                dataType: "json",
                delay: 250,
                data: (params) => ({ q: params.term }),
                processResults: (data) => ({ results: data }),
                cache: true,
            },
            placeholder: "Agregar producto...",
            minimumInputLength: 1,
            width: "100%",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $("#modal-create-producto"),
        })
        .on("select2:select", function (e) {
            const { id: productId, text: productName } = e.params.data;
            if (productosCombo.includes(productId)) {
                alert("Este producto ya ha sido agregado al combo.");
                return;
            }
            productosCombo.push(productId);

            const index = productosCombo.length - 1;
            const row = `
            <tr data-id="${productId}">
                <td>${productName}
                    <input type="hidden" data-index="${index}" name="componentes[${index}][product_id]" value="${productId}">
                </td>
                <td>
                    <input type="number" data-index="${index}"
                           name="componentes[${index}][cantidad]"
                           class="form-control component-qty" value="1" min="1" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-product">Eliminar</button>
                </td>
            </tr>`;
            $("#product-table tbody").append(row);
            $(this).val(null).trigger("change");
        });

    // Remover producto
    $("#product-table").on("click", ".remove-product", function () {
        const row = $(this).closest("tr");
        const productId = row.data("id");
        const idx = row.find("input[type=hidden]").data("index");

        // Eliminar del array
        const pos = productosCombo.indexOf(productId);
        if (pos > -1) productosCombo.splice(pos, 1);

        row.remove();

        // Reindexar filas e inputs
        $("#product-table tbody tr").each(function (i) {
            $(this).attr("data-id", productosCombo[i]);
            $(this)
                .find("input[type=hidden]")
                .attr("data-index", i)
                .attr("name", `componentes[${i}][product_id]`);
            $(this)
                .find(".component-qty")
                .attr("data-index", i)
                .attr("name", `componentes[${i}][cantidad]`);
        });
    });

    // Envío de formulario con FormData
    $("#form-producto").on("submit", function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);

        // Reagregar componentes (si algún cambio manual)
        $("#product-table tbody tr").each(function (i) {
            const productId = $(this).data("id");
            const qty = $(this).find("input.component-qty").val();
            formData.set(`componentes[${i}][product_id]`, productId);
            formData.set(`componentes[${i}][cantidad]`, qty);
        });

        // Agregar tipo de producto
        formData.set("product_type", $("#product_type").val());

        sendData("/productosave", formData, token)
            .then((resp) => {
                if (resp.status === 1) {
                    form.reset();
                    $("#modal-create-producto").modal("hide");
                    successToastMessage(resp.message);
                    refresh_table();
                } else {
                    // Mostrar errores
                    $(".error-message").text("");
                    $.each(resp.errors || {}, (field, messages) => {
                        const $input = $(`[name="${field}"]`);
                        $input
                            .closest(".form-group")
                            .find(".error-message")
                            .text(messages[0]);
                    });
                }
            })
            .catch((err) => errorMessage("Error procesando la petición"));
    });

    // Limpieza de errores y tabla al cerrar modal
    $("#modal-create-producto").on("hidden.bs.modal", () => {
        $(".error-message").text("");
        $("#product-table tbody").empty();
        productosCombo.length = 0;
        formProducto.reset();
        // Activar el estado inicial si ya hay un tipo preseleccionado
        $("#product_type").trigger("change");
    });
});

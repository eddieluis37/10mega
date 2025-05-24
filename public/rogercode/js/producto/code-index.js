console.log("Starting");
const btnAddProducto = document.querySelector("#btnAddproducto");
const formProducto = document.querySelector("#form-producto");
const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
const btnClose = document.querySelector("#btnModalClose");

const selectCategory = document.querySelector("#categoria");

const selectMarca = document.querySelector("#marca");

const selectCentrocosto = document.querySelector("#centrocosto");
const producto_id = document.querySelector("#productoId");
const contentform = document.querySelector("#contentDisable");

const fechaalistamiento = document.querySelector("#fecha");

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
                { data: "namefamilia", name: "namefamilia" },
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
    $(".selectMarca").select2({
        placeholder: "Busca un proveedor",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });
});

const edit = async (id) => {
    console.log(id);
    const response = await fetch(`/producto-edit/${id}`);
    const data = await response.json();
    console.log(data);
    if (contentform.hasAttribute("disabled")) {
        contentform.removeAttribute("disabled");

        $("#cliente").prop("disabled", false);
    }
    showForm(data);
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
    $("#categoria").val(resp.category_id).trigger("change");
    $("#marca").val(resp.brand_id).trigger("change");
    $("#nivel").val(resp.level_product_id).trigger("change");
    $("#presentacion").val(resp.unitofmeasure_id).trigger("change");
    $("#quantity").val(resp.quantity).trigger("change");
    $("#familia").val(resp.meatcut_id).trigger("change");
    $("#subfamilia").val(resp.name).trigger("change");
    $("#code").val(resp.code).trigger("change");
    $("#codigobarra").val(resp.barcode).trigger("change");
    $("#alerta").val(resp.alerts).trigger("change");
    $("#impuestoiva").val(ivaNumber).trigger("change");
    $("#isa").val(otroImpuestoNumber).trigger("change");
    $("#impoconsumo").val(impoconsumoNumber).trigger("change");

    const modal = new bootstrap.Modal(
        document.getElementById("modal-create-producto")
    );
    modal.show();
};

const send = async (dataform, ruta) => {
    let response = await fetch(ruta, {
        headers: {
            "X-CSRF-TOKEN": token,
        },
        method: "POST",
        body: dataform,
    });
    let data = await response.json();
    //console.log(data);
    return data;
};

const refresh_table = () => {
    let table = $("#tableProducto").dataTable();
    table.fnDraw(false);
};

// Limpiar mensajes de error al cerrar la ventana modal
$("#modal-create-producto").on("hidden.bs.modal", function () {
    $(".error-message").text("");
});

// script.js
$(document).ready(function () {
    const selectedProducts = new Set();
   

    // Mostrar acordeones según tipo de producto
    $("#product_type").change(function () {
        const type = $(this).val();
        $(".product-type-fields").hide();
        if (type === "simple") {
            $("#simpleFields").show();
        } else if (type === "combo") {
            $("#combo_fields").show();
        } else if (type === "receta") {
            $("#receta_fields").show();
        }
    }).trigger('change'); // Para mostrar el acordeón si ya hay valor

    // Agregar producto a la tabla con cantidad y eliminar
    $("#product-selector").select2({
        ajax: {
            url: "/productos/select2",
            dataType: "json",
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data }),
            cache: true
        },
        placeholder: "Agregar producto...",
        minimumInputLength: 1,
        width: '100%',
        theme: "bootstrap-5",
        allowClear: true,
    }).on("select2:select", function (e) {
        const product = e.params.data;
        if (!selectedProducts.has(product.id)) {
            selectedProducts.add(product.id);
            const row = `
                <tr data-id="${product.id}">
                    <td>${product.text}</td>
                    <td>
                        <input type="number" name="products[${product.id}][quantity]" 
                               class="form-control" min="1" value="1" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-product">Eliminar</button>
                    </td>
                </tr>`;
            $("#product-table tbody").append(row);
        }
        $(this).val(null).trigger("change");
    });

    $("#product-table").on("click", ".remove-product", function () {
        const row = $(this).closest("tr");
        const id = row.data("id");
        selectedProducts.delete(id);
        row.remove();
    });

    // Agregar productos al combo
    window.addProductToCombo = function () {
        const selected = $("#combo_product_selector").select2("data")[0];
        if (selected) {
            const li = `<li>${selected.text} 
                <input type="hidden" name="combo_products[]" value="${selected.id}">
                <button type="button" onclick="$(this).parent().remove()">Eliminar</button>
            </li>`;
            $("#combo_list").append(li);
            $("#combo_product_selector").val(null).trigger("change");
        }
    };

    // Agregar ingredientes a la receta
    window.addProductToReceta = function () {
        const selected = $("#receta_product_selector").select2("data")[0];
        if (selected) {
            const li = `<li>${selected.text} 
                <input type="hidden" name="receta_ingredients[]" value="${selected.id}">
                <button type="button" onclick="$(this).parent().remove()">Eliminar</button>
            </li>`;
            $("#receta_list").append(li);
            $("#receta_product_selector").val(null).trigger("change");
        }
    };
});

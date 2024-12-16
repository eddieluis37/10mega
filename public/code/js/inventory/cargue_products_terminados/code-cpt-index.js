console.log("Comenzando");
const btnAddLote = document.querySelector("#btnAddlote");
const btnAddProducto = document.querySelector("#btnAddproducto");
const formLote = document.querySelector("#form-lote");
const formProducto = document.querySelector("#form-producto");
const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
const btnClose = document.querySelector("#btnModalClose");

$(".select2Prod").select2({
    placeholder: "Busca un producto",
    width: "100%",
    theme: "bootstrap-5",
    allowClear: true,
});

$(".select2Lote").select2({
    placeholder: "Busca un lote",
    width: "100%",
    theme: "bootstrap-5",
    allowClear: true,
});

// Función para recargar el select de lotes del modal_create_producto
function refreshLote() {
    fetch("/lote-data")
        .then((response) => response.json())
        .then((data) => {
            const $loteSelect = $("#loteProd");
            $loteSelect.empty(); // Limpia las opciones actuales
            $loteSelect.append('<option value="">Seleccione el lote</option>'); // Opción por defecto

            data.forEach((option) => {
                $loteSelect.append(
                    `<option value="${option.id}" data="${option}">${option.name}</option>`
                );
            });
        });
}

$(document).ready(function () {
    var dataTable;

    function initializeDataTable(
        centrocostoId = "-1",
        categoriaId = "-1",
        loteId = "1"
    ) {
        dataTable = $("#tableInventory").DataTable({
            paging: true,
            pageLength: 15,
            autoWidth: false,
            processing: true,
            serverSide: true,
            lengthMenu: [
                [10, 15, 25, 50, -1],
                [10, 15, 25, 50, "Todos"],
            ],
            ajax: {
                url: "/showCptInventory",
                type: "GET",
                data: {
                    centrocostoId: centrocostoId,
                    categoriaId: categoriaId,
                    loteId: loteId,
                },
                dataSrc: function (response) {
                    // Modify the data before processing it in the table
                    var modifiedData = response.data.map(function (item) {
                        return {
                            namecategoria: item.namecategoria,
                            nameproducto: item.nameproducto,
                            productId: item.productId,
                            namelote: item.namelote,                              
                            fechavence: item.fechavence,
                            quantity:
                                '<input type="text" class="edit-quantity text-right" value="' +
                                item.quantity +
                                '" size="4" />',
                        };
                    });
                    return modifiedData;
                },
            },
            columns: [
                { data: "namecategoria", name: "namecategoria" },
                { data: "productId", name: "productId" },
                { data: "nameproducto", name: "nameproducto" },
                { data: "namelote", name: "namelote" },
                { data: "fechavence", name: "fechavence" },
                { data: "quantity", name: "quantity" },
            ],
            order: [[2, "ASC"]],
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
            /*  dom: "Bfrtip",
            buttons: ["copy", "csv", "excel", "pdf"], */
        });
    }

    function updateCptInventory(
        productId,
        fisico,
        centrocostoId,
        lote,
        fecha_vencimiento
    ) {
        console.log("productId:", productId);
        console.log("fisico:", fisico);
        console.log("centrocostoId:", centrocostoId);
        console.log("lote:", lote);
        console.log("fecha_vencimiento:", fecha_vencimiento);
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: "/updateCptInventory",
            type: "POST",
            data: {
                productId: productId,
                fisico: fisico,
                centrocostoId: centrocostoId,
                lote: lote,
                fecha_vencimiento: fecha_vencimiento,
            },
            success: function (response) {
                console.log("Update successful");
                dataTable.ajax.reload();
            },
            error: function (xhr, status, error) {
                console.error("Error updating");
            },
        });
    }

    $(document).ready(function () {
        initializeDataTable("-1");

        $("#centrocosto, #categoria, #lote").on("change", function () {
            var centrocostoId = $("#centrocosto").val();
            var categoriaId = $("#categoria").val();
            var loteId = $("#lote").val();
            dataTable.destroy();
            initializeDataTable(centrocostoId, categoriaId, loteId);
        });

        $(document).on("keydown", ".edit-fisico", function (event) {
            if (event.which === 13 || event.which === 9) {
                event.preventDefault();
                var fisico = $(this).val().replace(",", ".");
                var lote = $(this).closest("tr").find(".edit-lote").val(); // Get lote value
                var fecha_vencimiento = $(this)
                    .closest("tr")
                    .find(".edit-fecha-vencimiento")
                    .val(); // Get fecha_vencimiento value

                // Regular Expression to validate fisico
                var regex = /^[0-9]+(\.[0-9]{1,2})?$/;
                if (regex.test(fisico)) {
                    var productId = $(this)
                        .closest("tr")
                        .find("td:eq(1)")
                        .text();
                    var centrocostoId = $("#centrocosto").val();
                    updateCptInventory(
                        productId,
                        fisico,
                        centrocostoId,
                        lote,
                        fecha_vencimiento
                    ); // Pass lote and fecha_vencimiento
                    $(this)
                        .closest("tr")
                        .next()
                        .find(".edit-fisico")
                        .focus()
                        .select();
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Número incorrecto",
                        text: "Solo acepta Números enteros con decimales de (2) dos cifras, separados por . o por ,",
                    });
                    console.error("Solo acepta numero enteros y decimales");
                }
            }
        });
    });
});






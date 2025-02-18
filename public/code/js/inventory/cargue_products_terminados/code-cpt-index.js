console.log("Comenzando");
const btnAddLote = document.querySelector("#btnAddlote");
const btnAddProducto = document.querySelector("#btnAddproducto");
const formLote = document.querySelector("#form-lote");
const formProducto = document.querySelector("#form-producto");
const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
const btnClose = document.querySelector("#btnModalClose");
const btnClose2 = document.querySelector("#btnModalClose2");

function showModalcreateLote() {
    // Lógica para mostrar el modal
    $('#modal-create-lote').modal('show');
}

$(".select2Store").select2({
    placeholder: "Busca una bodega",
    width: "100%",
    theme: "bootstrap-5",
    allowClear: true,
});

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
                    `<option value="${option.id}" data="${option}">${option.codigo}</option>`
                );
            });
        });
}

$(document).ready(function () {
    var dataTable;

    function initializeDataTable(
        storeId = "-1",
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
                    storeId: storeId,
                    categoriaId: categoriaId,
                    loteId: loteId,
                },
                dataSrc: function (response) {
                    var modifiedData = response.data.map(function (item) {
                        const costoFormatted = new Intl.NumberFormat("es-CO", {
                            style: "currency",
                            currency: "COP",
                            minimumFractionDigits: 0,
                        }).format(item.costo);

                        return {
                            productoLoteId: item.productoLoteId,
                            namebodega: item.namebodega,
                            nameproducto: item.nameproducto,
                            productId: item.productId,
                            codigolote: item.codigolote,
                            fechavence: item.fechavence,
                            quantity: `
                                <input type="text" 
                                    class="edit-quantity text-right" 
                                    value="${item.quantity}" 
                                    size="4" 
                                />
                            `,
                            costo: `
                                <input type="text" 
                                    class="edit-costo text-right" 
                                    value="${costoFormatted}" 
                                    size="7" 
                                />
                            `,
                        };
                    });
                    return modifiedData;
                },
            },
            columns: [
                { data: "productoLoteId", name: "productoLoteId" },
                { data: "namebodega", name: "namebodega" },
                { data: "productId", name: "productId" },
                { data: "nameproducto", name: "nameproducto" },
                { data: "codigolote", name: "codigolote" },
                { data: "fechavence", name: "fechavence" },
                { data: "quantity", name: "quantity" },
                { data: "costo", name: "costo" },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `<button class="btn btn-danger delete-btn" data-id="${row.productoLoteId}">Eliminar</button>`;
                    },
                },
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
        });

        // Function to delete a record
        function deleteProductLote(id) {
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                url: `/product-lote/${id}`,
                type: "DELETE",
                success: function (response) {
                    if (response.success) {
                        console.log("Delete successful");
                        // dataTable.ajax.reload(); // Refresh the DataTable
                    } else {
                        console.error("Delete failed");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error deleting record:", error);
                },
            });
        }

        // Event listener for delete button
        $(document).on("click", ".delete-btn", function () {
            const id = $(this).data("id");
            if (confirm("Esta seguro de eliminar registro?")) {
                deleteProductLote(id);
            }
        });
    }

    initializeDataTable("-1");

    $("#store, #categoria, #lote").on("change", function () {
        var storeId = $("#store").val();
        var categoriaId = $("#categoria").val();
        var loteId = $("#lote").val();
        dataTable.destroy();
        initializeDataTable(storeId, categoriaId, loteId);
    });

    $(document).on("keydown", ".edit-quantity, .edit-costo", function (event) {
        if (event.which === 13 || event.which === 9) {
            event.preventDefault();

            var row = $(this).closest("tr");
            var quantity = row.find(".edit-quantity").val().replace(",", ".");
            var costo = row.find(".edit-costo").val().replace(/[^\d]/g, ""); // Remove formatting before sending
            var loteId = $("#lote").val();
            var productoLoteId = row.find("td:eq(0)").text(); // Assuming productoLoteId is in the first column
            var productId = row.find("td:eq(2)").text(); // Get productId from the third column
            var storeId = $("#store").val();

            // Regular Expression to validate numbers
            var regex = /^[0-9]+(\.[0-9]{1,2})?$/;
            if (regex.test(quantity) && regex.test(costo)) {
                updateCptInventory(
                    productId,
                    quantity,
                    costo,
                    storeId,
                    loteId,
                    productoLoteId
                );
                dataTable.ajax.reload(); // Refresh DataTable after update
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Número incorrecto",
                    text: "Solo acepta Números enteros con decimales de (2) dos cifras, separados por . o por ,",
                });
                console.error("Solo acepta números enteros y decimales");
            }
        }
    });

    function updateCptInventory(
        productId,
        quantity,
        costo,
        storeId,
        loteId,
        productoLoteId
    ) {
        console.log("productId:", productId);
        console.log("quantity:", quantity);
        console.log("costo:", costo);
        console.log("storeId:", storeId);
        console.log("loteId:", loteId);
        console.log("productoLoteId:", productoLoteId);

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: "/updateCptInventory",
            type: "POST",
            data: {
                productId: productId,
                quantity: quantity,
                costo: costo,
                storeId: storeId,
                loteId: loteId,
                productoLoteId: productoLoteId,
            },
            success: function (response) {
                console.log("Update successful");
            },
            error: function (xhr, status, error) {
                console.error("Error updating");
            },
        });
    }
});

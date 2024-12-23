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
                        return {
                            productoLoteId: item.productoLoteId,
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
                { data: "productoLoteId", name: "productoLoteId" },
                { data: "namecategoria", name: "namecategoria" },
                { data: "productId", name: "productId" },
                { data: "nameproducto", name: "nameproducto" },
                { data: "namelote", name: "namelote" },
                { data: "fechavence", name: "fechavence" },
                { data: "quantity", name: "quantity" },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `<button class="btn btn-danger delete-btn" data-id="${row.productoLoteId}">Delete</button>`;
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
            if (confirm("Are you sure you want to delete this record?")) {
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

    $(document).on("keydown", ".edit-quantity", function (event) {
        if (event.which === 13 || event.which === 9) {
            event.preventDefault();
            var quantity = $(this).val().replace(",", "."); // Replace comma with dot for decimal
            var loteId = $("#lote").val(); // Get loteId from the dropdown
            var fecha_vencimiento = $(this)
                .closest("tr")
                .find(".edit-fecha-vencimiento")
                .val(); // Get fecha_vencimiento value

            // Get productoLoteId from the current row
            var productoLoteId = $(this).closest("tr").find("td:eq(0)").text(); // Assuming productoLoteId is in the first column

            // Regular Expression to validate quantity
            var regex = /^[0-9]+(\.[0-9]{1,2})?$/;
            if (regex.test(quantity)) {
                var productId = $(this).closest("tr").find("td:eq(2)").text(); // Get productId from the third column
                var storeId = $("#store").val();
                updateCptInventory(
                    productId,
                    quantity,
                    storeId,
                    loteId,
                    fecha_vencimiento,
                    productoLoteId
                ); // Pass productoLoteId
                $(this)
                    .closest("tr")
                    .next()
                    .find(".edit-quantity")
                    .focus()
                    .select(); // Focus on the next input
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Número incorrecto",
                    text: "Solo acepta Números enteros con decimales de (2) dos cifras, separados por . o por ,",
                });
                console.error("Solo acepta numero enteros y decimales");
            }

            function updateCptInventory(
                productId,
                quantity,
                storeId,
                loteId,
                fecha_vencimiento,
                productoLoteId
            ) {
                console.log("productId:", productId);
                console.log("quantity:", quantity);
                console.log("storeId:", storeId);
                console.log("loteId:", loteId);
                console.log("fecha_vencimiento:", fecha_vencimiento);
                console.log("productoLoteId:", productoLoteId);

                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    url: "/updateCptInventory",
                    type: "POST",
                    data: {
                        productId: productId,
                        quantity: quantity,
                        storeId: storeId,
                        loteId: loteId,
                        fecha_vencimiento: fecha_vencimiento,
                        productoLoteId: productoLoteId, // Include productoLoteId in the data
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
        }
    });
});

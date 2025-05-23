console.log("Comenzando");
const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

$(document).ready(function () {
    var dataTable;

    function initializeDataTable(centrocostoId = "-1", categoriaId = "-1") {
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
                },
                dataSrc: function (response) {
                    // Modify the data before processing it in the table
                    var modifiedData = response.data.map(function (item) {
                        return {
                            namecategoria: item.namecategoria,
                            nameproducto: item.nameproducto,
                            productId: item.productId,
                            lote:
                                '<input type="text" class="edit-lote" value="' +
                                item.lote +
                                '" size="10" />', // New input for lote
                            fecha_vencimiento:
                                '<input type="date" class="edit-fecha-vencimiento" value="' +
                                item.fecha_vencimiento +
                                '" />',
                            fisico:
                                '<input type="text" class="edit-fisico text-right" value="' +
                                item.fisico +
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
                { data: "lote", name: "lote" },
                { data: "fecha_vencimiento", name: "fecha_vencimiento" },
                { data: "fisico", name: "fisico" },
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

        $("#centrocosto, #categoria").on("change", function () {
            var centrocostoId = $("#centrocosto").val();
            var categoriaId = $("#categoria").val();

            dataTable.destroy();
            initializeDataTable(centrocostoId, categoriaId);
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

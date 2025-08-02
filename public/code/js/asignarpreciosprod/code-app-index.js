console.log("Comenzando");

$(document).ready(function () {
    const token = $('meta[name="csrf-token"]').attr("content");
    let dataTable;

    function initializeDataTable(listaprecioId = "-1", categoriaId = "-1") {
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
                url: "/showAPPSwitch",
                type: "GET",
                data: { listaprecioId, categoriaId },
                dataSrc: function (response) {
                    return response.data.map((item) => {
                        let porc_utilidad = (item.utilidad / item.precio) * 100;
                        return {
                            namecategoria: item.namecategoria,
                            nameproducto: item.nameproducto,
                            costo: item.costo,
                            costo_total: item.costo_total,
                            porc_util_proyectada: getPorcUtilProyectInput(
                                item.porc_util_proyectada,
                                item.productId
                            ),
                            precio_proyectado: item.precio_proyectado,
                            precio: getPriceInput(item.precio, item.productId),
                            porc_descuento: getPorcDescuentoInput(
                                item.porc_descuento,
                                item.productId
                            ),
                            utilidad: item.utilidad,
                            contribucion: item.contribucion,
                            porc_utilidad,
                            porc_imp_iva: item.porc_imp_iva,
                            porc_imp_ultra_pro: item.porc_imp_ultra_pro,
                            porc_imp_consumo: item.porc_imp_consumo,
                            productId: item.productId,
                            precio_venta: item.precio_venta,
                            status: getStatusCheckbox(
                                item.status,
                                item.productId
                            ),
                        };
                    });
                },
            },
            columns: [
                { data: "namecategoria" },
                { data: "productId" },
                { data: "nameproducto" },
                {
                    data: "costo",
                    render: (data) => "$ " + formatCantidadSinCero(data),
                },
                {
                    data: "costo_total",
                    render: (data) => "$ " + formatCantidadSinCero(data),
                },
                { data: "porc_util_proyectada" },
                {
                    data: "precio_proyectado",
                    render: (data) => "$ " + formatCantidadSinCero(data),
                },
                { data: "precio" },
                { data: "porc_descuento" },
                {
                    data: "utilidad",
                    render: (data) => "$ " + formatCantidadSinCero(data),
                },
                {
                    data: "porc_utilidad",
                    render: (data) => formatCantidad(data) + "%",
                },
                {
                    data: "contribucion",
                    render: (data) => "$ " + formatCantidadSinCero(data),
                },
                {
                    data: "porc_imp_iva",
                    render: (data) => formatCantidad(data) + "%",
                },
                {
                    data: "porc_imp_ultra_pro",
                    render: (data) => formatCantidad(data) + "%",
                },
                {
                    data: "porc_imp_consumo",
                    render: (data) => formatCantidad(data) + "%",
                },
                {
                    data: "precio_venta",
                    render: (data) => "$ " + formatCantidadSinCero(data),
                },
                { data: "status" },
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
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior",
                },
            },
        });
    }

    function getStatusCheckbox(status, productId) {
        return `<input type="checkbox" class="edit-status" data-product-id="${productId}" ${status ? "checked" : ""} />`;
    }

    function getPriceInput(precio, productId) {
        let formatted = new Intl.NumberFormat("es-CO", {
            style: "currency",
            currency: "COP",
            minimumFractionDigits: 0,
        }).format(precio);
        return `<input type="text" class="edit-precio" data-product-id="${productId}" value="${formatted}" size="8" />`;
    }

    function getPorcUtilProyectInput(porc, productId) {
        let pct = (porc * 1).toFixed(2);
        return `<input type="text" class="edit-porc_util_proyectada" data-product-id="${productId}" value="${pct}" size="6" />`;
    }

    function getPorcDescuentoInput(porc, productId) {
        let pct = (porc * 1).toFixed(2);
        return `<input type="text" class="edit-porc_descuento" data-product-id="${productId}" value="${pct}" size="6" />`;
    }

    function updateAPPSwitch(
        productId,
        precio,
        porc_descuento,
        porc_util_proyectada,
        listaprecioId,
        status
    ) {
        console.log({
            productId,
            precio,
            porc_descuento,
            porc_util_proyectada,
            listaprecioId,
            status,
        });
        $.ajax({
            headers: { "X-CSRF-TOKEN": token },
            url: "/updateAPPSwitch",
            type: "POST",
            data: {
                productId,
                precio,
                porc_descuento,
                porc_util_proyectada,
                status,
                listaprecioId,
            },
            success: () => {
                console.log("Update successful");
                dataTable.ajax.reload(null, false);
            },
            error: () => console.error("Error updating"),
        });
    }

    function handlePorcUtilProyectInput(e) {
        if (e.which === 13 || e.which === 9) {
            e.preventDefault();
            let val = $(this)
                .val()
                .replace(/[$\s,%]/g, "")
                .replace(",", ".");
            if (/^\d{1,3}(\.\d{1,2})?$/.test(val)) {
                let productId = $(this).data("product-id");
                let listaprecioId = $("#listaprecio").val();
                updateAPPSwitch(
                    productId,
                    null,
                    null,
                    val,
                    listaprecioId,
                    null
                );
                $(this).select();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Porcentaje proyectado incorrecto",
                    text: "Ingrese un valor válido",
                });
            }
        }
    }

    function handlePriceInput(e) {
        if (e.which === 13 || e.which === 9) {
            e.preventDefault();
            let val = $(this)
                .val()
                .replace(/[$\s.,]/g, "");
            if (/^\d{1,8}(\.\d{1,2})?$/.test(val)) {
                let productId = $(this).data("product-id");
                let listaprecioId = $("#listaprecio").val();
                updateAPPSwitch(
                    productId,
                    val,
                    null,
                    null,
                    listaprecioId,
                    null
                );
                $(this).select();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Precio incorrecto",
                    text: "Solo números menores a 100.000.000",
                });
            }
        }
    }

    function handlePorcDescInput(e) {
        if (e.which === 13 || e.which === 9) {
            e.preventDefault();
            let val = $(this)
                .val()
                .replace(/[$\s,%]/g, "")
                .replace(",", ".");
            if (/^\d{1,3}(\.\d{1,2})?$/.test(val)) {
                let productId = $(this).data("product-id");
                let listaprecioId = $("#listaprecio").val();
                updateAPPSwitch(
                    productId,
                    null,
                    val,
                    null,
                    listaprecioId,
                    null
                );
                $(this).select();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Porcentaje de descuento incorrecto",
                    text: "Ingrese un valor válido",
                });
            }
        }
    }

    function handleStatusChange() {
        let productId = $(this).data("product-id");
        let listaprecioId = $("#listaprecio").val();
        let status = $(this).is(":checked") ? 1 : 0;
        updateAPPSwitch(productId, null, null, null, listaprecioId, status);
    }

    // Inicialización
    initializeDataTable();
    $("#listaprecio, #categoria").on("change", function () {
        dataTable.destroy();
        initializeDataTable($("#listaprecio").val(), $("#categoria").val());
    });

    // Delegación de eventos
    $(document).on("keydown", ".edit-precio", handlePriceInput);
    $(document).on(
        "keydown",
        ".edit-porc_util_proyectada",
        handlePorcUtilProyectInput
    );
    $(document).on("keydown", ".edit-porc_descuento", handlePorcDescInput);
    $(document).on("change", ".edit-status", handleStatusChange);
});

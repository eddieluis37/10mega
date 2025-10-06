import { sendData } from "../exportModule/core/rogercode-core.js";
import {
    successToastMessage,
    errorMessage,
} from "../exportModule/message/rogercode-message.js";
import {
    loadingStart,
    loadingEnd,
} from "../exportModule/core/rogercode-core.js";

console.log("Starting");

const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

var dataTable;

function initializeDataTable({
    centroId = "",
    vendedorId = "",
    domiciliarioId = "",
    clienteId = "",
    startDateId = "",
    endDateId = "",
} = {}) {
    dataTable = $("#tableInventory").DataTable({
        paging: false,
        pageLength: 150,
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: "/reportVentasPorProdClient",
            type: "GET",
            data: {
                centrocosto: centroId,
                vendedor: vendedorId,
                domiciliario: domiciliarioId,
                cliente: clienteId,
                startDateId: startDateId,
                endDateId: endDateId,
            },
        },
        columns: [
            {
                data: "factura",
                name: "factura",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        (data ? data : "") +
                        "</div>"
                    );
                },
            },
            {
                data: "direccion_envio",
                name: "direccion_envio",
                render: function (data) {
                    if (!data)
                        return "<span style='font-size:smaller;'>-</span>";
                    let sub = data.substring(0, 25).toLowerCase();
                    let cap = sub.charAt(0).toUpperCase() + sub.slice(1);
                    return data.length > 25
                        ? `<span style="font-size: smaller;" title="${data}">${cap}.</span>`
                        : `<span style="font-size: smaller; display:block; text-align:center;">${cap}</span>`;
                },
            },
            {
                data: "telefono",
                name: "telefono",
                render: function (data) {
                    return (
                        "<div style='text-align: center;'>" +
                        (data ? data : "") +
                        "</div>"
                    );
                },
            },
            {
                data: "vendedor_name",
                name: "vendedor_name",
                render: function (data) {
                    if (!data)
                        return "<span style='font-size:smaller;'>-</span>";
                    let sub = data.substring(0, 18).toLowerCase();
                    let cap = sub.charAt(0).toUpperCase() + sub.slice(1);
                    return data.length > 18
                        ? `<span title="${data}">${cap}.</span>`
                        : `<span style="font-size:smaller; display:block; text-align:center;">${cap}</span>`;
                },
            },
            {
                data: "cajero_name",
                name: "cajero_name",
                render: function (data) {
                    return (
                        "<div style='text-align: center;'>" +
                        (data ? data : "") +
                        "</div>"
                    );
                },
            },
            {
                data: "domiciliario_name",
                name: "domiciliario_name",
                render: function (data) {
                    return (
                        "<div style='text-align: center;'>" +
                        (data ? data : "") +
                        "</div>"
                    );
                },
            },
            {
                data: "third_identification",
                name: "third_identification",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        formatCantidad(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "third_name",
                name: "third_name",
                render: function (data) {
                    let subStringData = data.substring(0, 19).toLowerCase();
                    let capitalizedSubString =
                        subStringData.charAt(0).toUpperCase() +
                        subStringData.slice(1);
                    if (data.length > 19) {
                        return `<span style="font-size: smaller;" title="${data}">${capitalizedSubString}.</span>`;
                    } else {
                        return `<span style="font-size: smaller; display: block; text-align: center;">${capitalizedSubString}</span>`;
                    }
                },
            },
            {
                data: "product_code",
                name: "product_code",
                render: function (data, type, row) {
                    return "<div style='text-align: right;'>" + data + "</div>";
                },
            },
            {
                data: "product_name",
                name: "product_name",
                render: function (data) {
                    let subStringData = data.substring(0, 25).toLowerCase();
                    let capitalizedSubString =
                        subStringData.charAt(0).toUpperCase() +
                        subStringData.slice(1);
                    if (data.length > 25) {
                        return `<span style="font-size: smaller;" title="${data}">${capitalizedSubString}.</span>`;
                    } else {
                        return `<span style="font-size: smaller; display: block; text-align: center;">${capitalizedSubString}</span>`;
                    }
                },
            },
            {
                data: "category_name",
                name: "category_name",
                render: function (data) {
                    let subStringData = data.substring(0, 10).toLowerCase();
                    let capitalizedSubString =
                        subStringData.charAt(0).toUpperCase() +
                        subStringData.slice(1);
                    if (data.length > 10) {
                        return `<span style="font-size: smaller;" title="${data}">${capitalizedSubString}.</span>`;
                    } else {
                        return `<span style="font-size: smaller; display: block; text-align: center;">${capitalizedSubString}</span>`;
                    }
                },
            },
            {
                data: "cantidad_venta",
                name: "cantidad_venta",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        formatCantidad(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "notacredito_quantity",
                name: "notacredito_quantity",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        formatCantidad(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "notadebito_quantity",
                name: "notadebito_quantity",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        formatCantidad(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "cantidad_venta_real",
                name: "cantidad_venta_real",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        formatCantidad(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "dinero_venta_real",
                name: "dinero_venta_real",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>$" +
                        formatCantidad(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "descuento_producto",
                name: "descuento_producto",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>$" +
                        formatCantidad(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "descuento_cliente",
                name: "descuento_cliente",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>$" +
                        formatCantidad(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "sub_total",
                name: "sub_total",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>$" +
                        formatCantidad(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "impuesto_salud",
                name: "impuesto_salud",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>$" +
                        formatCantidad(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "iva",
                name: "iva",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>$" +
                        formatCantidad(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "impoconsumo",
                name: "impoconsumo",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>$" +
                        formatCantidad(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "total",
                name: "total",
                render: function (data) {
                    return (
                        "<div style='text-align: right;'>$" +
                        formatCantidad(data) +
                        "</div>"
                    );
                },
            },
        ],
        // El order para ordenar por 'third_name' en la posición 7
        order: [[7, "ASC"]],

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
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf"],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            // Totalizar la columna "totalCantVent"
            var totalCantVent = api
                .column("cantidad_venta:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0)
                .toFixed(2);
            var totalCantVentFormatted = formatCantidad(totalCantVent);

            // Totalizar la columna "cantidad notacredito"
            var totalCantNC = api
                .column("notacredito_quantity:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    var value = parseFloat(b);
                    return isNaN(value) ? a : a + value;
                }, 0)
                .toFixed(2);
            var totalCantNCFormatted = formatCantidad(totalCantNC);

            // Totalizar la columna "cantidad nota debito"
            var totalCantND = api
                .column("notadebito_quantity:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    var value = parseFloat(b);
                    return isNaN(value) ? a : a + value;
                }, 0)
                .toFixed(2);
            var totalCantNDFormatted = formatCantidad(totalCantND);

            // Totalizar la columna "cantidad venta real"
            var totalCantVR = api
                .column("cantidad_venta_real:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    var value = parseFloat(b);
                    return isNaN(value) ? a : a + value;
                }, 0)
                .toFixed(2);
            var totalCantVRFormatted = formatCantidad(totalCantVR);

            var totalDineroVR = api
                .column("dinero_venta_real:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0)
                .toFixed(2);

            var totalDineroVRFormatted =
                "$" + formatCantidad(totalDineroVR);

            var totalDescProd = api
                .column("descuento_producto:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0)
                .toFixed(2);
            var totalDescProdVRFormatted =
                "$" + formatCantidad(totalDescProd);

            var totalDescCliente = api
                .column("descuento_cliente:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0)
                .toFixed(2);
            var totalDescClienteFormatted =
                "$" + formatCantidad(totalDescCliente);

            var totalSubTotal = api
                .column("sub_total:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0)
                .toFixed(2);
            var totalSubTotalFormatted =
                "$" + formatCantidad(totalSubTotal);

            var totalImpSalud = api
                .column("impuesto_salud:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0)
                .toFixed(2);
            var totalImpSaludFormatted =
                "$" + formatCantidad(totalImpSalud);

            var totalIva = api
                .column("iva:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    var value = parseFloat(b);
                    return isNaN(value) ? a : a + value;
                }, 0)
                .toFixed(2);
            var totalIvaFormatted = "$" + formatCantidad(totalIva);

             var totalImpoconsumo = api
                .column("impoconsumo:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    var value = parseFloat(b);
                    return isNaN(value) ? a : a + value;
                }, 0)
                .toFixed(2);

            var totalImpoconsumoFormatted = "$" + formatCantidad(totalImpoconsumo);

            var totalTotal = api
                .column("total:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    var value = parseFloat(b);
                    return isNaN(value) ? a : a + value;
                }, 0)
                .toFixed(2);

            var totalTotalFormatted = "$" + formatCantidad(totalTotal);

            // Agregar los valores totales en el footer
            $(api.column("cantidad_venta:name").footer())
                .html(totalCantVentFormatted)
                .css("text-align", "right");
            $(api.column("notacredito_quantity:name").footer())
                .html(totalCantNCFormatted)
                .css("text-align", "right");
            $(api.column("notadebito_quantity:name").footer())
                .html(totalCantNDFormatted)
                .css("text-align", "right");
            $(api.column("cantidad_venta_real:name").footer())
                .html(totalCantVRFormatted)
                .css("text-align", "right");
            $(api.column("dinero_venta_real:name").footer())
                .html(totalDineroVRFormatted)
                .css("text-align", "right");
            $(api.column("descuento_producto:name").footer())
                .html(totalDescProdVRFormatted)
                .css("text-align", "right");
            $(api.column("descuento_cliente:name").footer())
                .html(totalDescClienteFormatted)
                .css("text-align", "right");
            $(api.column("sub_total:name").footer())
                .html(totalSubTotalFormatted)
                .css("text-align", "right");
            $(api.column("impuesto_salud:name").footer())
                .html(totalImpSaludFormatted)
                .css("text-align", "right");
            $(api.column("iva:name").footer())
                .html(totalIvaFormatted)
                .css("text-align", "right");
            $(api.column("impoconsumo:name").footer())
                .html(totalImpoconsumoFormatted)
                .css("text-align", "right");
            $(api.column("total:name").footer())
                .html(totalTotalFormatted)
                .css("text-align", "right");
        },
    });
     $("#tableInventory thead th").each(function (index) {
        var originalTitle = $(this).text().trim();

        if (index === 0) {
            $(this).html(
                '<div style="display:flex;flex-direction:column;align-items:center;">' +
                    '<span style="font-weight:600;">FACTURA</span>' +
                    '<input type="text" placeholder="Buscar FACTURA" style="margin-top:4px;" />' +
                "</div>"
            );
        } else if (index === 3) {
            $(this).html(
                '<div style="display:flex;flex-direction:column;align-items:center;">' +
                    `<span style="font-weight:600;">${originalTitle}</span>` +
                    '<input type="text" placeholder="Buscar ' + originalTitle + '" style="margin-top:4px;" />' +
                "</div>"
            );
        } else if (index === 7) {
            $(this).html(
                '<div style="display:flex;flex-direction:column;align-items:center;">' +
                    '<span style="font-weight:600;">NOM_CLIENTES</span>' +
                    '<input type="text" placeholder="Buscar NOM_CLIENTES" style="margin-top:4px;" />' +
                "</div>"
            );
        } else if (index === 9) {
            $(this).html(
                '<div style="display:flex;flex-direction:column;align-items:center;">' +
                    '<span style="font-weight:600;">PRODUCTO</span>' +
                    '<input type="text" placeholder="Buscar PRODUCTO" style="margin-top:4px;" />' +
                "</div>"
            );
        }
    });

    // Aplicar el filtro de búsqueda para las columnas: 0 (FACTURA), 3, 7 (NOM_CLIENTES) y 9 (PRODUCTO)
    dataTable.columns().every(function (index) {
        if (index === 0 || index === 3 || index === 7 || index === 9) {
            var that = this;
            $("input", $(that.header())).on("keyup change clear", function () {
                if (that.search() !== this.value) {
                    that.search(this.value).draw();
                }
            });
        }
    });
}

$(document).ready(function () {
    $(".select2").select2({
        theme: "bootstrap-5",
        width: "100%",
        allowClear: true,
    });
    initializeDataTable("-1");

    // 2) Cada vez que cambie cualquier filtro, destruimos y recreamos la tabla
    $("#centrocosto, #vendedor, #domiciliario, #cliente, #startDate, #endDate").on(
        "change",
        function () {
            const filtros = {
                centroId: $("#centrocosto").val(),
                vendedorId: $("#vendedor").val(),
                domiciliarioId: $("#domiciliario").val(),
                clienteId: $("#cliente").val(),
                startDateId: $("#startDate").val(),
                endDateId: $("#endDate").val(),
            };
            dataTable.destroy();
            initializeDataTable(filtros);
        }
    );
});

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

let dataTable;
let categoriaId = "-1";
let loteId = "-1";

function initializeDataTable(
    centroId = "1",
    storeId = "-1",
    lote = "-1",
    categoria = "-1"
) {
    dataTable = $("#tableInventory").DataTable({
        paging: true,
        pageLength: 500,
        autoWidth: false,
        processing: true,
        serverSide: true,
        scrollX: false,
        ajax: {
            url: "/showPorCentroCosto",
            type: "GET",
            data: {
                centroId: centroId,
                storeId: storeId,
                loteId: lote,
                categoriaId: categoria,
            },
        },
        columns: [
            {
                data: "StoreNombre",
                name: "StoreNombre",
                render: function (data) {
                    let subStringData = data.substring(0, 25).toLowerCase();
                    let capitalizedSubString =
                        subStringData.charAt(0).toUpperCase() +
                        subStringData.slice(1);
                    if (data.length > 25) {
                        return `<span style="font-size: smaller;" title="${data}">${capitalizedSubString}.</span>`;
                    } else {
                        /*   return `<span style="font-size: smaller;">${data.toLowerCase()}</span>`; */
                        return `<span style="font-size: smaller;">${capitalizedSubString}</span>`;
                    }
                },
            },
            { data: "codigoLote", name: "codigoLote" },
            { data: "fechaVencimientoLote", name: "fechaVencimientoLote" },
            {
                data: "CategoriaNombre",
                name: "CategoriaNombre",
                render: function (data) {
                    let subStringData = data.substring(0, 25).toLowerCase();
                    let capitalizedSubString =
                        subStringData.charAt(0).toUpperCase() +
                        subStringData.slice(1);
                    if (data.length > 25) {
                        return `<span style="font-size: smaller;" title="${data}">${capitalizedSubString}.</span>`;
                    } else {
                        /*   return `<span style="font-size: smaller;">${data.toLowerCase()}</span>`; */
                        return `<span style="font-size: smaller;">${capitalizedSubString}</span>`;
                    }
                },
            },
            {
                data: "ProductoNombre",
                name: "ProductoNombre",
                render: function (data) {
                    let subStringData = data.substring(0, 25).toLowerCase();
                    let capitalizedSubString =
                        subStringData.charAt(0).toUpperCase() +
                        subStringData.slice(1);
                    if (data.length > 25) {
                        return `<span style="font-size: smaller;" title="${data}">${capitalizedSubString}.</span>`;
                    } else {
                        /*   return `<span style="font-size: smaller;">${data.toLowerCase()}</span>`; */
                        return `<span style="font-size: smaller;">${capitalizedSubString}</span>`;
                    }
                },
            },
            { data: "CantidadInicial", name: "CantidadInicial" },
            { data: "compraLote", name: "compraLote" },
            { data: "alistamiento", name: "alistamiento" },
            { data: "compensados", name: "compensados" },
            { data: "ProductoTerminado", name: "ProductoTerminado" },
            { data: "trasladoing", name: "trasladoing" },
            { data: "trasladosal", name: "trasladosal" },
            { data: "venta", name: "venta" },
            { data: "notacredito", name: "notacredito" },
            { data: "notadebito", name: "notadebito" },
            {
                data: "venta_real",
                name: "venta_real",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: left;'>" +
                        (data.toFixed(2)) +
                        "</div>"
                    );
                },
            },            
            { data: "StockIdeal", name: "StockIdeal" },
            { data: "stock", name: "stock" },
            { data: "fisico", name: "fisico" },

            {
                data: null,
                name: "disponible",
                render: function (data, type, row) {
                    var CantidadInicial = parseFloat(row.CantidadInicial);
                    var compraLote = parseFloat(row.compraLote);
                    var alistamiento = parseFloat(row.alistamiento);
                    var compensados = parseFloat(row.compensados);
                    var trasladoing = parseFloat(row.trasladoing);
                    var disponible =
                        CantidadInicial +
                        compraLote +
                        alistamiento +
                        compensados +
                        trasladoing;
                    return disponible.toFixed(2);
                },
            },

            {
                data: null,
                name: "merma",
                render: function (data, type, row) {
                    var merma = row.fisico - row.stock;
                    var mermaFormatted = merma.toFixed(2);
                    if (merma < 0) {
                        return (
                            '<span style="color: red;">' +
                            mermaFormatted +
                            "</span>"
                        );
                    } else {
                        return mermaFormatted;
                    }
                },
            },
        ],
        order: [[1, "ASC"]],
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

            // Totalizar la columna "CantidadInicial"
            var totalInvinicial = api
                .column("CantidadInicial:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0)
                .toFixed(2);

            // Totalizar la columna "compraLote"
            var totalCompraLote = api
                .column("compraLote:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0)
                .toFixed(2);

            // Totalizar la columna "alistamiento"
            var totalAlistamiento = api
                .column("alistamiento:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0)
                .toFixed(2);

            // Totalizar la columna "compensados"
            var totalCompensados = api
                .column("compensados:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0)
                .toFixed(2);

            // Totalizar la columna "trasladoing"
            var totalTrasladoing = api
                .column("trasladoing:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0)
                .toFixed(2);

            // Totalizar la columna "trasladosal"
            var totalTrasladosal = api
                .column("trasladosal:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0)
                .toFixed(2);

            // Totalizar la columna "stock"
            var totalStock = api
                .column("stock:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0)
                .toFixed(2);

            // Totalizar la columna "venta"
            var totalVenta = api
                .column("venta:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0)
                .toFixed(2);

            // Totalizar la columna "fisico"
            var totalFisico = api
                .column("fisico:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0)
                .toFixed(2);

            // Totalizar la columna "disponible"
            var totalDisponible = api
                .column("disponible:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    var value = parseFloat(b);
                    return isNaN(value) ? a : a + value;
                }, 0)
                .toFixed(2);

            // Agregar los valores totales en el footer
            $(api.column("CantidadInicial:name").footer()).html(
                totalInvinicial
            );
            $(api.column("compraLote:name").footer()).html(totalCompraLote);
            $(api.column("alistamiento:name").footer()).html(totalAlistamiento);
            $(api.column("compensados:name").footer()).html(totalCompensados);
            $(api.column("trasladoing:name").footer()).html(totalTrasladoing);
            $(api.column("trasladosal:name").footer()).html(totalTrasladosal);
            $(api.column("venta:name").footer()).html(totalVenta);
            $(api.column("stock:name").footer()).html(totalStock);
            $(api.column("fisico:name").footer()).html(totalFisico);
            $(api.column("disponible:name").footer()).html(totalDisponible);
        },
    });
}

$(document).ready(function () {
    $(".select2").select2({
        theme: "bootstrap-5",
        width: "100%",
        allowClear: true,
    });

    // Carga inicial
    initializeDataTable();

    // Al cambiar Centrocosto → recargar Bodegas
    $("#inputcentro").on("change", function () {
        const centro = $(this).val();
        const $store = $("#inputstore");
        $store
            .empty()
            .append('<option value="">Todas las bodegas</option>')
            .trigger("change");
        // Vaciar Lote
        $("#inputlote")
            .empty()
            .append('<option value="">Todos los lotes</option>')
            .trigger("change");

        $.ajax({
            url: centro ? "/getStores" : "/getAllStores",
            data: centro ? { centroId: centro } : {},
            success(data) {
                data.forEach((s) => $store.append(new Option(s.name, s.id)));
                $store.trigger("change");
            },
        });
    });

    // Al cambiar Bodega → recargar Lotes
    $("#inputstore").on("change", function () {
        const store = $(this).val();
        loteId = "-1"; // reset
        const $lote = $("#inputlote");
        $lote
            .empty()
            .append('<option value="">Todos los lotes</option>')
            .trigger("change");

        if (store) {
            $.get("/getLotes", { storeId: store }, function (data) {
                data.forEach((l) => $lote.append(new Option(l.codigo, l.id)));
                $lote.trigger("change");
            });
        }

        // refrescar tabla con nuevo filtro de bodega
        reloadTable();
    });

    // Al cambiar Lote → refresca tabla
    $("#inputlote").on("change", function () {
        loteId = $(this).val() || "-1";
        reloadTable();
    });

    // Al cambiar Categoría → refresca tabla
    $("#inputcategoria").on("change", function () {
        categoriaId = $(this).val() || "-1";
        reloadTable();
    });

    function reloadTable() {
        const centro = $("#inputcentro").val() || "-1";
        const store = $("#inputstore").val() || "-1";
        dataTable.destroy();
        initializeDataTable(centro, store, loteId, categoriaId);
        // si tienes función de totales, inclúyela también:
        cargarTotales(centro, store, loteId, categoriaId);
    }
});

function cargarTotales(centroId = "-1", storeId = "-1") {
    $.ajax({
        type: "GET",
        url: "/totales",
        data: {
            centroId: centroId,
            storeId: storeId,
        },
        dataType: "JSON",
        success: function (data) {
            $("#totalStock").html(data.totalStock);
            $("#totalInvInicial").html(data.totalInvInicial);

            $("#totalCompraLote").html(data.totalCompraLote);
            $("#totalAlistamiento").html(data.totalAlistamiento);
            $("#totalCompensados").html(data.totalCompensados);
            $("#totalTrasladoing").html(data.totalTrasladoing);

            $("#totalVenta").html(data.totalVenta);
            $("#totalTrasladoSal").html(data.totalTrasladoSal);

            $("#totalIngresos").html(data.totalIngresos);
            $("#totalSalidas").html(data.totalSalidas);

            $("#totalConteoFisico").html(data.totalConteoFisico);

            $("#diferenciaKilos").html(data.diferenciaKilos);
            $("#difKilosPermitidos").html(data.difKilosPermitidos);
            $("#porcMerma").html(data.porcMerma);
            $("#porcMermaPermitida").html(data.porcMermaPermitida);
            $("#difKilos").html(data.difKilos);
            $("#difPorcentajeMerma").html(data.difPorcentajeMerma);
        },
    });
}

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

function initializeDataTable(storeId = "-1", loteId = "-1") {
    dataTable = $("#tableInventory").DataTable({
        paging: true,
        pageLength: 500,
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: "/showInventorioCierre",
            type: "GET",
            data: {
                storeId: storeId,
                loteId: loteId,
            },
        },
        columns: [
            { data: "namecategoria", name: "namecategoria" },
            {
                data: "nameproducto",
                name: "nameproducto",
                render: function (data) {
                    let subStringData = data.substring(0, 25).toLowerCase();
                    let capitalizedSubString = subStringData.charAt(0).toUpperCase() + subStringData.slice(1);
                    if (data.length > 25) {
                        return `<span style="font-size: smaller;" title="${data}">${capitalizedSubString}.</span>`;
                    } else {
                      /*   return `<span style="font-size: smaller;">${data.toLowerCase()}</span>`; */
                        return `<span style="font-size: smaller;">${capitalizedSubString}</span>`;
                    }
                },
            },
            { data: "invinicial", name: "invinicial" },
            { data: "compraLote", name: "compraLote" },
            { data: "alistamiento", name: "alistamiento" },
            { data: "compensados", name: "compensados" },
            { data: "trasladoing", name: "trasladoing" },
            { data: "trasladosal", name: "trasladosal" },
            { data: "venta", name: "venta" },         
            { data: "notacredito", name: "notacredito" },
            { data: "notadebito", name: "notadebito" },
            { data: "venta_real", name: "venta_real" },
            { data: "stock", name: "stock" },
            { data: "fisico", name: "fisico" },

            {
                data: null,
                name: "disponible",
                render: function (data, type, row) {
                    var invinicial = parseFloat(row.invinicial);
                    var compraLote = parseFloat(row.compraLote);
                    var alistamiento = parseFloat(row.alistamiento);
                    var compensados = parseFloat(row.compensados);
                    var trasladoing = parseFloat(row.trasladoing);
                    var disponible =
                        invinicial +
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

            {
                data: null,
                name: "pmerma",
                render: function (data, type, row) {
                    var merma = row.fisico - row.stock;
                    var invinicial = parseFloat(row.invinicial);
                    var compraLote = parseFloat(row.compraLote);
                    var alistamiento = parseFloat(row.alistamiento);
                    var compensados = parseFloat(row.compensados);
                    var trasladoing = parseFloat(row.trasladoing);
                    var disponible =
                        invinicial +
                        compraLote +
                        alistamiento +
                        compensados +
                        trasladoing;
                    var pmerma = (merma / disponible) * 100;
                    if (isNaN(pmerma) || !isFinite(pmerma)) {
                        pmerma = 0;
                    }
                    var pmermaFormatted = pmerma.toFixed(2) + "%";
                    if (pmerma < 0) {
                        return (
                            '<span style="color: red;">' +
                            pmermaFormatted +
                            "</span>"
                        );
                    } else {
                        return pmermaFormatted;
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

            // Totalizar la columna "invinicial"
            var totalInvinicial = api
                .column("invinicial:name", { search: "applied" })
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

            // Totalizar la columna "venta"
            var totalVenta = api
                .column("venta:name", { search: "applied" })
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
            $(api.column("invinicial:name").footer()).html(totalInvinicial);
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

function cargarTotales(storeId = "-1", loteId = "-1") {
    $.ajax({
        type: "GET",
        url: "/totales",
        data: {
            storeId: storeId,
            loteId: loteId,
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

$(document).ready(function () {
    initializeDataTable("-1");

    $("#input_store, #input_lote").on("change", function () {
        var storeId = $("#input_store").val();
        var loteId = $("#input_lote").val();

        dataTable.destroy();
        initializeDataTable(storeId, loteId);
        cargarTotales(storeId, loteId);
    });
});

document
    .getElementById("cargarInventarioBtn")
    .addEventListener("click", (e) => {
        e.preventDefault();
        let element = e.target;
        showConfirmationAlert(element)
            .then((result) => {
                if (result && result.value) {
                    loadingStart(element);
                    const dataform = new FormData();

                    const var_storeId =
                        document.querySelector("#input_store");
                    const var_loteId =
                        document.querySelector("#input_lote");

                    dataform.append(
                        "storeId",
                        Number(var_storeId.value)
                    );
                    dataform.append(
                        "loteId",
                        Number(var_loteId.value)
                    );

                    return sendData("/cargarInventariohist", dataform, token);
                }
            })
            .then((result) => {
                console.log(result);
                if (result && result.status == 1) {
                    loadingEnd(element, "success", "Cargando al inventorio");
                    element.disabled = true;
                    return swal(
                        "EXITO",
                        "Inventario Cargado Exitosamente",
                        "success"
                    );
                }
                if (result && result.status == 0) {
                    loadingEnd(element, "success", "Cargando al inventorio");
                    errorMessage(result.message);
                }
            })
            .then(() => {
                window.location.href = "/inventory/consolidado";
            })
            .catch((error) => {
                console.error(error);
            });
    });

function showConfirmationAlert(element) {
    return swal.fire({
        title: "CONFIRMAR",
        text: "Estas seguro que desea cargar el inventario ?",
        icon: "warning",
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Acpetar",
        denyButtonText: `Cancelar`,
    });
}

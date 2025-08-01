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
    categoriaId = "",
    dateFrom = "",
    dateTo = "",
} = {}) {
    dataTable = $("#tableInventory").DataTable({
        paging: false,
        pageLength: 150,
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: "/showReportAjusteDeInv",
            type: "GET",
            data: {
                centrocosto: centroId,
                categoria: categoriaId,
                dateFrom: dateFrom,
                dateTo: dateTo,
            },
        },
        columns: [
            { data: "diahora_ajuste", name: "diahora_ajuste" },
            {
                data: "category_name",
                name: "category_name",
                render: function (data, type, row) {
                    return "<div style='text-align: right;'>" + data + "</div>";
                },
            },
            { data: "product_id", name: "product_id" },
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
                        /*   return `<span style="font-size: smaller;">${data.toLowerCase()}</span>`; */
                        return `<span style="font-size: smaller; display: block; text-align: center;">${capitalizedSubString}</span>`;
                    }
                },
            },
            {
                data: "stock_ideal_antes",
                name: "stock_ideal_antes",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        parseFloat(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "store_name",
                name: "store_name",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: center;'>" + data + "</div>"
                    );
                },
            },
            {
                data: "lote_code",
                name: "lote_code",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: center;'>" + data + "</div>"
                    );
                },
            },
            { data: "fecha_vencimientolote", name: "fecha_vencimientolote" },
            {
                data: "stock_fisico_despues",
                name: "stock_fisico_despues",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        parseFloat(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "cantidad_diferencia",
                name: "cantidad_diferencia",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        parseFloat(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "costo_inicial_total",
                name: "costo_inicial_total",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>$" +
                        formatCantidadSinCero(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "costo_total_ajuste",
                name: "costo_total_ajuste",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>$" +
                        formatCantidadSinCero(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "user_name",
                name: "user_name",
                render: function (data) {
                    let subStringData = data.substring(0, 25).toLowerCase();
                    let capitalizedSubString =
                        subStringData.charAt(0).toUpperCase() +
                        subStringData.slice(1);
                    if (data.length > 25) {
                        return `<span style="font-size: smaller;" title="${data}">${capitalizedSubString}.</span>`;
                    } else {
                        /*   return `<span style="font-size: smaller;">${data.toLowerCase()}</span>`; */
                        return `<span style="font-size: smaller; display: block; text-align: center;">${capitalizedSubString}</span>`;
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

            var totalCant = api
                .column("stock_fisico_despues:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    var value = parseFloat(b);
                    return isNaN(value) ? a : a + value;
                }, 0)
                .toFixed(2);
            var totalCantFormatted = formatCantidad(totalCant);

            var totalTotalCosto = api
                .column("costo_inicial_total:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    var value = parseFloat(b);
                    return isNaN(value) ? a : a + value;
                }, 0)
                .toFixed(2);
            var totalTotalCostoFormatted =
                "$" + formatCantidadSinCero(totalTotalCosto);

            var totalCostoPromd = api
                .column("cantidad_diferencia:name", { search: "applied" })
                .data()
                .reduce(function (a, b) {
                    var value = parseFloat(b);
                    return isNaN(value) ? a : a + value;
                }, 0)
                .toFixed(2);
            var totalCostoPromdFormatted =
                "" + formatCantidadSinCero(totalCostoPromd);

            $(api.column("stock_fisico_despues:name").footer())
                .html(totalCantFormatted)
                .css("text-align", "right");

            $(api.column("costo_inicial_total:name").footer())
                .html(totalTotalCostoFormatted)
                .css("text-align", "right");

            $(api.column("cantidad_diferencia:name").footer())
                .html(totalCostoPromdFormatted)
                .css("text-align", "right");
        },
    });
}

$(document).ready(function () {
       // Inicializa Select2
    $(".select2").select2({
        theme: "bootstrap-5", // Establece el tema de Bootstrap 5 para select2
        width: "100%",
        allowClear: true,
    });

    // 1) Inicializamos con valores vacíos
    initializeDataTable();

    // 2) Cada vez que cambie cualquier filtro, destruimos y recreamos la tabla
    $("#centrocosto, #categoria, #dateFrom, #dateTo").on("change", function () {
        const filtros = {
            centroId: $("#centrocosto").val(),
            categoriaId: $("#categoria").val(),
            dateFrom: $("#dateFrom").val(),
            dateTo: $("#dateTo").val(),
        };
        dataTable.destroy();
        initializeDataTable(filtros);
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

                    const var_startDateId = document.querySelector("#dateFrom");
                    const var_endDateId = document.querySelector("#dateTo");

                    dataform.append("dateFrom", Number(var_startDateId.value));
                    dataform.append("dateTo", Number(var_endDateId.value));

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

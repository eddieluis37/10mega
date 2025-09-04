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

function formatCantidad(value, decimals = 2) {
    const n = parseFloat(value) || 0;
    return n.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

var dataTable;

function initializeDataTable({
    centroId = "",
    categoriaId = "",
    startDate = "",
    endDate = "",
    vendedorId = "",
    domiciliarioId = "",
} = {}) {
    dataTable = $("#tableInventory").DataTable({
        paging: false,
        pageLength: 150,
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: "/showReportVentasPorProd",
            type: "GET",
            data: {
                centrocosto: centroId,
                categoria: categoriaId,
                startDate: startDate,
                endDate: endDate,
                vendedor: vendedorId,
                domiciliario: domiciliarioId,
            },
        },
        columns: [
            { data: "producto", name: "producto" },
            { data: "lote", name: "lote" },
            {
                data: "cantidad",
                name: "cantidad",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        formatCantidad(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "precio_base",
                name: "precio_base",
                render: function (d, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        formatCantidadSinCero(d) +
                        "</div>"
                    );
                },
            },
            {
                data: "total_base",
                name: "total_base",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        formatCantidadSinCero(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "descuento_productos",
                name: "descuento_productos",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        formatCantidadSinCero(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "descuento_clientes",
                name: "descuento_clientes",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        formatCantidadSinCero(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "total_iva",
                name: "total_iva",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        formatCantidadSinCero(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "total_up",
                name: "total_up",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        formatCantidadSinCero(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "total_ic",
                name: "total_ic",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        formatCantidadSinCero(data) +
                        "</div>"
                    );
                },
            },
            {
                data: "total_venta",
                name: "total_venta",
                render: function (data, type, row) {
                    return (
                        "<div style='text-align: right;'>" +
                        formatCantidadSinCero(data) +
                        "</div>"
                    );
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
            const totalCant = api
                .column("cantidad:name", { search: "applied" })
                .data()
                .reduce((a, b) => a + parseFloat(b || 0), 0);
            $(api.column("cantidad:name").footer()).html(
                formatCantidadSinCero(totalCant)
            );
            const totalPrecioBase = api
                .column("precio_base:name", { search: "applied" })
                .data()
                .reduce((a, b) => a + parseFloat(b || 0), 0);
            $(api.column("precio_base:name").footer()).html(
                formatCantidadSinCero(totalPrecioBase)
            );
            const totalBase = api
                .column("total_base:name")
                .data()
                .reduce((a, b) => a + parseFloat(b || 0), 0);
            $(api.column("total_base:name").footer()).html(
                formatCantidadSinCero(totalBase)
            );
            const totalDescProd = api
                .column("descuento_productos:name")
                .data()
                .reduce((a, b) => a + parseFloat(b || 0), 0);
            $(api.column("descuento_productos:name").footer()).html(
                formatCantidadSinCero(totalDescProd)
            );
            const totalDescCli = api
                .column("descuento_clientes:name")
                .data()
                .reduce((a, b) => a + parseFloat(b || 0), 0);
            $(api.column("descuento_clientes:name").footer()).html(
                formatCantidadSinCero(totalDescCli)
            );
            const totalIVA = api
                .column("total_iva:name")
                .data()
                .reduce((a, b) => a + parseFloat(b || 0), 0);
            $(api.column("total_iva:name").footer()).html(
                formatCantidadSinCero(totalIVA)
            );
            const totalUP = api
                .column("total_up:name")
                .data()
                .reduce((a, b) => a + parseFloat(b || 0), 0);
            $(api.column("total_up:name").footer()).html(
                formatCantidadSinCero(totalUP)
            );
            const totalIC = api
                .column("total_ic:name")
                .data()
                .reduce((a, b) => a + parseFloat(b || 0), 0);
            $(api.column("total_ic:name").footer()).html(
                formatCantidadSinCero(totalIC)
            );
            const totalVenta = api
                .column("total_venta:name")
                .data()
                .reduce((a, b) => a + parseFloat(b || 0), 0);
            $(api.column("total_venta:name").footer()).html(
                formatCantidadSinCero(totalVenta)
            );
        },
    });
}

$(document).ready(function () {
    // 1) Inicializamos con valores vacíos
    $(".select2").select2({
        theme: "bootstrap-5",
        width: "100%",
        allowClear: true,
    });

    initializeDataTable();

    // 2) Cada vez que cambie cualquier filtro, destruimos y recreamos la tabla
    $("#centrocosto, #categoria, #startDate, #endDate, #vendedor, #domiciliario").on(
        "change",
        function () {
            const filtros = {
                centroId: $("#centrocosto").val(),
                categoriaId: $("#categoria").val(),
                startDate: $("#startDate").val(),
                endDate: $("#endDate").val(),
                vendedorId: $("#vendedor").val(),
                domiciliarioId: $("#domiciliario").val(),
            };
            dataTable.destroy();
            initializeDataTable(filtros);
        }
    );
});

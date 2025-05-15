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

function formatCantidad(value, decimals = 2) {
    const n = parseFloat(value) || 0;
    return n.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

var dataTable;

function initializeDataTable({
    centroId = "",
    categoriaId = "",
    startDate = "",
    endDate = "",
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
            },
        },
        columns: [
            { data: "producto", name: "producto" },
            { data: "lote", name: "lote" },
            {
                data: "cantidad",
                name: "cantidad",
                render: (d) => formatCantidad(d),
            },
            {
                data: "precio_base",
                name: "precio_base",
                render: (d) => formatCantidad(d, 0),
            },
            {
                data: "total_base",
                name: "total_base",
                render: (d) => formatCantidad(d, 0),
            },
            {
                data: "descuento_productos",
                name: "descuento_productos",
                render: (d) => formatCantidad(d, 0),
            },
            {
                data: "descuento_clientes",
                name: "descuento_clientes",
                render: (d) => formatCantidad(d, 0),
            },
            {
                data: "total_iva",
                name: "total_iva",
                render: (d) => formatCantidad(d, 0),
            },
            {
                data: "total_up",
                name: "total_up",
                render: (d) => formatCantidad(d, 0),
            },
            {
                data: "total_ic",
                name: "total_ic",
                render: (d) => formatCantidad(d, 0),
            },
            {
                data: "total_venta",
                name: "total_venta",
                render: (d) => formatCantidad(d, 0),
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
                formatCantidad(totalCant)
            );
        },
    });
}

$(document).ready(function () {
    // 1) Inicializamos con valores vacíos
    initializeDataTable();

    // 2) Cada vez que cambie cualquier filtro, destruimos y recreamos la tabla
    $("#centrocosto, #categoria, #startDate, #endDate").on(
        "change",
        function () {
            const filtros = {
                centroId: $("#centrocosto").val(),
                categoriaId: $("#categoria").val(),
                startDate: $("#startDate").val(),
                endDate: $("#endDate").val(),
            };
            dataTable.destroy();
            initializeDataTable(filtros);
        }
    );
});

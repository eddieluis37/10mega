console.log("Starting recibo de caja");
const btnAddCustomerPayment = document.querySelector("#btnAddCustomerPayment");
const formCompensadoRes = document.querySelector("#form-compensado-res");
const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
const btnClose = document.querySelector("#btnModalClose");

const selectCategory = document.querySelector("#categoria");
const selectProvider = document.querySelector("#provider");
const selectCentrocosto = document.querySelector("#centrocosto");
const inputFactura = document.querySelector("#factura");
const sale_id = document.querySelector("#ventaId");
const contentform = document.querySelector("#contentDisable");

// 1) la declaras fuera de cualquier ready()
var tableCompensado;

$(document).ready(function () {
    // 2) aquí inicializas y asignas el DataTable
    tableCompensado = $("#tableCompensado").DataTable({
        paging: true,
        pageLength: 50,
        /*"lengthChange": false,*/
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: "/showlistRecibodecajas",
            type: "GET",
        },
        columns: [
            { data: "id", name: "id" },
            /*  { data: "namethird", name: "namethird" }, */
            {
                data: "namethird",
                name: "namethird",
                render: function (data) {
                    if (data.length > 15) {
                        return data.substring(0, 7) + "...";
                    } else {
                        return data;
                    }
                },
            },
            {
                data: "tipo",
                name: "tipo",
                render: function (data) {
                    if (data === "1") {
                        return '<span title="Recibo de ingreso" style="color: white; background-color: green; border-radius: 5px; padding: 5px; line-height: 1; font-size: 11px;">RI</span>';
                    } else if (data === "2") {
                        return '<span title="Recibo de egreso" style="color: white; background-color: blue; border-radius: 5px; padding: 5px; line-height: 1; font-size: 11px;">RE</span>';
                    } else {
                        return "";
                    }
                },
            },
            { data: "status", name: "status" },
            {
                data: "vr_total_deuda",
                name: "vr_total_deuda",
                render: function (data) {
                    return (
                        "$" +
                        parseFloat(data).toLocaleString(undefined, {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0,
                        })
                    );
                },
            },
            {
                data: "vr_total_pago",
                name: "vr_total_pago",
                render: function (data) {
                    return (
                        "$" +
                        parseFloat(data).toLocaleString(undefined, {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0,
                        })
                    );
                },
            },
            {
                data: "nvo_total_saldo",
                name: "nvo_total_saldo",
                render: function (data) {
                    return (
                        "$" +
                        parseFloat(data).toLocaleString(undefined, {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0,
                        })
                    );
                },
            },
            { data: "date", name: "date" },        
            { data: "action", name: "action" },
        ],
        order: [[0, "DESC"]],
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

    // Inicializa select2
    $(".select2Cliente").select2({
        placeholder: "Busca un cliente",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });
    // Formateador de moneda para COP
    const currencyFormat = new Intl.NumberFormat("es-CO", {
        style: "currency",
        currency: "COP",
        minimumFractionDigits: 0,
    });

    // Antes de todo, expón el arreglo de métodos de pago:
    // Leer la variable inyectada
    const formas = window.formasPago;

    function buildFormaPagoSelect(selected = null) {
        let html =
            '<select class="form-control form-control-sm select2RowFormaPago">';
        html += '<option value="">Seleccione</option>';
        formas.forEach((f) => {
            html += `<option value="${f.id}" ${
                f.id == selected ? "selected" : ""
            }> ${f.nombre}</option>`;
        });
        html += "</select>";
        return html;
    }

    // Ejemplo de carga de datos en la tabla #tablePagoCliente (modifica según tu lógica)
    $("#cliente").on("change", function () {
        var clienteId = $(this).val();
        if (!clienteId) {
            $("#tablePagoCliente tbody").html("");
            updateTotals();
            return;
        }
        $.ajax({
            url: "/getClientPayments",
            method: "GET",
            data: { client_id: clienteId },
            success: function (data) {
                var rows = "";
                $.each(data, function (index, reg) {
                    rows +=
                        "<tr>" +
                        "<td>" +
                        reg.id +
                        "</td>" +
                        "<td>" +
                        reg.FECHA_VENTA +
                        "</td>" +
                        "<td>" +
                        reg.identification_cliente +
                        "</td>" +
                        "<td>" +
                        reg.consecutivo +
                        "</td>" +
                        "<td>" +
                        reg.FECHA_VENCIMIENTO +
                        "</td>" +
                        "<td>" +
                        reg.DIAS_MORA +
                        "</td>" +
                        // VR.DEUDA: Se muestra formateado y se guarda el valor original en data-value
                        "<td class='vr-deuda' data-value='" +
                        reg.deuda_x_cobrar +
                        "'>" +
                        currencyFormat.format(reg.deuda_x_cobrar) +
                        "</td>" +
                        "<td>" +
                        buildFormaPagoSelect() +
                        "</td>" +
                        // VR.PAGO: Input con valor inicial 0, formateado
                        "<td><input type='text' class='form-control vr-pago' value='" +
                        currencyFormat.format(0) +
                        "' style='text-align:right;' size='10' /></td>" +
                        // NVO.SALDO: Se inicia con el valor de la deuda
                        "<td class='nvo-saldo'>" +
                        currencyFormat.format(reg.deuda_x_cobrar) +
                        "</td>" +
                        // Botón PT para pago total
                        "<td><button type='button' class='btn btn-primary btn-sm btn-pt'>PT</button></td>" +
                        "</tr>";
                });
                $("#tablePagoCliente tbody").html(rows);

                // Inicializa Select2 en cada fila:
                $(".select2RowFormaPago").select2({
                    placeholder: "Forma pago",
                    width: "100%",
                    theme: "bootstrap-5",
                    allowClear: true,
                });

                updateTotals();
            },
            error: function (error) {
                console.error("Error al obtener datos:", error);
            },
        });
    });

    // Actualiza el nuevo saldo mientras se digita en VR.PAGO
    $(document).on("input", ".vr-pago", function () {
        var tr = $(this).closest("tr");
        var deuda = parseFloat(tr.find(".vr-deuda").data("value")) || 0;
        var pago = parseFloat($(this).val().replace(/\D/g, "")) || 0;
        var nuevoSaldo = deuda - pago;
        tr.find(".nvo-saldo").text(currencyFormat.format(nuevoSaldo));
        updateTotals();
    });

    // Al salir del input, se formatea el valor
    $(document).on("blur", ".vr-pago", function () {
        var value = $(this).val().replace(/\D/g, "");
        var number = parseFloat(value) || 0;
        $(this).val(currencyFormat.format(number));
        updateTotals();
    });

    // Botón PT: asigna el total de la deuda al input y actualiza el saldo a 0
    $(document).on("click", ".btn-pt", function () {
        var tr = $(this).closest("tr");
        var deuda = parseFloat(tr.find(".vr-deuda").data("value")) || 0;
        tr.find(".vr-pago").val(currencyFormat.format(deuda));
        tr.find(".nvo-saldo").text(currencyFormat.format(0));
        updateTotals();
    });

    // Función para actualizar totales en el footer de la tabla
    function updateTotals() {
        var totalDeuda = 0,
            totalPago = 0,
            totalSaldo = 0;
        $("#tablePagoCliente tbody tr").each(function () {
            var deuda =
                parseFloat($(this).find(".vr-deuda").data("value")) || 0;
            totalDeuda += deuda;
            var pago =
                parseFloat($(this).find(".vr-pago").val().replace(/\D/g, "")) ||
                0;
            totalPago += pago;
            totalSaldo += deuda - pago;
        });
        $("#totalDeuda").text(currencyFormat.format(totalDeuda));
        $("#totalPago").text(currencyFormat.format(totalPago));
        $("#totalSaldo").text(currencyFormat.format(totalSaldo));
    }

    // Manejador para el botón Aceptar con id btnAddCustomerPayment (tipo "button")
    $("#btnAddCustomerPayment").on("click", function (e) {
        e.preventDefault();

        let formData = {
            cliente: $("#cliente").val(),
            tableData: [],
        };

        $("#tablePagoCliente tbody tr").each(function () {
            let $tr = $(this);
            formData.tableData.push({
                id: $tr.find("td").eq(0).text().trim(),
                vr_deuda: parseFloat($tr.find(".vr-deuda").data("value")),
                formaPago: $tr.find(".select2RowFormaPago").val(),
                vr_pago: parseFloat(
                    $tr.find(".vr-pago").val().replace(/\D/g, "")
                ),
                nvo_saldo: parseFloat(
                    $tr.find(".nvo-saldo").text().replace(/\D/g, "")
                ),
            });
        });

        console.log("Datos recolectados del formulario:", formData);

        // Envío de datos vía AJAX
        $.ajax({
            url: $("#form-compensado-res").attr("action"), // La ruta definida en la acción del formulario
            method: "POST",
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                // 1) Mensaje al usuario
                alert(response.success);

                // 2) Cerrar el modal
                $("#modal-create-pagocliente").modal("hide");

                // 3) Al terminar de ocultarse, refrescar tabla y resetear form
                $("#modal-create-pagocliente").one(
                    "hidden.bs.modal",
                    function () {
                        if (response.reloadTable) {
                            // refresca la DataTable sin cambiar de página
                            tableCompensado.ajax.reload(null, false);
                        }
                        // reset del formulario
                        var $form = $("#form-compensado-res");
                        $form[0].reset(); // limpia inputs, textareas y selects nativos
                        // limpia todos los Select2 dentro del form
                        $form.find("select").val(null).trigger("change");
                        // opcional: vuelve a inicializar tus valores de moneda
                        $(".vr-pago").each(function () {
                            $(this).val(currencyFormat.format(0));
                        });
                        // recalcula totales
                        updateTotals();
                    }
                );
            },

            error: function (xhr) {
                console.error("Error en la petición:", xhr);
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMsg = "";
                    $.each(errors, function (key, messages) {
                        errorMsg += messages.join(", ") + "\n";
                    });
                    alert("Errores en el formulario:\n" + errorMsg);
                } else {
                    alert("Error al registrar el pago.");
                }
            },
        });
    });
});

async function openReport(id) {
    try {
        // Realiza la petición al endpoint del reporte
        const response = await fetch(`/reporte-detalle-recibo/${id}`);
        if (!response.ok) {
            throw new Error("Error al cargar el reporte");
        }
        const html = await response.text();
        // Inserta el contenido obtenido en el contenedor del modal
        document.getElementById("reportContent").innerHTML = html;
        // Muestra el modal utilizando Bootstrap 5
        const reportModal = new bootstrap.Modal(
            document.getElementById("reportModal")
        );
        reportModal.show();
    } catch (error) {
        console.error(error);
        alert("Hubo un error al cargar el reporte.");
    }
}

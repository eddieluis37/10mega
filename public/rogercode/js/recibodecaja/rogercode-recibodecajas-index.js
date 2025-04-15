console.log("Starting recibo de caja");
const btnAddVentaDomicilio = document.querySelector("#btnAddVentaDomicilio");
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

$(document).ready(function () {
    $(function () {
        $("#tableCompensado").DataTable({
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
                            return '<span title="Recibo de caja diario" style="color: white; background-color: green; border-radius: 5px; padding: 5px; line-height: 1; font-size: 11px;">RD</span>';
                        } else if (data === "2") {
                            return '<span title="Recibo de caja de cartera" style="color: white; background-color: blue; border-radius: 5px; padding: 5px; line-height: 1; font-size: 11px;">RC</span>';
                        } else {
                            return "";
                        }
                    },
                },
                { data: "status", name: "status" },
                { data: "resolucion_factura", name: "resolucion_factura" },
                {
                    data: "saldo",
                    name: "saldo",
                    render: function (data) {
                        return (
                            "$ " +
                            parseFloat(data).toLocaleString(undefined, {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0,
                            })
                        );
                    },
                },
                {
                    data: "abono",
                    name: "abono",
                    render: function (data) {
                        return (
                            "$ " +
                            parseFloat(data).toLocaleString(undefined, {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0,
                            })
                        );
                    },
                },
                {
                    data: "nuevo_saldo",
                    name: "nuevo_saldo",
                    render: function (data) {
                        return (
                            "$ " +
                            parseFloat(data).toLocaleString(undefined, {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0,
                            })
                        );
                    },
                },
                { data: "date", name: "date" },
                { data: "consecutivo", name: "consecutivo" },

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
    });
});

$(document).ready(function () {
    // Inicializa los select2
    $(".select2Cliente").select2({
        placeholder: "Busca un cliente",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });
    $(".select2FormaPago").select2({
        placeholder: "Busca una forma pago",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });

    // Al cambiar el cliente, se hacen la petición AJAX y se construyen las filas
    $("#cliente").on("change", function () {
        var clienteId = $(this).val();
        if (!clienteId) {
            $("#tablePagoCliente tbody").html("");
            updateTotals();
            return;
        }
        $.ajax({
            url: "/getClientPayments", // Asegúrate de tener la ruta configurada
            method: "GET",
            data: { client_id: clienteId },
            success: function (data) {
                var rows = "";
                $.each(data, function (index, reg) {
                    // Se guarda el valor original de la deuda sin formato para usarlo en los cálculos
                    rows += "<tr>" +
                                "<td>" + reg.id + "</td>" +
                                "<td>" + reg.FECHA_VENTA + "</td>" +
                                "<td>" + reg.identification_cliente + "</td>" +
                                "<td>" + reg.consecutivo + "</td>" +
                                "<td>" + reg.FECHA_VENCIMIENTO + "</td>" +
                                "<td>" + reg.DIAS_MORA + "</td>" +
                                // Columna VR.DEUDA: se muestra el valor formateado y se guarda en un data-attribute
                                "<td class='vr-deuda' data-value='" + reg.deuda_x_cobrar + "'>$" + parseFloat(reg.deuda_x_cobrar).toLocaleString() + "</td>" +
                                // Columna VR.PAGO: input para el pago
                                "<td>$<input type='text' class='form-control vr-pago' value='0' style='text-align:right;' /></td>" +
                                // Columna NVO.SALDO: se inicia con el valor total de la deuda
                                "<td class='nvo-saldo'>$" + parseFloat(reg.deuda_x_cobrar).toLocaleString() + "</td>" +
                                // Columna de acciones: botón para pago total (PT)
                                "<td><button type='button' class='btn btn-primary btn-sm btn-pt'>PT</button></td>" +
                            "</tr>";
                });
                $("#tablePagoCliente tbody").html(rows);
                updateTotals();
            },
            error: function (error) {
                console.error("Error al obtener datos:", error);
            },
        });
    });

    // Evento para actualizar el nuevo saldo cuando el usuario digita un abono
    $(document).on('input', '.vr-pago', function(){
        var tr = $(this).closest('tr');
        var deuda = parseFloat(tr.find('.vr-deuda').data('value')) || 0;
        // Se remueven posibles comas para poder convertir el valor
        var pago = parseFloat($(this).val().replace(/,/g, '')) || 0;
        var nuevoSaldo = deuda - pago;
        tr.find('.nvo-saldo').text("$ " + nuevoSaldo.toLocaleString());
        updateTotals();
    });

    // Botón "PT": asigna el valor total de la deuda al input VR.PAGO y recalcula el nuevo saldo
    $(document).on('click', '.btn-pt', function(){
        var tr = $(this).closest('tr');
        var deuda = parseFloat(tr.find('.vr-deuda').data('value')) || 0;
        tr.find('.vr-pago').val(deuda);
        tr.find('.nvo-saldo').text("$ " + (deuda - deuda).toLocaleString());
        updateTotals();
    });

    // Función para actualizar los totales en el pie de la tabla
    function updateTotals(){
        var totalDeuda = 0, totalPago = 0, totalSaldo = 0;
        $("#tablePagoCliente tbody tr").each(function(){
            var deuda = parseFloat($(this).find('.vr-deuda').data('value')) || 0;
            totalDeuda += deuda;
            var pago = parseFloat($(this).find('.vr-pago').val().replace(/,/g, '')) || 0;
            totalPago += pago;
            totalSaldo += (deuda - pago);
        });
        $("#totalDeuda").text("$ " + totalDeuda.toLocaleString());
        $("#totalPago").text("$ " + totalPago.toLocaleString());
        $("#totalSaldo").text("$ " + totalSaldo.toLocaleString());
    }
});


const send = async (dataform, ruta) => {
    let response = await fetch(ruta, {
        headers: {
            "X-CSRF-TOKEN": token,
        },
        method: "POST",
        body: dataform,
    });
    let data = await response.json();
    //console.log(data);
    return data;
};

const refresh_table = () => {
    let table = $("#tableCompensado").dataTable();
    table.fnDraw(false);
};


const showDataForm = (id) => {
    console.log(id);
    const dataform = new FormData();
    dataform.append("id", id);
    send(dataform, "/saleById").then((resp) => {
        console.log(resp);
        console.log(resp.reg);
        showData(resp);
        $("#provider").prop("disabled", true);
        contentform.setAttribute("disabled", "disabled");
    });
};

const editCompensado = (id) => {
    console.log(id);
    const dataform = new FormData();
    dataform.append("id", id);
    send(dataform, "/saleById").then((resp) => {
        console.log(resp);
        console.log(resp.reg);
        showData(resp);
        if (contentform.hasAttribute("disabled")) {
            contentform.removeAttribute("disabled");
            $("#provider").prop("disabled", false);
        }
    });
};

const showData = (resp) => {
    let register = resp.reg;
    sale_id.value = register.id;
    /*  selectCategory.value = register.categoria_id; */
    $("#provider").val(register.thirds_id).trigger("change");
    selectCentrocosto.value = register.centrocosto_id;
    /*    inputFactura.value = register.factura; */
    const modal = new bootstrap.Modal(
        document.getElementById("modal-create-recibodecaja")
    );
    modal.show();
};

const downCompensado = (id) => {
    swal({
        title: "CONFIRMAR",
        text: "¿CONFIRMAS ELIMINAR EL REGISTRO?",
        type: "warning",
        showCancelButton: true,
        cancelButtonText: "Cerrar",
        cancelButtonColor: "#fff",
        confirmButtonColor: "#3B3F5C",
        confirmButtonText: "Aceptar",
    }).then(function (result) {
        if (result.value) {
            console.log(id);
            const dataform = new FormData();
            dataform.append("id", id);
            send(dataform, "/downmaincompensado").then((resp) => {
                console.log(resp);
                refresh_table();
            });
        }
    });
};

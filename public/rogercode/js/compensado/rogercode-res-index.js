console.log("Starting");
const btnAddLote = document.querySelector("#btnAddlote");
const formLote = document.querySelector("#form-lote");
const btnAddCompensadoRes = document.querySelector("#btnAddCompensadoRes");
const formCompensadoRes = document.querySelector("#form-compensado-res");
const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
const btnClose = document.querySelector("#btnModalClose");

const selectCategory = document.querySelector("#categoria");
const selectProvider = document.querySelector("#provider");
const selectStore = document.querySelector("#store");
const selectFormapago = document.querySelector("#formapago");
const inputFactura = document.querySelector("#factura");
const inputObservacion = document.querySelector("#observacion");
const compensado_id = document.querySelector("#compensadoId");
const contentform = document.querySelector("#contentDisable");

function showModalcreateLote() {
    // Lógica para mostrar el modal
    $('#modal-create-lote').modal('show');
}

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
                url: "/showlistcompensado",
                type: "GET",
            },
            columns: [
                { data: "id", name: "id" },
                {
                    data: "namethird",
                    name: "namethird",
                    render: function (data) {
                        if (data.length > 15) {
                            return `<span title="${data}">${data.substring(0, 7)}.</span>`;
                        } else {
                            return data;
                        }
                    },
                },
                { data: "namestore", name: "namestore" },
                { data: "factura", name: "factura" },
                {
                    data: "valor_total_factura",
                    name: "valor_total_factura",
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
                { data: "fecha_compensado", name: "fecha_compensado" },
                { data: "fecha_ingreso", name: "fecha_ingreso" },
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
    $(".select2Provider").select2({
        placeholder: "Busca un proveedor",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });
    $(".select2Store").select2({
        placeholder: "Busca una bodega",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });
     $(".select2Formapago").select2({
        placeholder: "Busca una formapago",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });
    $(".select2Lote").select2({
        placeholder: "Busca una lote",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });
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
const showModalcreate = () => {
    if (contentform.hasAttribute("disabled")) {
        contentform.removeAttribute("disabled");
        $("#provider").prop("disabled", false);
    }
    $("#provider").val("").trigger("change");
    formCompensadoRes.reset();
    compensado_id.value = 0;
};

const showDataForm = (id) => {
    console.log(id);
    const dataform = new FormData();
    dataform.append("id", id);
    send(dataform, "/compensadoById").then((resp) => {
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
    send(dataform, "/compensadoById").then((resp) => {
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
    compensado_id.value = register.id;
    /*  selectCategory.value = register.categoria_id; */
    $("#provider").val(register.thirds_id).trigger("change");
    $("#store").val(register.store_id).trigger("change");
    $("#lote").val(register.lote_id).trigger("change");
    selectStore.value = register.store_id;
    inputFactura.value = register.factura;
    inputObservacion.value = register.observacion;
    const modal = new bootstrap.Modal(
        document.getElementById("modal-create-compensado")
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


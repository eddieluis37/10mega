console.log("Starting");

$(document).ready(function () {
    // Inicializar select2 en los campos de bodega
    $("#bodegaOrigen, #bodegaDestino").select2({
        theme: "bootstrap-5", // Establece el tema de Bootstrap 5 para select2
        width: "100%",
        placeholder: "Seleccione una opción",
        allowClear: true,
    });

    // Opcional: Evento cuando se cambia la selección
    $("#bodegaOrigen").on("change", function () {
        console.log("Bodega Origen seleccionada:", $(this).val());
    });

    $("#bodegaDestino").on("change", function () {
        console.log("Bodega Destino seleccionada:", $(this).val());
    });
});

const btnAddTransfer = document.querySelector("#btnAddTransfer");
const formTransfer = document.querySelector("#form-transfer");
const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
const btnClose = document.querySelector("#btnModalClose");

//const selectCategory = document.querySelector("#categoria");
const selectCentrocosto = document.querySelector("#bodegaOrigen");
const selectCentrocostoDestino = document.querySelector("#bodegaDestino");

const selectCostcenterOrigin = document.querySelector("#centrocostoorigen");
const selectCostcenterDest = document.querySelector("#centrocostodestino");

const transfer_id = document.querySelector("#transferId");
const contentform = document.querySelector("#contentDisable");

const selectCortePadre = document.querySelector("#selectCortePadre");

const stockActualCenterCostOrigin = document.getElementById(
    "stockActualCenterCostOrigin"
);
const stockActualCenterCostDest = document.getElementById(
    "stockActualCenterCostDest"
);

$(document).ready(initializeDataTable);
function initializeDataTable() {
    $("#tableTransfer").DataTable({
        paging: true,
        pageLength: 50,
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: "/showtransfer",
            type: "GET",
        },
        columns: [
            { data: "id", name: "id" },
            { data: "date", name: "date" },
            { data: "namecentrocostoOrigen", name: "namecentrocostoOrigen" },
            { data: "namecentrocostoDestino", name: "namecentrocostoDestino" },
            { data: "inventory", name: "inventory" },
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
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf"],
    });

    $(".select2corte").select2({
        placeholder: "Busca un producto",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });
}

const showModalcreate = () => {
    if (contentform.hasAttribute("disabled")) {
        contentform.removeAttribute("disabled");
        $(".select2corte").prop("disabled", false);
    }
    $(".select2corte").val("").trigger("change");
    //  selectCortePadre.innerHTML = "";
    formTransfer.reset();
    transfer_id.value = 0;
};

const showDataForm = (id) => {
    console.log(id);
    const dataform = new FormData();
    dataform.append("id", id);
    send(dataform, "/transferById").then((resp) => {
        console.log(resp);
        console.log(resp.reg);
        showData(resp);
        setTimeout(() => {
            $(".select2corte").val(resp.reg.meatcut_id).trigger("change");
        }, 1000);
        $(".select2corte").prop("disabled", true);
        contentform.setAttribute("disabled", "disabled");
    });
};

const showData = (resp) => {
    let register = resp.reg;
    //alistamiento_id.value = register.id;
    // selectCategory.value = register.categoria_id;
    selectCentrocosto.value = register.centrocostoOrigen_id;
    selectCentrocostoDestino.value = register.centrocostoDestino_id;
    getCortes(register.categoria_id);

    const modal = new bootstrap.Modal(
        document.getElementById("modal-create-transfer")
    );
    modal.show();
};

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


function validateCentroCosto() {
    if (centrocostoorigen.value === centrocostodestino.value) {
        alert(
            "El centro de costo origen debe ser diferente al centro de costo destino."
        );
        return false;
    }
    return true;
}

const form = document.getElementById("transferId");
form.addEventListener("submit", function (event) {
    if (!validateCentroCosto()) {
        event.preventDefault(); // Evitar el envío del formulario si la validación falla
    }
});

const downTransfer = (id) => {
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
            send(dataform, "/downmmaintransfer").then((resp) => {
                console.log(resp);
                refresh_table();
            });
        }
    });
};

const refresh_table = () => {
    let table = $("#tableTransfer").dataTable();
    table.fnDraw(false);
};

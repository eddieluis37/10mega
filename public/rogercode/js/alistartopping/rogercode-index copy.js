console.log("Starting");
const btnAddAlistamiento = document.querySelector("#btnAddalistamiento");
const formAlistamiento = document.querySelector("#form-alistamiento");
const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
const btnClose = document.querySelector("#btnModalClose");

const selectStore = document.querySelector("#inputstore");
const selectCategory = document.querySelector("#categoria");
const selectCentrocosto = document.querySelector("#centrocosto");
const alistamiento_id = document.querySelector("#alistamientoId");
const contentform = document.querySelector("#contentDisable");
const selectCortePadre = document.querySelector("#selectCortePadre");
const fechaalistamiento = document.querySelector("#fecha");

$(document).ready(function () {
    // Inicializar Select2
    $(".select2").select2({
        theme: "bootstrap-5", // Establece el tema de Bootstrap 5 para select2
        width: "100%",
        allowClear: true,
    });

    // Evento para cargar lotes al seleccionar una bodega
    $('#inputstore').on('change', function () {
        const storeId = $(this).val();
        $('#inputlote').empty().append('<option value="">Cargando...</option>');
        $('#select2corte').empty().append('<option value="">Seleccione un lote primero</option>');

        if (storeId) {
            $.ajax({
                url: `/get-lotes/${storeId}`,
                type: 'GET',
                success: function (data) {
                    $('#inputlote').empty().append('<option value="">Seleccione un lote</option>');
                    $.each(data, function (key, value) {
                        $('#inputlote').append(`<option value="${key}">${value}</option>`);
                    });
                },
                error: function () {
                    alert('Error al cargar los lotes.');
                },
            });
        }
    });

    // Evento para cargar productos al seleccionar un lote
    $("#inputlote").on("change", function () {
        const loteId = $(this).val();
        $("#select2corte")
            .empty()
            .append('<option value="">Cargando...</option>');

        if (loteId) {
            $.ajax({
                url: `/get-productos/${loteId}`,
                type: "GET",
                success: function (data) {
                    $("#select2corte")
                        .empty()
                        .append(
                            '<option value="">Seleccione un producto</option>'
                        );
                    $.each(data, function (key, value) {
                        $("#select2corte").append(
                            `<option value="${key}">${value}</option>`
                        );
                    });
                },
                error: function () {
                    alert("Error al cargar los productos.");
                },
            });
        }
    });
});



$(document).ready(function () {
    $(function () {
        $("#tableAlistamiento").DataTable({
            paging: true,
            pageLength: 5,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "/showalistartopping",
                type: "GET",
            },
            columns: [
                { data: "id", name: "id" },
                { data: "namebodega", name: "namebodega" },
                { data: "codigolote", name: "codigolote" }, 
                { data: "codigolotehijo", name: "codigolotehijo" },            
                { data: "namecut", name: "namecut" },
                { data: "nuevo_stock_padre", name: "nuevo_stock_padre" },
                { data: "inventory", name: "inventory" },
                { data: "fecha", name: "fecha" },
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
    $(".select2corte").select2({
        placeholder: "Busca un producto",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });
});

const showModalcreate = () => {
    if (contentform.hasAttribute("disabled")) {
        contentform.removeAttribute("disabled");
        $(".select2corte").prop("disabled", false);
    }
    $(".select2corte").val("").trigger("change");
    selectCortePadre.innerHTML = "";
    formAlistamiento.reset();
    alistamiento_id.value = 0;
};

//const editAlistamiento = (id) => {
//console.log(id);
//const dataform = new FormData();
//dataform.append('id', id);
//send(dataform,'/alistamientoById').then((resp) => {
//console.log(resp);
//console.log(resp.reg);
//showData(resp);
//if(contentform.hasAttribute('disabled')){
//contentform.removeAttribute('disabled');
//$('#provider').prop('disabled', false);
//}
//});
//}

const showDataForm = (id) => {
    console.log(id);
    const dataform = new FormData();
    dataform.append("id", id);
    send(dataform, "/alistamientoById").then((resp) => {
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
   // fechaalistamiento.value = register.fecha_alistamiento;
    selectStore.value = register.inputstore;
    selectCategory.value = register.categoria_id;
    selectCentrocosto.value = register.centrocosto_id;
   
    getCortes(register.categoria_id);

    const modal = new bootstrap.Modal(
        document.getElementById("modal-create-alistamiento")
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

selectCategory.addEventListener("change", function () {
    const selectedValue = this.value;
    console.log("Selected value:", selectedValue);
    getCortes(selectedValue);
});

getCortes = (storeId) => {
    const dataform = new FormData();
    dataform.append("categoriaId", Number(storeId));
    send(dataform, "/getproductospadre").then((result) => {
        console.log(result);
        let prod = result.products;
        console.log(prod);
        //showDataTable(result);
        selectCortePadre.innerHTML = "";
        selectCortePadre.innerHTML += `<option value="">Seleccione el producto</option>`;
        // Create and append options to the select element
        prod.forEach((option) => {
            const optionElement = document.createElement("option");
            optionElement.value = option.id;
            optionElement.text = option.name;
            selectCortePadre.appendChild(optionElement);
        });
    });
};

const downAlistamiento = (id) => {
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
            send(dataform, "/downmmainalistamiento").then((resp) => {
                console.log(resp);
                refresh_table();
            });
        }
    });
};

const refresh_table = () => {
    let table = $("#tableAlistamiento").dataTable();
    table.fnDraw(false);
};

console.log("Starting");
const btnAddAlistamiento = document.querySelector("#btnAddalistamiento");
const formAlistamiento = document.querySelector("#form-alistamiento");
const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
const btnClose = document.querySelector("#btnModalClose");

const inputlote = document.querySelector("#inputlote");
const selectStore = document.querySelector("#inputstore");
const selectCategory = document.querySelector("#categoria");
const selectCentrocosto = document.querySelector("#centrocosto");
const alistamiento_id = document.querySelector("#alistamientoId");
const contentform = document.querySelector("#contentDisable");
const selectCortePadre = document.querySelector("#selectCortePadre");
const fechaalistamiento = document.querySelector("#fecha");

// JavaScript para manejar selección de bodega y búsqueda de productos por storeId
// JavaScript para manejar selección de bodega y búsqueda de productos por storeId
$(document).ready(function () {
    // Inicializar Select2 en bodega
    $(".select2").select2({
        placeholder: "Busca bodega",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });

    // Deshabilitar inicialmente el selector de productos
    $(".select2Prod").prop("disabled", true);

    // Al cambiar la bodega
    $("#inputstore").on("change", function () {
        const storeId = $(this).val();
        console.log("Cambio de bodega, storeId:", storeId);

        // Limpiar selector de productos
        $("#producto").val(null).trigger("change");

        if (!storeId) {
            console.log(
                "No hay bodega seleccionada, deshabilitando select2Prod"
            );
            $(".select2Prod").prop("disabled", true);
        } else {
            console.log("Bodega seleccionada, habilitando select2Prod");
            $(".select2Prod").prop("disabled", false);

            // Inicializar Select2 en productos con transporte AJAX personalizado
            $(".select2Prod")
                .select2({
                    placeholder: "Seleccione un producto",
                    theme: "bootstrap-5",
                    width: "100%",
                    allowClear: true,
                    minimumInputLength: 1,
                    ajax: {
                        transport: function (params, success, failure) {
                            const storeId = $("#inputstore").val();
                            console.log(
                                "Petición AJAX, storeId:",
                                storeId,
                                "term:",
                                params.data.term
                            );

                            if (!storeId) {
                                console.log(
                                    "No storeId, devolviendo resultados vacíos"
                                );
                                return success({ results: [] });
                            }

                            return $.ajax({
                                url: `/alistartopping/search/${storeId}`,
                                data: { q: params.data.term },
                                dataType: "json",
                                success: function (data) {
                                    console.log(
                                        "Respuesta AJAX recibida:",
                                        data
                                    );
                                    success(data);
                                },
                                error: function (xhr, status, error) {
                                    console.error(
                                        "Error en AJAX:",
                                        status,
                                        error
                                    );
                                    failure();
                                },
                            });
                        },
                        processResults: function (data) {
                            return {
                                results: data.map((item) => ({
                                    id: item.product_id,
                                    text: item.text,
                                    lote_id: item.lote_id,
                                    inventario_id: item.inventario_id,
                                    stock_ideal: item.stock_ideal,
                                    store_id: item.store_id,
                                    store_name: item.store_name,
                                })),
                            };
                        },
                    },
                    templateResult: (item) =>
                        item.loading ? item.text : item.text,
                    templateSelection: (item) => item.text,
                    escapeMarkup: (m) => m,
                })
                .on("select2:select", function (e) {
                    const d = e.params.data;
                    console.log("Producto seleccionado:", d);
                    $("#lote_id").val(d.lote_id);
                    $("#inventario_id").val(d.inventario_id);
                    $("#stock_ideal").val(d.stock_ideal);
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
    $(".select2Prod").select2({
        placeholder: "Busca un producto",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });
});

const showModalcreate = () => {
    if (contentform.hasAttribute("disabled")) {
        contentform.removeAttribute("disabled");
        $(".select2Prod").prop("disabled", false);
    }
    $(".select2Prod").val("").trigger("change");
    formAlistamiento.reset();
    alistamiento_id.value = 0;
};

const showDataForm = (id) => {
    console.log(id);
    const dataform = new FormData();
    dataform.append("id", id);
    send(dataform, "/alistartoppingById").then((resp) => {
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
            send(dataform, "/downmmainalistartopping").then((resp) => {
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

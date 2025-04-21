import { sendData } from "../exportModule/core/rogercode-core.js";
import {
    successToastMessage,
    errorMessage,
} from "../exportModule/message/rogercode-message.js";
import {
    loadingStart,
    loadingEnd,
} from "../exportModule/core/rogercode-core.js";
const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
const formDetail = document.querySelector("#form-detail");
const showRegTbody = document.querySelector("#tbodyDetail");
const tableTransfer = document.querySelector("#tableTransfer");
const tbodyTable = document.querySelector("#tableTransfer tbody");
const tfootTable = document.querySelector("#tableTransfer tfoot");
const stockPadre = document.querySelector("#stockCortePadre");
const pesokg = document.querySelector("#pesokg");

const newStockOrigen = document.querySelector("#newStockOrigen");
const meatcutId = document.querySelector("#meatcutId");
const tableFoot = document.querySelector("#tabletfoot");
const selectProducto = document.getElementById("producto");
const selectCategoria = document.querySelector("#productoCorte");
const btnAddTrans = document.querySelector("#btnAddTransfer");
const transferId = document.querySelector("#transferId");
const kgrequeridos = document.querySelector("#kgrequeridos");
const addShopping = document.querySelector("#addShopping");
const productoPadre = document.querySelector("#productopadreId");
const bodegaOrigen = document.querySelector("#bodegaOrigen");
const bodegaDestino = document.querySelector("#bodegaDestino");
const loteTraslado = document.querySelector("#lote_id");
const categoryId = document.querySelector("#categoryId");

// Obtén el valor del campo
var centrocostoOrigenId = document.getElementById("bodegaOrigen").value;
var centrocostoDestinoId = document.getElementById("bodegaDestino").value;

console.log("origen " + centrocostoOrigenId);
console.log("destino " + centrocostoDestinoId);
console.log("pesokg " + pesokg);
console.log("stockOrigen " + stockOrigen.value);

// Limpia el mensaje de error al modificar el input de cantidad
$("#kgrequeridos").on("input", function () {
    $(this).closest(".form-group").find(".error-message").text("");
});

// 1) Declaramos primero las funciones que vamos a usar en todo el script:
function limpiarCamposOrigen() {
    $("#stockOrigen, #pesoKgOrigen, #costoOrigen, #costoTotalOrigen").val("");
}

function limpiarCamposDestino() {
    $("#stockDestino, #pesoKgDestino, #costoDestino, #costoTotalDestino").val(
        ""
    );
}

// Inicializar select2 en #producto usando AJAX
$(".select2Prod")
    .select2({
        placeholder: "Seleccione un producto o escanee el código de barras",
        theme: "bootstrap-5",
        width: "100%",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: "/transfer/search",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    bodegaOrigen: $("#bodegaOrigen").val(),
                };
            },
            processResults: function (data) {
                return {
                    results: data.map((item) => ({
                        id: item.product_id, // <-- ahora el value será product_id
                        text: item.text,
                        lote_id: item.lote_id,
                        inventario_id: item.inventario_id, // si necesitas usarlo
                        stock_ideal: item.stock_ideal,
                        store_id: item.store_id,
                        store_name: item.store_name,
                    })),
                };
            },
        },
        templateResult: (item) => (item.loading ? item.text : item.text),
        templateSelection: (item) => item.text,
        escapeMarkup: (m) => m,
    })
    // Cuando el usuario selecciona una opción en el Select2
    .on("select2:select", function (e) {
        const d = e.params.data;

        // Guardamos en inputs ocultos para poder leerlos luego
        $("#lote_id").val(d.lote_id);
        $("#inventario_id").val(d.inventario_id);

        // NOTA: ahora d.id === product_id
        actualizarValoresProducto(d.id, d.lote_id);
        actualizarValoresProductoDestino(d.id, d.lote_id);
    });
// Si cambias manualmente el <select> (por ej. quitas la selección)
// y en el change():
$("#producto").on("change", function () {
    const productId = $(this).val(); // ahora es product_id correcto
    const loteId = $("#lote_id").val();
    if (!productId) return limpiarCamposOrigen(), limpiarCamposDestino();
    actualizarValoresProducto(productId, loteId);
    actualizarValoresProductoDestino(productId, loteId);
});

// AJAX para valores de origen, ahora recibe loteId
function actualizarValoresProducto(productId, loteId) {
    $.ajax({
        url: "/obtener-valores-producto",
        type: "GET",
        data: {
            productId: productId,
            bodegaOrigen: $("#bodegaOrigen").val(),
            loteTraslado: loteId, // envías aquí el lote seleccionado
        },
        success: function (response) {
            $("#stockOrigen").val(response.stockOrigen);
            $("#pesoKgOrigen").val(response.fisicoOrigen);
            $("#costoOrigen").val(formatCantidadSinCero(response.costoOrigen));
            $("#costoTotalOrigen").val(
                formatCantidadSinCero(response.costoTotalOrigen)
            );
        },
        error: function () {
            limpiarCamposOrigen();
        },
    });
}

// AJAX para valores de destino, recibe también loteId
function actualizarValoresProductoDestino(productId, loteId) {
    $.ajax({
        url: "/obtener-valores-producto-destino",
        type: "GET",
        data: {
            productId: productId,
            bodegaDestino: $("#bodegaDestino").val(),
            loteTraslado: loteId, // mismo parámetro para el destino
        },
        success: function (response) {
            $("#stockDestino").val(response.stockDestino);
            $("#pesoKgDestino").val(response.fisicoDestino);
            $("#costoDestino").val(
                formatCantidadSinCero(response.costoDestino)
            );
            $("#costoTotalDestino").val(
                formatCantidadSinCero(response.costoTotalDestino)
            );
        },
        error: function () {
            limpiarCamposDestino();
        },
    });
}

/* Insertar registros al tableTransfer del detalle. Se activa al darle enter en KG a trasladar o boton btnAddTransfer */
// ... (tu inicialización de Select2 y funciones AJAX siguen igual)

// Evento de click para agregar el detalle
btnAddTrans.addEventListener("click", async (e) => {
    e.preventDefault();

    const dataform = new FormData(formDetail);
    dataform.append("stockOrigen", stockOrigen.value);
    dataform.append("loteTraslado", loteTraslado.value);
    dataform.append("bodegaOrigen", bodegaOrigen.value);
    dataform.append("bodegaDestino", bodegaDestino.value);
    dataform.append("stockDestino", stockDestino.value);

    sendData("/transfersavedetail", dataform, token)
        .then((result) => {
            console.log(result);
            if (result.status === 1) {
                // 1) Re-renderiza la tabla
                showData(result);

                // 2) Limpia Select2
                $("#producto").val(null).trigger("change");

                // 3) Reset del form y campo kg
                formDetail.reset();
                $('input[name="kgrequeridos"]').val("");

                // 4) Limpia orígenes y destinos
                limpiarCamposOrigen();
                limpiarCamposDestino();

                successToastMessage(result.message);
            } else if (result.status === 0) {
                // Manejo de validaciones
                $.each(result.errors, function (field, messages) {
                    let $input = $('[name="' + field + '"]');
                    let $error = $input
                        .closest(".form-group")
                        .find(".error-message");
                    $error.text(messages[0]).show();
                });
            }
        })
        .catch((err) => {
            console.error(err);
            errorToastMessage("Error al insertar el detalle.");
        });
});

{
    /* <tbody id="tbodyDetail"></tbody> insertado con transfersavedetail a la vista create http://2puracarnes.test:8080/transfer/create/4 */
}

const showData = (data) => {
    let dataAll = data.array;
    console.log(dataAll);
    showRegTbody.innerHTML = "";
    dataAll.forEach((element, indice) => {
        showRegTbody.innerHTML += `
    	    <tr>      	
      	    <td>${element.codigo}</td>
      	    <td>${element.nameprod}</td>
      	    <td>${element.actual_stock_origen}</td>
              <td>
              <input type="text" class="form-control-sm" data-id="${
                  element.products_id
              }" id="${element.id}" value="${
            element.kgrequeridos
        }" placeholder="Ingresar" size="5">
              </td>
      	    <td>${element.nuevo_stock_origen}</td>
      	    
      	    <td>${element.actual_stock_destino}</td>
            <td>${element.nuevo_stock_destino}</td>
            <td>$${formatCantidadSinCero(element.costo_unitario_origen)}</td>
            <td>$${formatCantidadSinCero(element.subtotal_traslado)}</td>
			<td class="text-center">
				<button class="btn btn-dark fas fa-trash" name="btnDownReg" data-id="${
                    element.id
                }" title="Borrar" >
				</button>
			</td>
    	    </tr>
	    `;
    });

    const arrayTotales = data.arrayTotales;
    console.log(arrayTotales);
    tableFoot.innerHTML = `
    <tr>
      <th>Totales</th>
      <td></td><td></td>
      <th>${arrayTotales.kgTotalRequeridos}</th>
      <th>${arrayTotales.newTotalStock}</th>
      <td></td>
      <th>${arrayTotales.newTotalStockDestino}</th>
      <td></td>
      <th>$${formatCantidadSinCero(arrayTotales.totalTraslado)}</th>
      <td class="text-center">
        ${
            isAuthorized
                ? `<button class="btn btn-success btn-sm" id="addShopping">Aceptar_Traslado</button>`
                : ""
        }
      </td>
    </tr>
  `;

    // Asegúrate de que exista el input #newStockOrigen en tu HTML
    const $newStockOrigen = document.querySelector("#newStockOrigen");
    if ($newStockOrigen) {
        // Ahora sí existe newTotalStock: lo sacamos de arrayTotales
        $newStockOrigen.value = arrayTotales.newTotalStock;
    }
};

kgrequeridos.addEventListener("change", function () {
    const enteredValue = Number(kgrequeridos.value);
    console.log("Entered value: " + enteredValue);
    kgrequeridos.value = enteredValue;
});

tableTransfer.addEventListener("keydown", (event) => {
    if (event.key !== "Enter") return;
    event.preventDefault();

    const target = event.target;
    if (target.tagName !== "INPUT" || !target.closest("tr")) return;

    const inputValue = target.value.trim();
    if (!inputValue) return;

    // IDs y datos que ya estabas leyendo
    const detailId = target.id;
    const productoId = target.getAttribute("data-id");
    const transferId = document.querySelector("#transferId").value;
    const bodegaO = document.querySelector("#bodegaOrigen").value;
    const bodegaD = document.querySelector("#bodegaDestino").value;
    const stockO = document.querySelector("#stockOrigen").value;

   

    // Preparamos el FormData con TODO lo que pide el controlador
    const dataform = new FormData();
    dataform.append("id", detailId);
    dataform.append("newkgrequeridos", inputValue);
    dataform.append("transferId", transferId);
    dataform.append("productoId", productoId);
    dataform.append("bodegaOrigen", bodegaO);
    dataform.append("bodegaDestino", bodegaD);
    dataform.append("stockOrigen", stockO);

    // Envío AJAX
    sendData("/transferUpdate", dataform, token)
        .then((result) => {
            if (result.status === 1) {
                showData(result);
                successToastMessage(result.message);
            } else if (result.status === 0) {
                // Validaciones de newkgrequeridos
                $.each(result.errors, function (field, messages) {
                    if (field === "newkgrequeridos") {
                        $(target).siblings(".error-message").text(messages[0]);
                    }
                });
            }
        })
        .catch((error) => {
            console.error("Error al actualizar el detalle:", error);
            errorToastMessage("Error al actualizar el detalle.");
        });
});

tbodyTable.addEventListener("click", (e) => {
    e.preventDefault();
    let element = e.target;
    if (element.name === "btnDownReg") {
        console.log(element);
        console.log(element);
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
                let id = element.getAttribute("data-id");
                console.log(id);
                const dataform = new FormData();
                dataform.append("id", Number(id));
                dataform.append("transferId", Number(transferId.value));
                dataform.append("bodegaOrigen", Number(bodegaOrigen.value));
                dataform.append("bodegaDestino", Number(bodegaDestino.value));
                dataform.append("stockOrigen", stockOrigen.value);
                sendData("/transferdown", dataform, token).then((result) => {
                    console.log(result);
                    showData(result);
                });
            }
        });
    }
});

/* Accciona el boton Cargar Inventario */
tfootTable.addEventListener("click", async (e) => {
    e.preventDefault();
    let element = e.target;

    if (element.id === "addShopping") {
        console.log("Se hizo clic en el botón addShopping");

        try {
            const result = await showConfirmationAlert();

            console.log("Resultado de la alerta:", result);

            if (result.value) {
                console.log("Usuario confirmó la acción, procediendo...");

                loadingStart(element);

                const dataform = new FormData();
                dataform.append("transferId", Number(transferId.value));
                dataform.append("stockOrigen", Number(stockOrigen.value));
                dataform.append("bodegaOrigen", Number(bodegaOrigen.value));
                dataform.append("bodegaDestino", Number(bodegaDestino.value));

                const response = await sendData(
                    "/transferAddShoping",
                    dataform,
                    token
                );
                console.log("Resultado de la petición:", response);

                if (response.status === 1) {
                    loadingEnd(element, "success", "Cargar al inventario");
                    element.disabled = true;
                    window.location.href = `/transfer`;
                } else {
                    loadingEnd(element, "error", "Cargar al inventario");
                    errorMessage(response.message);
                }
            } else {
                console.log("El usuario canceló la acción.");
            }
        } catch (error) {
            console.error("Error en la ejecución:", error);
        }
    }
});

function showConfirmationAlert() {
    return Swal.fire({
        title: "CONFIRMAR",
        text: "¿Estás seguro de que deseas cargar el inventario?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        console.log("Respuesta de SweetAlert2:", result);
        return result;
    });
}

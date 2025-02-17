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
const categoryId = document.querySelector("#categoryId");

// Obtén el valor del campo
var centrocostoOrigenId = document.getElementById("bodegaOrigen").value;
var centrocostoDestinoId = document.getElementById("bodegaDestino").value;

console.log("origen " + centrocostoOrigenId);
console.log("destino " + centrocostoDestinoId);
console.log("pesokg " + pesokg);
console.log("stockOrigen " + stockOrigen.value);

$(document).ready(function () {
    // Inicializa Select2
    $(".select2Lote").select2({
        placeholder: "Busca un producto",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });
    $(".select2Prod").select2({
        placeholder: "Busca un producto",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });

    $("#lote").on("change", function () {
        let loteId = $(this).val(); // Obtiene el ID del lote seleccionado
        let bodegaOrigenId = $("#bodegaOrigen").val(); // Obtiene la bodega de origen

        console.log("Lote seleccionado:", loteId);
        console.log("Bodega de origen:", bodegaOrigenId);

        if (loteId) {
            $.ajax({
                url: "/transfer/get-products-by-lote",
                type: "GET",
                data: { lote_id: loteId, bodega_origen_id: bodegaOrigenId },
                success: function (response) {
                    console.log("Productos recibidos:", response);
                    $("#producto")
                        .empty()
                        .append(
                            '<option value="">Seleccione el producto</option>'
                        );
                    $.each(response, function (index, product) {
                        $("#producto").append(
                            '<option value="' +
                                product.id +
                                '">' +
                                product.name +
                                "</option>"
                        );
                    });
                },
                error: function () {
                    console.log("Error al cargar los productos.");
                },
            });
        } else {
            console.log("No se seleccionó un lote válido.");
            $("#producto")
                .empty()
                .append('<option value="">Seleccione el producto</option>');
        }
    });
});

$(document).ready(function () {
    $("#producto").change(function () {
        var productId = $(this).val();

        // Si no se selecciona un producto, limpiar los campos
        if (!productId) {
            limpiarCamposOrigen();
            limpiarCamposDestino();
            return;
        }

        // Llamar a las funciones para actualizar valores
        actualizarValoresProducto(productId);
        actualizarValoresProductoDestino(productId);
    });
});

// Función para limpiar los campos
function limpiarCamposOrigen() {
    $("#stockOrigen, #pesoKgOrigen, #costoOrigen, #costoTotalOrigen").val("");
}
function limpiarCamposDestino() {
    $("#stockDestino, #pesoKgDestino, #costoDestino, #costoTotalDestino").val(
        ""
    );
}

function actualizarValoresProducto(productId) {
    $.ajax({
        url: "/obtener-valores-producto",
        type: "GET",
        data: {
            productId: productId,
            bodegaOrigen: $("#bodegaOrigen").val(),
            loteTraslado: $("#lote").val(),
        },
        success: function (response) {
            // Actualizar los valores con los datos recibidos
            $("#stockOrigen").val(response.stockOrigen);
            $("#pesoKgOrigen").val(response.fisicoOrigen);
            $("#costoOrigen").val(formatCantidadSinCero(response.costoOrigen));
            $("#costoTotalOrigen").val(
                formatCantidadSinCero(response.costoTotalOrigen)
            );
        },
        error: function () {
            limpiarCamposOrigen(); // Si hay un error, limpiar los campos
        },
    });
}

function actualizarValoresProductoDestino(productId) {
    $.ajax({
        url: "/obtener-valores-producto-destino",
        type: "GET",
        data: {
            productId: productId,
            bodegaDestino: $("#bodegaDestino").val(),
            loteTraslado: $("#lote").val(),
        },
        success: function (response) {
            // Actualizar los valores con los datos recibidos
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
            limpiarCamposDestino(); // Si hay un error, limpiar los campos
        },
    });
}

/* Insertar registros al tableTransfer del detalle. Se activa al darle enter en KG a trasladar o boton btnAddTransfer */
btnAddTrans.addEventListener("click", (e) => {
    e.preventDefault();
    const dataform = new FormData(formDetail);
    dataform.append("stockOrigen", stockOrigen.value);
    dataform.append("bodegaOrigen", bodegaOrigen.value);
    dataform.append("bodegaDestino", bodegaDestino.value);
    dataform.append("stockDestino", stockDestino.value);
    sendData("/transfersavedetail", dataform, token).then((result) => {
        console.log(result);
        if (result.status === 1) {
            $("#producto").val("").trigger("change");
            $("#lote").val("").trigger("change");
            formDetail.reset();
            showData(result);
            limpiarCamposOrigen();
            limpiarCamposDestino();
            successToastMessage(result.message);
        }
        if (result.status === 0) {
            let errors = result.errors;
            console.log(errors);
            $.each(errors, function (field, messages) {
                console.log(field, messages);
                let $input = $('[name="' + field + '"]');
                let $errorContainer = $input
                    .closest(".form-group")
                    .find(".error-message");
                $errorContainer.html(messages[0]);
                $errorContainer.show();
            });
        }
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

    let arrayTotales = data.arrayTotales;
    console.log(arrayTotales);
    tableFoot.innerHTML = "";
    tableFoot.innerHTML += `
	    <tr>
		 <th>Totales</th>  
            <td></td>
		    <td></td>         	   
		    <th>${(arrayTotales.kgTotalRequeridos)}</td>
		    <th>${(arrayTotales.newTotalStock)}</th>
            <td></td>
            <th>${(arrayTotales.newTotalStockDestino)}</th>	
            <td></td>
            <th>$${formatCantidadSinCero(arrayTotales.totalTraslado)}</th>
		    <td class="text-center">
                <button class="btn btn-success btn-sm" id="addShopping">Iniciar_Traslado</button>
            </td>
	    </tr>
    `;
    /*  let newTotalStockPadre = stockOrigen.value - arrayTotales.kgTotalRequeridos;
    newStockOrigen.value = newTotalStockPadre; */
    newStockOrigen.value = newTotalStock;
};

kgrequeridos.addEventListener("change", function () {
    const enteredValue = formatkg(kgrequeridos.value);
    console.log("Entered value: " + enteredValue);
    kgrequeridos.value = enteredValue;
});

tableTransfer.addEventListener("keydown", (event) => {
    if (event.key === "Enter") {
        event.preventDefault();
        const target = event.target;
        // Verificar que se presionó Enter en un input dentro de una fila de la tabla
        if (target.tagName === "INPUT" && target.closest("tr")) {
            const inputValue = target.value.trim();
            if (inputValue === "") {
                return;
            }
            // Extraer los datos necesarios: id del detalle, id del producto, etc.
            const detailId = target.id; // El id del input coincide con el id del detalle
            const productoId = target.getAttribute("data-id");

            // Preparar el objeto FormData con los datos requeridos
            const dataform = new FormData();
            dataform.append("id", detailId);
            dataform.append("newkgrequeridos", inputValue);
            dataform.append("transferId", transferId.value);
            dataform.append("productoId", productoId);
            dataform.append("bodegaOrigen", bodegaOrigen.value);
            dataform.append("bodegaDestino", bodegaDestino.value);
            dataform.append("stockOrigen", stockOrigen.value);

            // Enviar la información vía AJAX a la ruta /transferUpdate
            sendData("/transferUpdate", dataform, token)
                .then((result) => {
                    if (result.status === 1) {
                        // Actualizar la tabla con los detalles y totales actualizados
                        showData(result);
                        successToastMessage(result.message);
                    } else if (result.status === 0) {
                        // Si hay errores, recorremos los errores y los mostramos
                        $.each(result.errors, function (field, messages) {
                            if (field === "newkgrequeridos") {
                                // Mostrar el mensaje de error junto al input que lo causó
                                $(target)
                                    .siblings(".error-message")
                                    .text(messages[0]);
                            }
                        });
                    }
                })
                .catch((error) => {
                    console.error("Error al actualizar el detalle:", error);
                    errorToastMessage("Error al actualizar el detalle.");
                });
        }
    }
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
tfootTable.addEventListener("click", (e) => {
    e.preventDefault();
    let element = e.target;
    console.log(element);
    if (element.id === "addShopping") {
        //added to inventory
        console.log("click");
        loadingStart(element);
        // Preparar los datos a enviar
        const dataform = new FormData();
        dataform.append("transferId", Number(transferId.value));
        dataform.append("stockOrigen", Number(stockOrigen.value));
        dataform.append("bodegaOrigen", Number(bodegaOrigen.value));
        dataform.append("bodegaDestino", Number(bodegaDestino.value));
        
        sendData("/transferAddShoping", dataform, token).then((result) => {
            console.log(result);
            if (result.status == 1) {
                loadingEnd(element, "success", "Cargar al inventario");
                element.disabled = true;
                window.location.href = `/transfer`;
            }
            if (result.status == 0) {
                loadingEnd(element, "success", "Cargar al inventario");
                errorMessage(result.message);
            }
        });
    }
});

document.getElementById("addShopping").addEventListener("click", function () {
    Swal.fire({
        title: "Confirmación",
        text: "¿Desea afectar el inventario?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            // Affect inventory logic here
        }
    });
});

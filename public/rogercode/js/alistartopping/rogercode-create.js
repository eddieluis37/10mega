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
const tableAlistamiento = document.querySelector("#tableAlistamiento");
const tbodyTable = document.querySelector("#tableAlistamiento tbody");
const tfootTable = document.querySelector("#tableAlistamiento tfoot");
const stockPadre = document.querySelector("#stockCortePadre");
const costoPadre = document.querySelector("#costoPadre");
const pesokg = document.querySelector("#pesokg");

const newStockPadre = document.querySelector("#newStockPadre");
const meatcutId = document.querySelector("#meatcutId");
const tableFoot = document.querySelector("#tabletfoot");
const selectProducto = document.getElementById("producto");
const selectCategoria = document.querySelector("#productoCorte");
const btnAddAlist = document.querySelector("#btnAddAlistamiento");
const alistamientoId = document.querySelector("#alistamientoId");
const kgrequeridos = document.querySelector("#kgrequeridos");
const addShopping = document.querySelector("#addShopping");
const productoPadre = document.querySelector("#productopadreId");

const storeId = document.querySelector("#storeId");

$(".select2Prod").select2({
    placeholder: "Busca un producto",
    width: "100%",
    theme: "bootstrap-5",
    allowClear: true,
});
$(".select2ProdHijos").select2({
    placeholder: "Busca topping",
    width: "100%",
    theme: "bootstrap-5",
    allowClear: true,
});
const dataform = new FormData();
dataform.append("categoriaId", Number(meatcutId.value));
sendData("/alistargetproductos", dataform, token).then((result) => {
    console.log(result);
    let prod = result.products;
    console.log(prod);
    selectProducto.innerHTML = "";
    selectProducto.innerHTML += `<option value="">Seleccione el producto</option>`;
    prod.forEach((option) => {
        const optionElement = document.createElement("option");
        optionElement.value = option.id;
        optionElement.text = option.name;
        selectProducto.appendChild(optionElement);
    });
});

btnAddAlist.addEventListener("click", async (e) => {
    e.preventDefault();
    console.log("log");
    const dataform = new FormData(formDetail);
    dataform.append("stockPadre", stockPadre.value);
    dataform.append("costoPadre", costoPadre.value);
    sendData("/alistartoppingsavedetail", dataform, token).then((result) => {
        console.log(result);
        if (result.status == 1) {
            $("#producto").val("").trigger("change");
            formDetail.reset();
            showData(result);
            successToastMessage(result.message);
        }
        if (result.status == 0) {
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

const showData = (data) => {
    let dataAll = data.array;
    console.log(dataAll);
    showRegTbody.innerHTML = "";
    dataAll.forEach((element, indice) => {
        showRegTbody.innerHTML += `
    	    <tr>
      	    <td>${element.id}</td>
      	    <td>${element.code}</td>
      	    <td>${element.nameprod}</td>      
      	    <td>
            <input type="text" class="form-control-sm" data-id="${
                element.products_id
            }" id="${element.id}" value="${
            element.kgrequeridos
        }" placeholder="Ingresar" size="5">
            </td>
             <td>$${formatCantidadSinCero(element.price_fama)}</td>
             <td>$${formatCantidadSinCero(element.total_venta)}</td>
             <td>${(element.porc_venta)}%</td>
             <td>$${formatCantidadSinCero(element.costo_total)}</td>
             <td>$${formatCantidadSinCero(element.costo_kilo)}</td>
             <td>$${formatCantidadSinCero(element.utilidad)}</td>
             <td>${(element.porc_utilidad)}%</td>                         
      	    <td>${(element.newstock)}KG</td>
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
		    <td></td>		   
		    <th>Total</th>		 
            <td></td>
		    <th>${(arrayTotales.kgTotalRequeridos)}KG</td>
            <th>$${formatCantidadSinCero(arrayTotales.totalPrecioMinimo)}</td>
		    <th>$${formatCantidadSinCero(arrayTotales.totalVentaFinal)}</td>
            <th>${(arrayTotales.totalPorcVenta)}%</th>
            <th>$${formatCantidadSinCero(arrayTotales.totalCostoTotal)}</td>
            <th>$${formatCantidadSinCero(arrayTotales.totalCostoKilo)}</td>
            <th>$${formatCantidadSinCero(arrayTotales.totalUtilidad)}</td>
            <th>${(arrayTotales.totalPorcUtilidad)}%</th>                                  
            <th>${(arrayTotales.newTotalStock)}KG</th>
		    <td class="text-center">
                <button class="btn btn-success btn-sm" id="addShopping">Cargar_Inventario</button>
            </td>
	    </tr>
        <tr>	   
            <th></th>
            <th>Merma:${(arrayTotales.merma)}</th>
            <th>%Merma:${(arrayTotales.porcMerma)}%</th>
        </tr>
    `;
 /*    let newTotalStockPadre = stockPadre.value - arrayTotales.kgTotalRequeridos;
    newStockPadre.value = newTotalStockPadre; */
};

kgrequeridos.addEventListener("change", function () {
    const enteredValue = formatCantidad(kgrequeridos.value);
    console.log("Entered value: " + enteredValue);
    kgrequeridos.value = enteredValue;
});

tableAlistamiento.addEventListener("keydown", function (event) {
    if (event.keyCode === 13) {
        const target = event.target;
        console.log(target);
        if (target.tagName === "INPUT" && target.closest("tr")) {
            console.log("Enter key pressed on an input inside a table row");
            console.log(event.target.value);
            console.log(event.target.id);

            const inputValue = event.target.value;
            if (inputValue == "") {
                return false;
            }

            let productoId = target.getAttribute("data-id");
            console.log("prod test id: " + alistamientoId.value);
            console.log(productoId);
            console.log(storeId.value);
            const trimValue = inputValue.trim();
            const dataform = new FormData();
            dataform.append("id", Number(event.target.id));
            dataform.append("newkgrequeridos", Number(trimValue));
            dataform.append("alistamientoId", Number(alistamientoId.value));
            dataform.append("productoId", Number(productoId));
            dataform.append("storeId", Number(storeId.value));
            dataform.append("stockPadre", stockPadre.value);

            sendData("/alistartoppingUpdate", dataform, token).then((result) => {
                console.log(result);
                showData(result);
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
                dataform.append("alistamientoId", Number(alistamientoId.value));
                dataform.append("storeId", Number(storeId.value));
                dataform.append("stockPadre", stockPadre.value);
                sendData("/alistamientodown", dataform, token).then(
                    (result) => {
                        console.log(result);
                        showData(result);
                    }
                );
            }
        });
    }
});

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
                dataform.append("alistamientoId", Number(alistamientoId.value));
                dataform.append("newStockPadre", Number(newStockPadre.value));
                dataform.append("stockPadre", Number(stockPadre.value));
                dataform.append("productoPadre", Number(productoPadre.value));
                dataform.append("storeId", Number(storeId.value));

                const response = await sendData("/alistartoppingAddShoping", dataform, token);
                console.log("Resultado de la petición:", response);
                
                if (response.status === 1) {
                    loadingEnd(element, "success", "Cargar al inventario");
                    element.disabled = true;
                    window.location.href = `/alistartopping`;
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

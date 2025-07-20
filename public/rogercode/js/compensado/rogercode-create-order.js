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
const btnAdd = document.querySelector("#btnAdd");
const showRegTbody = document.querySelector("#tbodyDetail");
let tbodyTable = document.querySelector("#tableDespostere tbody");
const compensado_id = document.querySelector("#compensadoId");
const centrocosto_id = document.querySelector("#centrocosto_id");
const pesokg = document.querySelector("#pesokg");
const pcompra = document.querySelector("#pcompra");
const regDetail = document.querySelector("#regdetailId");
const tableFoot = document.querySelector("#tabletfoot");
const cargarInventarioBtn = document.getElementById("cargarInventarioBtn");

cargarInventarioBtn.addEventListener("click", showConfirmationAlert);

$(".select2Lote").select2({
    placeholder: "Busca un lote",
    width: "100%",
    theme: "bootstrap-5",
    allowClear: true,
});

$(".select2Prod").select2({
    placeholder: "Busca un producto por nombre o código",
    width: "100%",
    theme: "bootstrap-5",
    allowClear: true,
    minimumInputLength: 1,
    ajax: {
        url: "/compensado/search-products",
        dataType: "json",
        delay: 250,
        data: function (params) {
            return {
                q: params.term // Término de búsqueda
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    templateResult: function (item) {
        if (item.loading) return item.text;
        return item.text;
    },
    templateSelection: function (item) {
        if (!item.id) return item.text;
        return item.text;
    }
});

function showConfirmationAlert(element) {
    return swal.fire({
        title: "CONFIRMAR",
        text: "Estas seguro que desea cargar el inventario ?",
        icon: "warning",
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: "Aceptar",
        denyButtonText: `Cancelar`,
    });
}

tbodyTable.addEventListener("click", (e) => {
    e.preventDefault();
    let element = e.target;
    if (element.name === "btnDown") {
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
                dataform.append("compensadoId", Number(compensado_id.value));
                sendData("/compensadodown", dataform, token).then((result) => {
                    console.log(result);
                    showData(result);
                });
            }
        });
    }
    if (element.name === "btnEdit") {
        console.log(element);
        let id = element.getAttribute("data-id");
        console.log(id);
        const dataform = new FormData();
        dataform.append("id", Number(id));
        sendData("/compensadogetByIdOrder", dataform, token).then((result) => {
            console.log(result);
            let editReg = result.reg;
            console.log(editReg);
            regDetail.value = editReg.id;
            precio_cotiza.value = formatCantidadSinCero(editReg.precio_cotiza);
            peso_cotiza.value = (editReg.peso_cotiza);
            $(".select2Prod").val(editReg.products_id).trigger("change");
            $(".select2Lote").val(editReg.lote_id).trigger("change");
        });
    }
});

// Bandera para controlar el estado de envío
let isSubmitting = false;

btnAdd.addEventListener("click", (e) => {
    e.preventDefault();

    // Si ya se está procesando un envío, se cancela el siguiente
    if (isSubmitting) return;

    // Marcar como en proceso y deshabilitar el botón para prevenir nuevos clicks
    isSubmitting = true;
    btnAdd.disabled = true;

    const dataform = new FormData(formDetail);

    sendData("/compensadosavedetailorder", dataform, token)
        .then((result) => {
            console.log(result);
            if (result.status === 1) {
                // Reiniciar el valor de regdetailId para que en la próxima acción se cree un nuevo detalle
                $("#regdetailId").val("0");
                $("#producto").val("").trigger("change");
                $("#lote").val("").trigger("change");
                formDetail.reset();
                showData(result);
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
        })
        .catch((error) => {
            console.error("Error en la petición:", error);
        })
        .finally(() => {
            // Una vez finalizada la petición, se habilita nuevamente el botón y se resetea la bandera
            isSubmitting = false;
            btnAdd.disabled = false;
        });
});

//<td>${formatDate(element.created_at)}</td>

const showData = (data) => {
    let dataAll = data.array;
    console.log(dataAll);
    showRegTbody.innerHTML = "";
    dataAll.forEach((element, indice) => {
        showRegTbody.innerHTML += `
            <tr>             
                <td>${element.codigo}</td>                    
                <td>${element.nameprod}</td>
                <td>$${formatCantidadSinCero(element.precio_cotiza)}</td>
                <td>${element.peso_cotiza}</td>
                <td>$${formatCantidadSinCero(element.subtotal_cotiza)}</td>
                <td>${element.iva}</td>
                <td class="text-center">
                    <button class="btn btn-dark fas fa-edit" data-id="${
                        element.id
                    }" name="btnEdit" title="Editar"></button>
                    <button class="btn btn-dark fas fa-trash" name="btnDown" data-id="${
                        element.id
                    }" title="Borrar"></button>
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
            <th>${(arrayTotales.pesoTotalGlobal)}</td>
            <th>$${formatCantidadSinCero(arrayTotales.totalGlobal)}</th>
            <td></td>
            
        </tr>
    `;

    tableFoot.addEventListener("click", (e) => {
        e.preventDefault();
        let element = e.target;
        console.log(element);
        if (element.id === "cargarInventarioBtn") {
            showConfirmationAlert(element)
                .then((result) => {
                    if (result && result.value) {
                        loadingStart(element);
                        const dataform = new FormData();
                        dataform.append(
                            "compensadoId",
                            Number(compensado_id.value)
                        );
                        return sendData("/compensadoInvres", dataform, token);
                    }
                })
                .then((result) => {
                    console.log(result);
                    if (result && result.status == 1) {
                        loadingEnd(
                            element,
                            "success",
                            "Cargando al inventorio"
                        );
                        element.disabled = true;
                        return swal(
                            "EXITO",
                            "Inventario Cargado Exitosamente",
                            "success"
                        );
                    }
                    if (result && result.status == 0) {
                        loadingEnd(
                            element,
                            "success",
                            "Cargando al inventorio"
                        );
                        errorMessage(result.message);
                    }
                })
                .then(() => {
                    window.location.href = "/compensado";
                })
                .catch((error) => {
                    console.error(error);
                });
        }
    });
};

pcompra.addEventListener("change", function () {
    const enteredValue = formatMoneyNumber(pcompra.value);
    console.log("Entered value: " + enteredValue);
    pcompra.value = formatCantidadSinCero(enteredValue);
});
/* 
pesokg.addEventListener("change", function () {
    const enteredValue = formatCantidad(pesokg.value);
    console.log("Entered value: " + enteredValue);
    pesokg.value = enteredValue;
});
 */
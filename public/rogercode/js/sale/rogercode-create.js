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
const venta_id = document.querySelector("#ventaId");
const quantity = document.querySelector("#quantity");
const price = document.querySelector("#price");
const iva = document.querySelector("#iva");
const regDetail = document.querySelector("#regdetailId");
const tableFoot = document.querySelector("#tabletfoot");
const cargarInventarioBtn = document.getElementById("cargarInventarioBtn");
const btnRemove = document.querySelector("#btnRemove");

var centrocosto = document.getElementById("centrocosto").value;
console.log("centro " + centrocosto);

var cliente = document.getElementById("cliente").value;
console.log("cliente " + cliente);

$(document).ready(function () {
    // Inicializar select2 usando AJAX para buscar productos
    $(".select2Prod").select2({
        placeholder: "Seleccione un producto o escanee el código de barras",
        theme: "bootstrap-5",
        width: "100%",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: "/products/search",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // Término de búsqueda
                };
            },
            processResults: function (data) {
                // Como el controlador retorna un id único (inventario_id) en el campo "id",
                // simplemente se pasan los resultados al select2
                return {
                    results: data,
                };
            },
            cache: true,
        },
        templateResult: function (item) {
            if (item.loading) return item.text;
            // Aquí se puede personalizar la plantilla de cada opción si es necesario
            return item.text;
        },
        templateSelection: function (item) {
            if (!item.id) return item.text;
            return item.text;
        },
        escapeMarkup: function (markup) {
            return markup; // Permite renderizar HTML en las plantillas
        },
    });

    // Al seleccionar una opción se extraen y asignan los valores a los campos ocultos
    $("#producto").on("select2:select", function (e) {
        var data = e.params.data;
        console.log("Opción seleccionada:", data);

        $("#lote_id").val(data.lote_id || "");
        $("#inventario_id").val(data.inventario_id || "");
        $("#stock_ideal").val(data.stock_ideal || "");
        $("#store_id").val(data.store_id || "");
        $("#store_name").val(data.store_name || "");

        // Se utiliza el id del producto (product_id) para actualizar otros valores relacionados
        actualizarValoresProducto(data.product_id, data.lote_id);
    });

    // Limpia el mensaje de error al modificar el input de cantidad
    $("#quantity").on("input", function () {
        $(this).closest(".form-group").find(".error-message").text("");
    });
});

function actualizarValoresProducto(productId, loteId) {
    $.ajax({
        url: "/sa-obtener-precios-producto",
        type: "GET",
        data: {
            productId: productId,
            loteId: loteId, // Se envía el lote_id seleccionado
            centrocosto: $("#centrocosto").val(), // Obtén el valor del campo centrocosto
            cliente: $("#cliente").val(), // Obtén el valor del campo centrocosto
        },
        success: function (response) {
            // Actualiza los valores en los campos de entrada del centro de costo

            const formattedPrice = formatCantidadSinCero(response.precio);

            $("#price").val(formattedPrice);
            $("#porc_iva").val(response.iva);
            $("#porc_otro_impuesto").val(response.otro_impuesto);
            $("#impoconsumo").val(response.impoconsumo);
            $("#porc_descuento").val(response.porc_descuento);
        },
        error: function (xhr, status, error) {
            // Maneja el error si la solicitud AJAX falla
            console.log(error);
        },
    });
}

$(document).ready(function () {
    $(".select2Prod").select2({
        placeholder: "Seleccione un producto",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
        ajax: {
            url: "/products/search",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // El término de búsqueda (puede ser nombre o código de barras)
                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
            cache: true,
        },
        placeholder: "Seleccione un producto o escanee el código de barras",
        minimumInputLength: 1,
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });
});

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
                dataform.append("ventaId", Number(venta_id.value));
                sendData("/ventadown", dataform, token).then((result) => {
                    console.log(result);
                    showData(result);
                });
            }
        });
    }

    if (element.name === "btnEdit") {
        console.log(element);
        let id = element.getAttribute("data-id");
        const dataform = new FormData();
        dataform.append("id", Number(id));
        sendData("/saleById", dataform, token).then((result) => {
            console.log(result);
            let editReg = result.reg;
            console.log(editReg);
            // Asignar datos a los campos del formulario
            regDetail.value = editReg.id;
            price.value = formatCantidadSinCero(editReg.price);
            quantity.value = editReg.quantity;

            // Usar inventario_id en el select2, no product_id
            let select = $(".select2Prod");
            if (
                select.find("option[value='" + editReg.inventario_id + "']")
                    .length
            ) {
                // Si la opción ya existe, se asigna el valor y se dispara el cambio
                select.val(editReg.inventario_id).trigger("change");
            } else {
                // Si no existe, se crea la opción usando el texto del registro o un valor por defecto
                let newOption = new Option(
                    editReg.text || "Producto editado",
                    editReg.inventario_id,
                    true,
                    true
                );
                select.append(newOption).trigger("change");
            }
        });
    }
});

btnAdd.addEventListener("click", (e) => {
    e.preventDefault();
    const dataform = new FormData(formDetail);
    sendData("/salesavedetail", dataform, token).then((result) => {
        console.log(result);
        if (result.status === 1) {
            // Reiniciar el valor de regdetailId para que en la próxima acción se cree un nuevo detalle
            $("#regdetailId").val("0");
            $("#producto").val("").trigger("change");
            formDetail.reset();
            showData(result);

            // Recarga la pagina para evitar que se renombren productos en la edición
            //  window.location.reload();
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

const showData = (data) => {
    let dataAll = data.array;
    console.log(dataAll);
    showRegTbody.innerHTML = "";
    dataAll.forEach((element, indice) => {
        showRegTbody.innerHTML += `
            <tr>                              
                <td>${element.nameprod}</td>
                <td>${element.quantity}</td>
                <td>$${formatCantidadSinCero(element.price)}</td> 
                <td>${formatCantidadSinCero(
                    element.porc_desc
                )}</td>                 
                <td>$${formatCantidadSinCero(element.descuento)}</td> 
                <td>$${formatCantidadSinCero(element.descuento_cliente)}</td>
                <td>$${formatCantidadSinCero(element.total_bruto)}</td>   
                <td>${formatCantidadSinCero(element.porc_iva)}</td> 
                <td>$${formatCantidadSinCero(element.iva)}</td> 
                <td>${formatCantidadSinCero(
                    element.porc_otro_impuesto
                )}</td>     
                <td>$${formatCantidadSinCero(element.otro_impuesto)}</td>   
                <td>${formatCantidadSinCero(element.porc_impoconsumo)}</td> 
                <td>$${formatCantidadSinCero(
                    element.impoconsumo
                )}</td>               
                <td>$${formatCantidadSinCero(element.total)}</td>        
                <td class="text-center">
                    <button class="btn btn-dark fas fa-edit" data-id="${
                        element.id
                    }" name="btnEdit" title="EditarJS"></button>
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
            <td></td>    
            <td></td>
            <td></td>                               
            <th>$${formatCantidadSinCero(arrayTotales.TotalBruto)}</th> 
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>          
            <th>$${formatCantidadSinCero(
                arrayTotales.TotalValorAPagar
            )}</th>            
            <td class="text-center">
            
            </td>
        </tr>
    `;

    function showConfirmationAlert(element) {
        return swal.fire({
            title: "CONFIRMAR",
            text: "Estas seguro que desea facturar ?",
            icon: "warning",
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: "Aceptar",
            denyButtonText: `Cancelar`,
        });
    }
};

price.addEventListener("change", function () {
    const enteredValue = formatMoneyNumber(price.value);
    console.log("Entered value: " + enteredValue);
    price.value = formatCantidadSinCero(enteredValue);
});

quantity.addEventListener("change", function () {
    const enteredValue = Number(quantity.value);
    console.log("Valor ingresado: " + enteredValue);
    quantity.value = enteredValue;
});

// Get the current date
const date = new Date();

// Create a dynamic password by combining letters and the current date
const passwordHoy =
    "admin" + date.getFullYear() + (date.getMonth() + 1) + date.getDate();

btnRemove.addEventListener("click", (e) => {
    e.preventDefault();
    const priceInput = document.querySelector("#price");
    const passwordInput = document.querySelector("#password");
    const password = passwordInput.value;

    // Check if the password is correct
    if (password === passwordHoy) {
        // Disable the readonly attribute of the price input field
        priceInput.removeAttribute("readonly");
    } else {
        // Set the readonly attribute of the price input field
        priceInput.setAttribute("readonly", true);
        // Display an error message
        alert("Contraseña incorrecta");
    }
});

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

/* $(".select2Prod").select2({
    placeholder: "Busca un producto",
    width: "100%",
    theme: "bootstrap-5",
    allowClear: true,
});
 */
/* 
$(document).ready(function () {
    $("#producto").change(function () {
        var productId = $(this).val();
        // Extrae el atributo data-lote-id de la opción seleccionada
        var loteId = $(this).find(":selected").data("lote-id");
        console.log("loteId seleccionado:", loteId); // Para verificar en consola
        // Asigna el valor al campo oculto, si lo utilizas en el formulario
        $("#lote_id").val(loteId);
        
        // Llama a la función para actualizar los valores del producto y enviar loteId
        actualizarValoresProducto(productId, loteId);
    });
}); */

$(document).ready(function(){
    $('#producto').on('change', function(){
        var productId = $(this).val();
        var selectedOption = $(this).find('option:selected');
        var loteId       = selectedOption.data('lote-id');
        var inventarioId = selectedOption.data('inventario-id');
        var stockIdeal   = selectedOption.data('stock-ideal');
        var storeId      = selectedOption.data('store-id');
        var storeName    = selectedOption.data('store-name');

        // Mostrar en consola para verificar
        console.log('Lote ID:', loteId);
        console.log('Inventario ID:', inventarioId);
        console.log('Stock Ideal:', stockIdeal);
        console.log('Store ID:', storeId);
        console.log('Store Name:', storeName);

        // Asignar los valores a los campos ocultos
        $('#lote_id').val(loteId);
        $('#inventario_id').val(inventarioId);
        $('#stock_ideal').val(stockIdeal);
        $('#store_id').val(storeId);
        $('#store_name').val(storeName);

         // Llama a la función para actualizar los valores del producto y enviar loteId
         actualizarValoresProducto(productId, loteId);

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
    $(".select2Store").select2({
        placeholder: "Seleccione una bodega",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });

    $(".select2Prod").select2({
        placeholder: "Seleccione un producto",
        width: "100%",
        theme: "bootstrap-5",
        allowClear: true,
    });

    $("#store").change(function () {
        var storeId = $(this).val();
       // $("#producto").empty().trigger("change"); // Limpiar productos

        if (storeId) {
            $.ajax({
                url: "/get-products-by-store",
                type: "GET",
                data: { store_id: storeId },
                success: function (data) {
                    var newOptions = [
                        { id: "", text: "Seleccione un producto" },
                    ].concat(data);
                    $("#producto").select2({
                        data: newOptions,
                        width: "100%",
                        theme: "bootstrap-5",
                        allowClear: true,
                    });
                },
                error: function () {
                    console.log("Error al obtener los productos");
                },
            });
        }
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
        console.log(id);
        const dataform = new FormData();
        dataform.append("id", Number(id));
        sendData("/saleById", dataform, token).then((result) => {
            console.log(result);
            let editReg = result.reg;
            console.log(editReg);
            regDetail.value = editReg.id;
            price.value = formatCantidadSinCero(editReg.price);
            quantity.value = formatCantidad(editReg.quantity);

            $(".select2Prod").val(editReg.product_id).trigger("change");
        });
    }
});

btnAdd.addEventListener("click", (e) => {
    e.preventDefault();
    const dataform = new FormData(formDetail);
    sendData("/salesavedetail", dataform, token).then((result) => {
        console.log(result);
        if (result.status === 1) {
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
                <td>${formatCantidad(element.quantity)}KG</td>
                <td>$${formatCantidadSinCero(element.price)}</td> 
                <td>${formatCantidad(element.porc_desc)}%</td>                 
                <td>$${formatCantidadSinCero(element.descuento)}</td> 
                <td>$${formatCantidadSinCero(element.descuento_cliente)}</td>
                <td>$${formatCantidadSinCero(element.total_bruto)}</td>   
                <td>${formatCantidad(element.porc_iva)}%</td> 
                <td>$${formatCantidadSinCero(element.iva)}</td> 
                <td>${element.porc_otro_impuesto}%</td>     
                <td>$${formatCantidadSinCero(element.otro_impuesto)}</td>   
                <td>${element.porc_impoconsumo}%</td> 
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
    const enteredValue = formatPeso(quantity.value);
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

const codigoBarrasInput = document.querySelector("#codigoBarras");
codigoBarrasInput.addEventListener("input", function () {
    const codigoBarras = codigoBarrasInput.value;
    console.log("Código de barras escaneado:", codigoBarras); // Imprimir el código de barras en la consola
    if (codigoBarras.length === 13) {
        // Longitud típica de un código de barras EAN-13
        // Realiza una solicitud AJAX para buscar el producto por el código de barras
        buscarProductoPorCodigoBarras(codigoBarras);
    }
});

function buscarProductoPorCodigoBarras(codigoBarras) {
    $.ajax({
        url: "/buscar-producto-por-codigo-barras",
        type: "GET",
        data: {
            codigoBarras: codigoBarras,
        },
        success: function (response) {
            // Actualiza los valores en el formulario con la información del producto encontrado
            $("#producto").val(response.producto_id).trigger("change");
            // Otras actualizaciones de campos según la respuesta
        },
        error: function (xhr, status, error) {
            console.log(error);
        },
    });
}

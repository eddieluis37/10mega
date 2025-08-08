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

$(function () {
    // Inicializar selects básicos
    $(".select2").select2({
        theme: "bootstrap-5",
        width: "100%",
        allowClear: true,
    });

    // Recarga bodegas cuando cambia centro
    $("#inputcentro").on("change", function () {
        const centro = $(this).val();
        const $store = $("#inputstore");
        $store
            .empty()
            .append('<option value="">Todas las bodegas</option>')
            .trigger("change");

        $.ajax({
            url: centro ? "/getStores" : "/getStoresAll",
            data: centro ? { centroId: centro } : {},
            success(data) {
                data.forEach((s) => $store.append(new Option(s.name, s.id)));
                $store.trigger("change");
            },
        });
    });

    // Inicializar select2Prod con AJAX, enviando storeId y categoryId
    $(".select2Prod").select2({
        placeholder: "Seleccione un producto o escanee el código de barras",
        theme: "bootstrap-5",
        width: "100%",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: "/products/search/promotion",
            dataType: "json",
            delay: 250,
            data: function (params) {
                const storeId = $("#inputstore").val() || null;
                const categoryId = $("#inputcategoria").val(); // puede ser "", "-1", o id numérico

                return {
                    q: params.term,
                    storeId: storeId,
                    categoryId: categoryId,
                };
            },
            processResults: function (data) {
                return { results: data };
            },
            cache: true,
        },
        templateResult: function (item) {
            if (item.loading) return item.text;
            return item.text;
        },
        templateSelection: function (item) {
            if (!item.id) return item.text;
            return item.text;
        },
        escapeMarkup: function (markup) {
            return markup;
        },
    });

    // Al seleccionar producto: llenar campos ocultos
    $("#producto").on("select2:select", function (e) {
        var data = e.params.data;
        $("#lote_id").val(data.lote_id || "");
        $("#inventario_id").val(data.inventario_id || "");
        $("#stock_ideal").val(data.stock_ideal || "");
        $("#store_id").val(data.store_id || "");
        $("#store_name").val(data.store_name || "");
        if (typeof actualizarValoresProducto === "function") {
            actualizarValoresProducto(data.product_id, data.lote_id);
        }
    });

    // Cuando cambie la bodega o la categoría: limpiar select2Prod (forzar nueva búsqueda)
    $("#inputstore, #inputcategoria").on("change", function () {
        $(".select2Prod").val(null).trigger("change");
    });

    // Mantener limpieza de mensajes de error en quantity
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
            $("#porc_impoconsumo").val(response.impoconsumo);
            $("#porc_desc").val(response.porc_descuento);
        },
        error: function (xhr, status, error) {
            // Maneja el error si la solicitud AJAX falla
            console.log(error);
        },
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
            porc_iva.value = editReg.porc_iva;
            porc_otro_impuesto.value = editReg.porc_otro_impuesto;
            porc_impoconsumo.value = editReg.porc_impoconsumo;
            porc_desc.value = editReg.porc_desc;

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

    // 1. Crea FormData a partir del form
    const dataform = new FormData(formDetail);

    // 2. Extrae el bodega del <select> y lo añade explícitamente
    const bodega = document.getElementById("bodega").value;
    console.log("bodega a enviar:", bodega);
    dataform.set("bodega", bodega);
    // (o bien dataform.append("bodega", bodega) si no existiera aún)

    // 3. Envía al método savedetail
    sendData("/salesavedetail", dataform, token)
        .then((result) => {
            console.log("Respuesta savedetail:", result);

            if (result.status === 1) {
                // reset campos
                $("#regdetailId").val("0");
                $("#producto").val("").trigger("change");
                formDetail.reset();
                showData(result);
            }

            if (result.status === 0) {
                let errors = result.errors;
                console.log("Errores validación:", errors);
                $.each(errors, function (field, messages) {
                    let $input = $('[name="' + field + '"]');
                    let $errorContainer = $input
                        .closest(".form-group")
                        .find(".error-message");
                    $errorContainer.html(messages[0]).show();
                });
            }
        })
        .catch((err) => {
            console.error("Error en la petición savedetail:", err);
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

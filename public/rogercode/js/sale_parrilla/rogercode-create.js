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
const price_venta = document.querySelector("#price_venta");
const iva = document.querySelector("#iva");
const regDetail = document.querySelector("#regdetailId");
const tableFoot = document.querySelector("#tabletfoot");
const cargarInventarioBtn = document.getElementById("cargarInventarioBtn");
const btnRemove = document.querySelector("#btnRemove");

var centrocosto = document.getElementById("centrocosto").value;
console.log("centro " + centrocosto);

var cliente = document.getElementById("cliente").value;
console.log("cliente " + cliente);

// Helpers de formato
function parseNumber(value) {
    if (value === null || value === undefined || value === '') return 0;
    // Quitar símbolos de moneda, espacios, miles y comas si se usan como separador de miles
    const v = String(value).replace(/\s/g, '').replace(/\$/g, '').replace(/\./g, '').replace(/,/g, '.');
    // Si venía "1.234.567,89" ya convertimos a "1234567.89"
    const n = parseFloat(v);
    return isNaN(n) ? 0 : n;
}

// Lee saleId expuesto en la vista (input hidden o variable global window.SALE_ID)
const saleId = window.SALE_ID || $("#saleId").val() || null;

$(function () {
    // Inicializar Select2 - un único inicializador, evita duplicados
    $(".select2Prod").select2({
        placeholder: "Seleccione un producto o escanee el código de barras",
        theme: "bootstrap-5",
        width: "100%",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: "/products/search/parrilla",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    q: params.term || "",
                    sale_id: saleId,
                };
            },
            processResults: function (data) {
                // data debe venir en el formato que definimos en el backend
                return { results: data };
            },
            cache: true,
        },
        templateResult: function (item) {
            if (!item) return;
            if (item.loading) return item.text;
            return item.text; // ya viene con formato en backend
        },
        templateSelection: function (item) {
            if (!item) return;
            return item.text || item.product_id || item.id;
        },
        escapeMarkup: function (m) {
            return m;
        },
    });

    // Cuando se selecciona una opción del select2 (inventario -> producto)
    $(".select2Prod").on("select2:select", function (e) {
        const data = e.params.data;
        console.log("Select2 seleccionado:", data);

        // Rellenar campos ocultos del formulario
        $("#lote_id").val(data.lote_id || "");
        $("#inventario_id").val(data.inventario_id || data.id || "");
        $("#stock_ideal").val(data.stock_ideal || "");
        $("#store_id").val(data.store_id || "");
        $("#store_name").val(data.store_name || "");

        // Si el select2 devuelve product_id usa ese, si no intenta derivar del inventario
        const productId = data.product_id || data.productId || null;
        const inventarioId = data.inventario_id || data.id || null;

        // Llamar al endpoint para obtener precios y promociones (cantidad por defecto 1)
        obtenerPreciosYPromo(productId, inventarioId, 1);
    });

    // Al cambiar la cantidad, recalcular promo_value en tiempo real
    $("#quantity").on("input", function () {
        // limpia posibles mensajes de error
        $(this).closest(".form-group").find(".error-message").text("");

        const qty = parseNumber($(this).val());
        const productId = $("#producto").val()
            ? $("#producto").select2("data")[0]?.product_id || null
            : $("#product_id").val() || null;
        const inventarioId = $("#inventario_id").val() || null;

        // Si no hay productId, nada que hacer
        if (!productId) return;

        // Elegimos: -> solicitar al servidor la promo calculada para la cantidad actual,
        // así respetamos reglas exactas (se sobrecarga poco, es seguro).
        obtenerPreciosYPromo(productId, inventarioId, qty);
    });

    // Si el input price es editable y quieres que al cambiar price se recalcule la promo:
    $("#price").on("input", function () {
        const qty = parseNumber($("#quantity").val()) || 1;
        const productId = $("#producto").val()
            ? $("#producto").select2("data")[0]?.product_id || null
            : $("#product_id").val() || null;
        const inventarioId = $("#inventario_id").val() || null;
        if (!productId) return;
        // Recalcular usando server para mantener reglas centrales
        obtenerPreciosYPromo(productId, inventarioId, qty);
    });
});

/**
 * Llama al endpoint /sa-obtener-precios-producto para obtener:
 * - precio unitario (precio)
 * - impuestos (iva, otro_impuesto, impoconsumo)
 * - porc_descuento (de la lista de precio)
 * - promo_percent, promo_min_quantity, promo_value (según quantity enviado)
 *
 * Actualiza campos del formulario: #price, #porc_iva, #porc_otro_impuesto, #porc_impoconsumo,
 * #porc_desc, #promo_percent, #promo_value, #applied_promotion_id, etc.
 */
function obtenerPreciosYPromo(productId, inventarioId, quantity) {
    // Protección básica
    if (!productId) return;

    // Lee valores actuales de centro de costo y cliente
    const centrocosto = $("#centrocosto").val() || null;
    const cliente = $("#cliente").val() || null;

    $.ajax({
        url: "/sa-obtener-precios-producto",
        method: "GET",
        dataType: "json",
        data: {
            productId: productId,
            inventario_id: inventarioId,
            quantity: quantity,
            centrocosto: centrocosto,
            cliente: cliente,
        },
        success: function (resp) {
            // RESPUESTA esperada:
            // { precio, iva, otro_impuesto, impoconsumo, porc_descuento,
            //   promo_percent, promo_min_quantity, promo_value, applied_promotion_id }

            // Precio unitario (para mostrar en input de price)
        
            const formattedPrice = formatCantidadSinCero(resp.precio);
            $("#price").val(formattedPrice);

            const formattedPriceVenta = formatCantidadSinCero(resp.precio_venta);
            $("#price_venta").val(formattedPriceVenta);

            // Impuestos/porcentajes
            $("#porc_iva").val(resp.iva ?? 0);
            $("#porc_otro_impuesto").val(resp.otro_impuesto ?? 0);
            $("#porc_impoconsumo").val(resp.impoconsumo ?? 0);

            // Descuento de lista de precio
            $("#porc_desc").val(resp.porc_descuento ?? 0);

            // Promoción
            const promoPercent = parseFloat(resp.promo_percent || 0);
            const promoMinQ = resp.promo_min_quantity ?? null;
            const promoValue = parseFloat(resp.promo_value || 0);
            const appliedPromotionId = resp.applied_promotion_id || null;

            $("#promo_percent").val(promoPercent.toFixed(2));
            $("#promo_min_quantity").val(promoMinQ ?? "");
            $("#applied_promotion_id").val(appliedPromotionId ?? "");

            // campo visible o label para mostrar valor promoción por línea
            $("#promo_value").val((promoValue)); // ejemplo: input oculto
            $("#promo_value_display").text(
                promoValue ? (promoValue) : "$0.00"
            );

            // También podrías recalcular y mostrar el total pre-visualizado:
            // total_bruto_input = price * quantity
            const qty = parseNumber($("#quantity").val()) || quantity || 1;
           /*  const totalBruto = precioUnitario * qty;
            $("#total_bruto_preview").text((totalBruto)); */
        },
        error: function (xhr, status, error) {
            console.error(
                "Error al obtener precios/promo:",
                error,
                xhr.responseText
            );
            // limpiar campos de promo si se produjo error
            $("#promo_percent").val("0.00");
            $("#promo_value").val("0.00");
            $("#applied_promotion_id").val("");
            $("#promo_value_display").text("$0.00");
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
            price_venta.value = formatCantidadSinCero(editReg.price_venta);
            quantity.value = editReg.quantity;
            porc_iva.value = editReg.porc_iva;
            porc_otro_impuesto.value = editReg.porc_otro_impuesto;
            porc_impoconsumo.value = editReg.porc_impoconsumo;
            porc_desc.value = editReg.porc_desc;
            promo_percent.value = editReg.promo_percent;       

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

    // 2. Extrae el tipobodega del <select> y lo añade explícitamente
    const tipobodega = document.getElementById("tipobodega").value;
    console.log("tipobodega a enviar:", tipobodega);
    dataform.set("tipobodega", tipobodega);
    // (o bien dataform.append("tipobodega", tipobodega) si no existiera aún)

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
                <td>${formatCantidadSinCero(
                    element.promo_percent
                )}</td>
                <td>$${formatCantidadSinCero(element.promo_value)}</td> 
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
                <td>$${formatCantidadSinCero(element.price_venta)}</td>           
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
            <td></td>
            <td></td>                               
            <th>$${formatCantidadSinCero(arrayTotales.TotalBruto)}</th> 
            <td></td>
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

price_venta.addEventListener("change", function () {
    const enteredValue = formatMoneyNumber(price_venta.value);
    console.log("Entered value: " + enteredValue);
    price_venta.value = formatCantidadSinCero(enteredValue);
});


quantity.addEventListener("change", function () {
    const enteredValue = Number(quantity.value);
    console.log("Valor ingresado: " + enteredValue);
    quantity.value = enteredValue;
});

// Get the current date
const date = new Date();

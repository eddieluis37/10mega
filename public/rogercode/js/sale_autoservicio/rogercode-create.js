import { sendData } from "../exportModule/core/rogercode-core.js";
import {
    successToastMessage,
    errorMessage,
} from "../exportModule/message/rogercode-message.js";
import {
    loadingStart,
    loadingEnd,
} from "../exportModule/core/rogercode-core.js";

/* ----------------- CONSTANTES / SELECTORES ----------------- */
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

var centrocosto = document.getElementById("centrocosto") ? document.getElementById("centrocosto").value : "";
console.log("centro " + centrocosto);

var cliente = document.getElementById("cliente") ? document.getElementById("cliente").value : "";
console.log("cliente " + cliente);

/* ----------------- WRAPPERS SEGUROS PARA LOADING ----------------- */
function safeLoadingStart() {
    try {
        if (typeof loadingStart === "function") {
            try { loadingStart(); } catch (err) { console.warn("loadingStart lanzó error:", err); }
        }
    } catch (err) {
        console.warn("safeLoadingStart fallo:", err);
    }
}
function safeLoadingEnd() {
    try {
        if (typeof loadingEnd === "function") {
            try { loadingEnd(); } catch (err) { console.warn("loadingEnd lanzó error:", err); }
        }
    } catch (err) {
        console.warn("safeLoadingEnd fallo:", err);
    }
}
/* ------------------------------------------------------------ */

/* ----------------- HELPERS FORMATO ----------------- */
function parseNumber(value) {
    if (value === null || value === undefined || value === '') return 0;
    const v = String(value).replace(/\s/g, '').replace(/\$/g, '').replace(/\./g, '').replace(/,/g, '.');
    const n = parseFloat(v);
    return isNaN(n) ? 0 : n;
}
function formatMoneyNumber(v) {
    if (v === null || v === undefined) return 0;
    return parseFloat(String(v).replace(/[^0-9,-\.]/g, '').replace(',', '.')) || 0;
}
// fallback para formatCantidadSinCero si no existe
if (typeof formatCantidadSinCero === "undefined") {
    function formatCantidadSinCero(v) {
        if (v === null || v === undefined) return "";
        // intenta formatear números sencillos
        if (!isNaN(Number(v))) return Number(v).toString();
        return String(v);
    }
}
/* ------------------------------------------------- */

// Lee saleId expuesto en la vista
const saleId = window.SALE_ID || ($("#saleId").length ? $("#saleId").val() : null) || null;

/* ----------------- INICIALIZACIÓN SELECT2 ----------------- */
$(function () {
    $(".select2Prod").select2({
        placeholder: "Seleccione un producto o escanee el código de barras",
        theme: "bootstrap-5",
        width: "100%",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: "/products/search/autoservicio",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    q: params.term || "",
                    sale_id: saleId,
                };
            },
            processResults: function (data) {
                return { results: data };
            },
            cache: true,
        },
        templateResult: function (item) {
            if (!item) return;
            if (item.loading) return item.text;
            return item.text;
        },
        templateSelection: function (item) {
            if (!item) return;
            return item.text || item.product_id || item.id;
        },
        escapeMarkup: function (m) {
            return m;
        },
    });

    // Selección manual (usuario hace click y el select2 dispara)
    $(".select2Prod").on("select2:select", function (e) {
        const data = e.params.data;
        console.log("Select2 seleccionado (manual):", data);

        $("#lote_id").val(data.lote_id || "");
        $("#inventario_id").val(data.inventario_id || data.id || "");
        $("#stock_ideal").val(data.stock_ideal || "");
        $("#store_id").val(data.store_id || "");
        $("#store_name").val(data.store_name || "");

        const productId = data.product_id || data.productId || null;
        const inventarioId = data.inventario_id || data.id || null;

        obtenerPreciosYPromo(productId, inventarioId, 1);
    });

    /* ------------- DETECTOR EAN robusto con debounce (sin Enter) ------------- */
    let debounceTimer = null;
    const processedTimestamps = {};
    const MIN_REPROCESS_MS = 300;

    $(document).on("input", ".select2-search__field", function () {
        const $input = $(this);
        const val = $input.val().trim();

        if (debounceTimer) clearTimeout(debounceTimer);

        debounceTimer = setTimeout(function () {
            const eanRegex = /^\d{13}$/;
            if (!eanRegex.test(val)) return;

            const now = Date.now();
            const last = processedTimestamps[val] || 0;
            if (now - last < MIN_REPROCESS_MS) return;
            processedTimestamps[val] = now;

            processEan(val, $input);
        }, 60);
    });

    function processEan(barcode, $searchInput) {
        console.log("EAN detectado (auto):", barcode);
        safeLoadingStart();

        $.ajax({
            url: "/products/search/autoservicio",
            dataType: "json",
            data: {
                q: barcode,
                sale_id: saleId,
            },
            success: function (results) {
                safeLoadingEnd();

                if (!results || !results.length) {
                    if (typeof errorMessage === "function") errorMessage("Producto no encontrado para EAN: " + barcode);
                    try { $searchInput.val("").focus(); } catch (e) {}
                    return;
                }

                const data = results[0];
                const inventarioId = data.inventario_id ?? data.id ?? data.product_id ?? null;
                const productId = data.product_id ?? data.productId ?? null;
                const optionText = data.text || data.nameprod || data.label || ("Producto " + (inventarioId || productId));
                const $select = $(".select2Prod").first();

                // Seleccionar o crear opción Select2
                try {
                    if (inventarioId === null || inventarioId === undefined) {
                        console.warn("InventarioId nulo para EAN:", barcode, data);
                    }
                    if ($select.find("option[value='" + inventarioId + "']").length) {
                        $select.val(inventarioId).trigger("change");
                    } else {
                        const newOption = new Option(optionText, inventarioId, true, true);
                        $select.append(newOption).trigger("change");
                    }
                } catch (err) {
                    console.warn("Error seleccionando/creando option Select2:", err);
                    try {
                        $select.append('<option value="' + inventarioId + '" selected>' + optionText + '</option>');
                        $select.val(inventarioId).trigger("change");
                    } catch (e) { console.error(e); }
                }

                // Rellenar campos escondidos
                $("#lote_id").val(data.lote_id || "");
                $("#inventario_id").val(inventarioId || "");
                $("#product_id").val(productId || "");
                $("#stock_ideal").val(data.stock_ideal || "");
                $("#store_id").val(data.store_id || "");
                $("#store_name").val(data.store_name || "");

                // Cantidad por defecto 1
                $("#quantity").val(1);

                // Obtener precios y luego agregar programáticamente
                obtenerPreciosYPromo(productId, inventarioId, 1, function (resp, err) {
                    setTimeout(function () {
                        try { $searchInput.val(""); $searchInput.focus(); } catch (e) {}

                        agregarDetalleProgramatico()
                            .then((result) => {
                                if (result && result.status === 1) {
                                    if (typeof successToastMessage === "function") successToastMessage("Producto agregado");
                                } else if (result && result.status === 0) {
                                    if (typeof errorMessage === "function") errorMessage("No se pudo agregar el producto: revisar validaciones");
                                }
                            })
                            .catch((err) => {
                                console.error("Error agregando detalle programáticamente:", err);
                                if (typeof errorMessage === "function") errorMessage("Error agregando producto");
                            });
                    }, 80);
                });
            },
            error: function (xhr) {
                safeLoadingEnd();
                console.error("Error AJAX buscando EAN:", xhr);
                if (typeof errorMessage === "function") errorMessage("Error buscando producto por EAN");
            },
        });
    }
    /* ------------------ fin detector EAN ------------------ */

    // Cambios en cantidad: recalcular precios/promo
    $("#quantity").on("input", function () {
        const qty = parseNumber($(this).val());
        const productId = $("#producto").val()
            ? $("#producto").select2("data")[0]?.product_id || null
            : $("#product_id").val() || null;
        const inventarioId = $("#inventario_id").val() || null;
        if (!productId && !inventarioId) return;
        obtenerPreciosYPromo(productId, inventarioId, qty);
    });

    $("#price").on("input", function () {
        const qty = parseNumber($("#quantity").val()) || 1;
        const productId = $("#producto").val()
            ? $("#producto").select2("data")[0]?.product_id || null
            : $("#product_id").val() || null;
        const inventarioId = $("#inventario_id").val() || null;
        if (!productId && !inventarioId) return;
        obtenerPreciosYPromo(productId, inventarioId, qty);
    });
});
/* ----------------- FIN $(function) ----------------- */

/* ----------------- obtenerPreciosYPromo (con callback) ----------------- */
function obtenerPreciosYPromo(productId, inventarioId, quantity, callback) {
    if (!productId && !inventarioId) {
        if (typeof callback === "function") callback(null, { error: true, message: "Falta productId/inventarioId" });
        return;
    }

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
            try {
                const formattedPrice = typeof formatCantidadSinCero === "function" ? formatCantidadSinCero(resp.precio) : (resp.precio ?? "");
                $("#price").val(formattedPrice);

                const formattedPriceVenta = typeof formatCantidadSinCero === "function" ? formatCantidadSinCero(resp.precio_venta) : (resp.precio_venta ?? "");
                $("#price_venta").val(formattedPriceVenta);

                $("#porc_iva").val(resp.iva ?? 0);
                $("#porc_otro_impuesto").val(resp.otro_impuesto ?? 0);
                $("#porc_impoconsumo").val(resp.impoconsumo ?? 0);

                $("#porc_desc").val(resp.porc_descuento ?? 0);

                const promoPercent = parseFloat(resp.promo_percent || 0);
                const promoMinQ = resp.promo_min_quantity ?? null;
                const promoValue = parseFloat(resp.promo_value || 0);
                const appliedPromotionId = resp.applied_promotion_id || null;

                $("#promo_percent").val(isNaN(promoPercent) ? "0.00" : promoPercent.toFixed(2));
                $("#promo_min_quantity").val(promoMinQ ?? "");
                $("#applied_promotion_id").val(appliedPromotionId ?? "");

                $("#promo_value").val((promoValue));
                $("#promo_value_display").text(promoValue ? (promoValue) : "$0.00");
            } catch (err) {
                console.error("Error aplicando resp de precios:", err, resp);
            }

            if (typeof callback === "function") callback(resp);
        },
        error: function (xhr, status, error) {
            console.error("Error al obtener precios/promo:", error, xhr && xhr.responseText);
            $("#promo_percent").val("0.00");
            $("#promo_value").val("0.00");
            $("#applied_promotion_id").val("");
            $("#promo_value_display").text("$0.00");
            if (typeof callback === "function") callback(null, { error: true, xhr: xhr });
        },
    });
}
/* ----------------------------------------------------------------------- */

/* ----------------- agregarDetalleProgramatico ----------------- */
function agregarDetalleProgramatico() {
    return new Promise((resolve, reject) => {
        try {
            const dataform = new FormData(formDetail);

            // Forzar campos importantes que el backend suele esperar
            const tipobodegaEl = document.getElementById("tipobodega");
            if (tipobodegaEl) dataform.set("tipobodega", tipobodegaEl.value);

            // Forzar quantity = 1 (si acaso)
            dataform.set("quantity", String($("#quantity").val() || 1));

            // Asegurar inventario_id / product_id / producto si faltan
            const inventarioVal = $("#inventario_id").val() || "";
            const productVal = $("#product_id").val() || "";
            const productoSelectVal = $(".select2Prod").val() || "";

            if (inventarioVal) dataform.set("inventario_id", inventarioVal);
            if (productVal) dataform.set("product_id", productVal);
            if (!dataform.get("producto")) dataform.set("producto", productVal || inventarioVal || productoSelectVal || "");

            // DEBUG: imprimir FormData en consola para ver exactamente lo que se envía
            try {
                const dump = {};
                for (let pair of dataform.entries()) {
                    dump[pair[0]] = pair[1];
                }
                console.log("-> FormData que se enviará a /salesavedetail:");
                console.table(dump);
            } catch (err) {
                console.warn("No se pudo imprimir FormData:", err);
            }

            // Enviar
            sendData("/salesavedetail", dataform, token)
                .then((result) => {
                    try {
                        if (result.status === 1) {
                            $("#regdetailId").val("0");
                            try { $(".select2Prod").val(null).trigger("change"); } catch(e){}
                            formDetail.reset();
                            showData(result);
                        } else if (result.status === 0) {
                            let errors = result.errors || {};
                            $.each(errors, function (field, messages) {
                                let $input = $('[name="' + field + '"]');
                                let $errorContainer = $input.closest(".form-group").find(".error-message");
                                $errorContainer.html(messages[0]).show();
                            });
                        }
                    } catch (err) {
                        console.error("Error post-procesando response savedetail:", err, result);
                    }
                    resolve(result);
                })
                .catch((err) => {
                    console.error("Error sendData en agregarDetalleProgramatico:", err);
                    reject(err);
                });
        } catch (err) {
            console.error("Error creando FormData programático:", err);
            reject(err);
        }
    });
}
/* ----------------------------------------------------------------- */

/* ----------------- HANDLERS EXISTENTES (mantener) ----------------- */
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
            regDetail.value = editReg.id;
            price.value = formatCantidadSinCero(editReg.price);
            price_venta.value = formatCantidadSinCero(editReg.price_venta);
            quantity.value = editReg.quantity;
            porc_iva.value = editReg.porc_iva;
            porc_otro_impuesto.value = editReg.porc_otro_impuesto;
            porc_impoconsumo.value = editReg.porc_impoconsumo;
            porc_desc.value = editReg.porc_desc;
            promo_percent.value = editReg.promo_percent;

            let select = $(".select2Prod");
            if (select.find("option[value='" + editReg.inventario_id + "']").length) {
                select.val(editReg.inventario_id).trigger("change");
            } else {
                let newOption = new Option(editReg.text || "Producto editado", editReg.inventario_id, true, true);
                select.append(newOption).trigger("change");
            }
        });
    }
});

if (btnAdd) {
    btnAdd.addEventListener("click", (e) => {
        e.preventDefault();

        const dataform = new FormData(formDetail);

        const tipobodega = document.getElementById("tipobodega") ? document.getElementById("tipobodega").value : "";
        console.log("tipobodega a enviar:", tipobodega);
        dataform.set("tipobodega", tipobodega);

        sendData("/salesavedetail", dataform, token)
            .then((result) => {
                console.log("Respuesta savedetail:", result);

                if (result.status === 1) {
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
} else {
    console.warn("btnAdd no está definido en el DOM al cargar el script.");
}

/* ----------------- showData (mantener) ----------------- */
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
                <td>${formatCantidadSinCero(element.porc_desc)}</td>                 
                <td>$${formatCantidadSinCero(element.descuento)}</td> 
                <td>$${formatCantidadSinCero(element.descuento_cliente)}</td>
                <td>${formatCantidadSinCero(element.promo_percent)}</td>
                <td>$${formatCantidadSinCero(element.promo_value)}</td> 
                <td>$${formatCantidadSinCero(element.total_bruto)}</td>   
                <td>${formatCantidadSinCero(element.porc_iva)}</td> 
                <td>$${formatCantidadSinCero(element.iva)}</td> 
                <td>${formatCantidadSinCero(element.porc_otro_impuesto)}</td>     
                <td>$${formatCantidadSinCero(element.otro_impuesto)}</td>   
                <td>${formatCantidadSinCero(element.porc_impoconsumo)}</td> 
                <td>$${formatCantidadSinCero(element.impoconsumo)}</td>    
                <td>$${formatCantidadSinCero(element.price_venta)}</td>           
                <td>$${formatCantidadSinCero(element.total)}</td>        
                <td class="text-center">
                    <button class="btn btn-dark fas fa-edit" data-id="${element.id}" name="btnEdit" title="EditarJS"></button>
                    <button class="btn btn-dark fas fa-trash" name="btnDown" data-id="${element.id}" title="Borrar"></button>
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
            <th>$${formatCantidadSinCero(arrayTotales.TotalValorAPagar)}</th>            
            <td class="text-center"></td>
        </tr>
    `;
};
/* ----------------------------------------------------------------- */

price && price.addEventListener("change", function () {
    const enteredValue = formatMoneyNumber(price.value);
    console.log("Entered value: " + enteredValue);
    price.value = formatCantidadSinCero(enteredValue);
});

price_venta && price_venta.addEventListener("change", function () {
    const enteredValue = formatMoneyNumber(price_venta.value);
    console.log("Entered value: " + enteredValue);
    price_venta.value = formatCantidadSinCero(enteredValue);
});

quantity && quantity.addEventListener("change", function () {
    const enteredValue = Number(quantity.value);
    console.log("Valor ingresado: " + enteredValue);
    quantity.value = enteredValue;
});

// Get the current date (si necesitas usarla)
const date = new Date();

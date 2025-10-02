// rogercode-create.js
import { sendData } from "../exportModule/core/rogercode-core.js";
import {
    successToastMessage,
    errorMessage,
} from "../exportModule/message/rogercode-message.js";
import {
    loadingStart,
    loadingEnd,
} from "../exportModule/core/rogercode-core.js";

/* ----------------- PROTECCIÓN GLOBAL: loadingStart/loadingEnd ----------------- */
// Aseguramos que existan funciones globales si otros scripts las llaman directamente.
// No sobrescribimos tus importaciones; esto protege llamadas globales.
if (typeof window.loadingStart !== "function") {
    window.loadingStart = loadingStart || function () {};
}
if (typeof window.loadingEnd !== "function") {
    window.loadingEnd = loadingEnd || function () {};
}

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

var centrocosto = document.getElementById("centrocosto")
    ? document.getElementById("centrocosto").value
    : "";
var cliente = document.getElementById("cliente")
    ? document.getElementById("cliente").value
    : "";

/* ----------------- HELPERS LOADING SEGUROS ----------------- */
function safeLoadingStart() {
    try {
        if (typeof loadingStart === "function") {
            try {
                loadingStart();
            } catch (err) {
                /* silencioso */
            }
        }
    } catch (err) {
        /* silencioso */
    }
}
function safeLoadingEnd() {
    try {
        if (typeof loadingEnd === "function") {
            try {
                loadingEnd();
            } catch (err) {
                /* silencioso */
            }
        }
    } catch (err) {
        /* silencioso */
    }
}

/* ----------------- HELPERS FORMATO ----------------- */
function parseNumber(value) {
    if (value === null || value === undefined || value === "") return 0;
    const v = String(value)
        .replace(/\s/g, "")
        .replace(/\$/g, "")
        .replace(/\./g, "")
        .replace(/,/g, ".");
    const n = parseFloat(v);
    return isNaN(n) ? 0 : n;
}
function formatMoneyNumber(v) {
    if (v === null || v === undefined) return 0;
    return (
        parseFloat(
            String(v)
                .replace(/[^0-9,-\.]/g, "")
                .replace(",", ".")
        ) || 0
    );
}
if (typeof formatCantidadSinCero === "undefined") {
    function formatCantidadSinCero(v) {
        if (v === null || v === undefined) return "";
        if (!isNaN(Number(v))) return Number(v).toString();
        return String(v);
    }
}

/* ----------------- EAN-13 VALIDATOR ----------------- */
function isValidEan13(ean) {
    if (!ean || typeof ean !== "string") return false;
    if (!/^\d{13}$/.test(ean)) return false;
    const digits = ean.split("").map((d) => parseInt(d, 10));
    const checkDigit = digits[12];
    const sumOdd = digits
        .slice(0, 12)
        .filter((_, i) => i % 2 === 0)
        .reduce((a, b) => a + b, 0);
    const sumEven = digits
        .slice(0, 12)
        .filter((_, i) => i % 2 === 1)
        .reduce((a, b) => a + b, 0);
    const total = sumOdd + 3 * sumEven;
    const calc = (10 - (total % 10)) % 10;
    return calc === checkDigit;
}

/* ----------------- GLOBALS PARA DETECTOR ----------------- */
const saleId =
    window.SALE_ID || ($("#saleId").length ? $("#saleId").val() : null) || null;
let processedTimestamps = {}; // evita reprocesos muy rápidos

/* ----------------- focusSelect2Search (robusto) ----------------- */
function focusSelect2Search(opts = {}) {
    const attemptsMax = opts.attempts || 8;
    const baseDelay = opts.delay || 50;
    const $select = $(".select2Prod").first();

    if (!$select || !$select.length) return Promise.resolve(false);

    return new Promise((resolve) => {
        let attempt = 0;

        function findAndFocus() {
            attempt++;
            try {
                $select.select2("close");
            } catch (e) {}
            try {
                $select.select2("open");
            } catch (e) {}

            // 1) estándar
            let $search = $(
                ".select2-container--open .select2-search__field"
            ).first();
            if ($search && $search.length) {
                try {
                    $search.val("");
                    $search.focus();
                    return resolve(true);
                } catch (e) {}
            }

            // 2) data('select2').$dropdown
            try {
                const sd = $select.data("select2");
                if (sd) {
                    const $dd = sd.$dropdown || sd.dropdown || null;
                    if ($dd && $dd.length) {
                        const $inp = $dd.find(".select2-search__field").first();
                        if ($inp && $inp.length) {
                            try {
                                $inp.val("");
                                $inp.focus();
                                return resolve(true);
                            } catch (e) {}
                        }
                    }
                }
            } catch (e) {}

            // 3) fallback container focus
            try {
                const $container = $select
                    .next(".select2-container")
                    .find(".select2-selection--single")
                    .first();
                if ($container && $container.length) {
                    try {
                        $container.focus();
                        return resolve(true);
                    } catch (e) {}
                }
            } catch (e) {}

            if (attempt < attemptsMax) {
                const wait = baseDelay * attempt;
                setTimeout(findAndFocus, wait);
            } else {
                resolve(false);
            }
        }

        findAndFocus();
    });
}
// small helper: espera hasta que exista un selector (usa requestAnimationFrame)
function waitForSelector(selector, timeout = 800) {
    return new Promise((resolve) => {
        const start = performance.now();
        function check() {
            const el = document.querySelector(selector);
            if (el) return resolve(el);
            if (performance.now() - start > timeout) return resolve(null);
            requestAnimationFrame(check);
        }
        check();
    });
}

/**
 * prepareSelectForScan:
 * - vacía el select2 (valor null)
 * - abre el dropdown
 * - espera a que exista el input interno y lo enfoca
 * Retorna Promise<boolean> (true si enfocó).
 */
async function prepareSelectForScan(opts = {}) {
    const $select = $(".select2Prod").first();
    if (!$select || !$select.length) return false;

    // 1) Vaciar valor (dejamos la opción visual en blanco)
    try {
        $select.val(null).trigger("change");
    } catch (e) {
        /* ignore */
    }

    // 2) Forzar abrir dropdown (intentamos varias veces silenciosamente)
    try {
        $select.select2("open");
    } catch (e) {
        /* ignore */
    }

    // 3) Esperar al input estándar que aparece cuando dropdown está abierto
    const timeout = opts.timeout || 900;
    let $input = await waitForSelector(
        ".select2-container--open .select2-search__field",
        timeout
    );

    // 4) si no se encontró, intentar buscar en data('select2').$dropdown (casos dropdownParent)
    if (!$input) {
        try {
            const sd = $select.data("select2");
            if (sd) {
                const $dd = sd.$dropdown || sd.dropdown || null;
                if ($dd && $dd.length) {
                    $input = $dd.find(".select2-search__field").get(0) || null;
                }
            }
        } catch (e) {
            /* ignore */
        }
    }

    // 5) Si encontramos el input, limpiarlo y enfocarlo
    if ($input) {
        try {
            if ($input.jquery) {
                $input.val("");
                $input.focus();
            } else {
                $input.value = "";
                $input.focus();
            }
            return true;
        } catch (e) {
            // fallback: intentar foco en container visible
        }
    }

    // 6) Fallback: enfocar el container visible de Select2 (no es ideal pero ayuda)
    try {
        const $container = $select
            .next(".select2-container")
            .find(".select2-selection--single")
            .first();
        if ($container && $container.length) {
            $container.focus();
            return true;
        }
    } catch (e) {
        /* ignore */
    }

    return false;
}

/* ----------------- Inicializar Select2 y detector EAN ----------------- */
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

    $(".select2Prod").on("select2:select", function (e) {
        const data = e.params.data;
        $("#lote_id").val(data.lote_id || "");
        $("#inventario_id").val(data.inventario_id || data.id || "");
        $("#stock_ideal").val(data.stock_ideal || "");
        $("#store_id").val(data.store_id || "");
        $("#store_name").val(data.store_name || "");
        const productId = data.product_id || data.productId || null;
        const inventarioId = data.inventario_id || data.id || null;
        obtenerPreciosYPromo(productId, inventarioId, 1);
    });

    let debounceTimer = null;
    const MIN_REPROCESS_MS = 300;

    $(document).on("input", ".select2-search__field", function () {
        const $input = $(this);
        const val = $input.val().trim();

        if (debounceTimer) clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            if (!/^\d{13}$/.test(val)) return;
            if (!isValidEan13(val)) {
                if (typeof errorMessage === "function")
                    errorMessage("EAN inválido: " + val);
                try {
                    $input.val("");
                } catch (e) {}
                return;
            }
            const now = Date.now();
            const last = processedTimestamps[val] || 0;
            if (now - last < MIN_REPROCESS_MS) return;
            processedTimestamps[val] = now;
            processEan(val, $input);
        }, 60);
    });
});

/* ----------------- processEan ----------------- */
function processEan(barcode, $searchInput) {
    safeLoadingStart();

    $.ajax({
        url: "/products/search/autoservicio",
        dataType: "json",
        data: { q: barcode, sale_id: saleId },
        success: function (results) {
            safeLoadingEnd();
            if (!results || !results.length) {
                if (typeof errorMessage === "function")
                    errorMessage("Producto no encontrado para EAN: " + barcode);
                try {
                    $searchInput.val("").focus();
                } catch (e) {}
                return;
            }

            const data = results[0];
            const inventarioId =
                data.inventario_id ?? data.id ?? data.product_id ?? null;
            const productId = data.product_id ?? data.productId ?? null;
            const optionText =
                data.text ||
                data.nameprod ||
                data.label ||
                "Producto " + (inventarioId || productId);
            const $select = $(".select2Prod").first();

            try {
                if (
                    $select.find("option[value='" + inventarioId + "']").length
                ) {
                    $select.val(inventarioId).trigger("change");
                } else {
                    const newOption = new Option(
                        optionText,
                        inventarioId,
                        true,
                        true
                    );
                    $select.append(newOption).trigger("change");
                }
            } catch (err) {
                try {
                    $select.append(
                        '<option value="' +
                            inventarioId +
                            '" selected>' +
                            optionText +
                            "</option>"
                    );
                    $select.val(inventarioId).trigger("change");
                } catch (e) {}
            }

            $("#lote_id").val(data.lote_id || "");
            $("#inventario_id").val(inventarioId || "");
            $("#product_id").val(productId || "");
            $("#stock_ideal").val(data.stock_ideal || "");
            $("#store_id").val(data.store_id || "");
            $("#store_name").val(data.store_name || "");
            $("#quantity").val(1);

            obtenerPreciosYPromo(
                productId,
                inventarioId,
                1,
                function (resp, err) {
                    setTimeout(function () {
                        try {
                            $searchInput.val("");
                            $searchInput.focus();
                        } catch (e) {}
                        agregarDetalleProgramatico()
                            .then((result) => {
                                if (result && result.status === 1) {
                                    processedTimestamps[barcode] = 0;
                                    if (
                                        typeof successToastMessage ===
                                        "function"
                                    )
                                        successToastMessage(
                                            "Producto agregado"
                                        );
                                } else if (result && result.status === 0) {
                                    if (typeof errorMessage === "function")
                                        errorMessage(
                                            "No se pudo agregar el producto: revisar validaciones"
                                        );
                                }
                            })
                            .catch((err) => {
                                if (typeof errorMessage === "function")
                                    errorMessage("Error agregando producto");
                            });
                    }, 80);
                }
            );
        },
        error: function () {
            safeLoadingEnd();
            if (typeof errorMessage === "function")
                errorMessage("Error buscando producto por EAN");
        },
    });
}

/* ----------------- obtenerPreciosYPromo ----------------- */
function obtenerPreciosYPromo(productId, inventarioId, quantity, callback) {
    if (!productId && !inventarioId) {
        if (typeof callback === "function")
            callback(null, {
                error: true,
                message: "Falta productId/inventarioId",
            });
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
                const formattedPrice =
                    typeof formatCantidadSinCero === "function"
                        ? formatCantidadSinCero(resp.precio)
                        : resp.precio ?? "";
                $("#price").val(formattedPrice);

                const formattedPriceVenta =
                    typeof formatCantidadSinCero === "function"
                        ? formatCantidadSinCero(resp.precio_venta)
                        : resp.precio_venta ?? "";
                $("#price_venta").val(formattedPriceVenta);

                $("#porc_iva").val(resp.iva ?? 0);
                $("#porc_otro_impuesto").val(resp.otro_impuesto ?? 0);
                $("#porc_impoconsumo").val(resp.impoconsumo ?? 0);

                $("#porc_desc").val(resp.porc_descuento ?? 0);

                const promoPercent = parseFloat(resp.promo_percent || 0);
                const promoMinQ = resp.promo_min_quantity ?? null;
                const promoValue = parseFloat(resp.promo_value || 0);
                const appliedPromotionId = resp.applied_promotion_id || null;

                $("#promo_percent").val(
                    isNaN(promoPercent) ? "0.00" : promoPercent.toFixed(2)
                );
                $("#promo_min_quantity").val(promoMinQ ?? "");
                $("#applied_promotion_id").val(appliedPromotionId ?? "");

                $("#promo_value").val(promoValue);
                $("#promo_value_display").text(
                    promoValue ? promoValue : "$0.00"
                );
            } catch (err) {
                console.error("Error aplicando resp de precios:", err, resp);
            }

            if (typeof callback === "function") callback(resp);
        },
        error: function (xhr, status, error) {
            console.error(
                "Error al obtener precios/promo:",
                error,
                xhr && xhr.responseText
            );
            $("#promo_percent").val("0.00");
            $("#promo_value").val("0.00");
            $("#applied_promotion_id").val("");
            $("#promo_value_display").text("$0.00");
            if (typeof callback === "function")
                callback(null, { error: true, xhr: xhr });
        },
    });
}

/* ----------------- agregarDetalleProgramatico ----------------- */
function agregarDetalleProgramatico() {
    return new Promise((resolve, reject) => {
        try {
            const dataform = new FormData(formDetail);

            const tipobodegaEl = document.getElementById("tipobodega");
            if (tipobodegaEl) dataform.set("tipobodega", tipobodegaEl.value);

            dataform.set("quantity", String($("#quantity").val() || 1));

            const inventarioVal = $("#inventario_id").val() || "";
            const productVal = $("#product_id").val() || "";
            const productoSelectVal = $(".select2Prod").val() || "";

            if (inventarioVal) dataform.set("inventario_id", inventarioVal);
            if (productVal) dataform.set("product_id", productVal);
            if (!dataform.get("producto"))
                dataform.set(
                    "producto",
                    productVal || inventarioVal || productoSelectVal || ""
                );

            sendData("/salesavedetail", dataform, token)
                .then((result) => {
                    try {
                        if (result.status === 1) {
                            // Reseteos visuales
                            $("#regdetailId").val("0");
                            // No vaciamos el select aquí (lo hace prepareSelectForScan)
                            formDetail.reset();
                            showData(result);

                            // Espera un microslot y luego prepara select2 para el próximo escaneo
                            setTimeout(function () {
                                prepareSelectForScan({ timeout: 900 }).then(
                                    (ok) => {
                                        if (!ok)
                                            console.warn(
                                                "prepareSelectForScan: no pudo enfocar el input"
                                            );
                                    }
                                );
                            }, 60);
                        } else if (result.status === 0) {
                            let errors = result.errors || {};
                            $.each(errors, function (field, messages) {
                                let $input = $('[name="' + field + '"]');
                                let $errorContainer = $input
                                    .closest(".form-group")
                                    .find(".error-message");
                                $errorContainer.html(messages[0]).show();
                            });
                        }
                    } catch (err) {
                        console.error(
                            "Error post-procesando response savedetail:",
                            err,
                            result
                        );
                    }
                    resolve(result);
                })
                .catch((err) => {
                    console.error(
                        "Error sendData en agregarDetalleProgramatico:",
                        err
                    );
                    reject(err);
                });
        } catch (err) {
            console.error("Error creando FormData programático:", err);
            reject(err);
        }
    });
}

/* ----------------- Handlers (mantener) ----------------- */
tbodyTable.addEventListener("click", (e) => {
    e.preventDefault();
    let element = e.target;
    if (element.name === "btnDown") {
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
                const dataform = new FormData();
                dataform.append("id", Number(id));
                dataform.append("ventaId", Number(venta_id.value));
                sendData("/ventadown", dataform, token).then((result) => {
                    showData(result);
                });
            }
        });
    }

    if (element.name === "btnEdit") {
        let id = element.getAttribute("data-id");
        const dataform = new FormData();
        dataform.append("id", Number(id));
        sendData("/saleById", dataform, token).then((result) => {
            let editReg = result.reg;
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
            if (
                select.find("option[value='" + editReg.inventario_id + "']")
                    .length
            ) {
                select.val(editReg.inventario_id).trigger("change");
            } else {
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

if (btnAdd) {
    btnAdd.addEventListener("click", (e) => {
        e.preventDefault();

        const dataform = new FormData(formDetail);
        const tipobodega = document.getElementById("tipobodega")
            ? document.getElementById("tipobodega").value
            : "";
        dataform.set("tipobodega", tipobodega);

        sendData("/salesavedetail", dataform, token)
            .then((result) => {
                if (result.status === 1) {
                    $("#regdetailId").val("0");
                    $("#producto").val("").trigger("change");
                    formDetail.reset();
                    showData(result);

                    setTimeout(function () {
                        focusSelect2Search({ attempts: 8, delay: 50 }).then(
                            () => {}
                        );
                    }, 80);
                }

                if (result.status === 0) {
                    let errors = result.errors;
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

/* ----------------- showData ----------------- */
const showData = (data) => {
    const dataAll = data.array || [];
    showRegTbody.innerHTML = "";
    dataAll.forEach((element) => {
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
                <td>${formatCantidadSinCero(element.promo_percent)}</td>
                <td>$${formatCantidadSinCero(element.promo_value)}</td> 
                <td>$${formatCantidadSinCero(element.total_bruto)}</td>   
                <td>${formatCantidadSinCero(element.porc_iva)}</td> 
                <td>$${formatCantidadSinCero(element.iva)}</td> 
                <td>${formatCantidadSinCero(
                    element.porc_otro_impuesto
                )}</td>     
                <td>$${formatCantidadSinCero(element.otro_impuesto)}</td>   
                <td>${formatCantidadSinCero(element.porc_impoconsumo)}</td> 
                <td>$${formatCantidadSinCero(element.impoconsumo)}</td>    
                <td>$${formatCantidadSinCero(
                    element.price_venta
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

    let arrayTotales = data.arrayTotales || {};
    tableFoot.innerHTML = `
        <tr>
            <th>Totales</th>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            <th>$${formatCantidadSinCero(arrayTotales.TotalBruto)}</th>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            <th>$${formatCantidadSinCero(arrayTotales.TotalValorAPagar)}</th>
            <td class="text-center"></td>
        </tr>
    `;
};

/* ----------------- listeners price/quantity -----------------
price &&
    price.addEventListener("change", function () {
        const enteredValue = formatMoneyNumber(price.value);
        price.value = formatCantidadSinCero(enteredValue);
    });

price_venta &&
    price_venta.addEventListener("change", function () {
        const enteredValue = formatMoneyNumber(price_venta.value);
        price_venta.value = formatCantidadSinCero(enteredValue);
    });

quantity &&
    quantity.addEventListener("change", function () {
        const enteredValue = Number(quantity.value);
        quantity.value = enteredValue;
    });

// fecha si la usas
const date = new Date();
 */
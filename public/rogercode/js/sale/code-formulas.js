// Variables globales (se pueden definir si se requieren en otros contextos)
var valorA_Pagar = 0;
var valorPagado = 0;
var valorCambio = 0;

// Inicializa el campo de valor pagado
$("#valor_pagado").val(0);

// Evento: Cambio en el valor de efectivo
$("#valor_a_pagar_efectivo").on("change", function () {
    let valorEfectivo = formatMoneyNumber($(this).val());
    $(this).val(formatCantidadSinCero(valorEfectivo));

    // Cálculo automático del valor a pagar con tarjeta:
    // Se obtiene el total a pagar (desde el input readonly) y se le resta el efectivo ingresado.
    let totalAPagar = formatMoneyNumber($("#valor_a_pagar").val());
    let valorTarjetaCalculado = totalAPagar - valorEfectivo;

    // Si el cálculo resulta negativo, se asigna cero
    if (valorTarjetaCalculado < 0) {
        valorTarjetaCalculado = 0;
    }

    $("#valor_a_pagar_tarjeta").val(
        formatCantidadSinCero(valorTarjetaCalculado)
    );

    // Se recalculan totales y cambio
    calculavalorapagar();
});

// Evento: Cambio en el valor de tarjeta (entrada manual)
$("#valor_a_pagar_tarjeta").on("change", function () {
    let valorTarjetaManual = formatMoneyNumber($(this).val());
    $(this).val(formatCantidadSinCero(valorTarjetaManual));

    // Se recalculan totales y cambio (manteniendo el valor ingresado manualmente)
    calculavalorapagar();
});

// Eventos para otros métodos de pago
$("#valor_a_pagar_otros").on("change", function () {
    let valorOtros = formatMoneyNumber($(this).val());
    $(this).val(formatCantidadSinCero(valorOtros));
    calculavalorapagar();
});

$("#valor_a_pagar_credito").on("change", function () {
    let valorCredito = formatMoneyNumber($(this).val());
    $(this).val(formatCantidadSinCero(valorCredito));
    calculavalorapagar();
});

// Función para calcular el total pagado sumando todos los métodos de pago
function calculavalorapagar() {
    let efectivo = formatMoneyNumber($("#valor_a_pagar_efectivo").val());
    let tarjeta = formatMoneyNumber($("#valor_a_pagar_tarjeta").val());
    let otros = formatMoneyNumber($("#valor_a_pagar_otros").val());
    let credito = formatMoneyNumber($("#valor_a_pagar_credito").val());

    let totalPagado = efectivo + tarjeta + otros + credito;
    $("#valor_pagado").val(formatCantidadSinCero(totalPagado));
    calcularCambio();
}

// Función para calcular el cambio
function calcularCambio() {
    let totalPagado = formatMoneyNumber($("#valor_pagado").val());
    let totalAPagar = formatMoneyNumber($("#valor_a_pagar").val());
    let cambio = totalPagado - totalAPagar;
    $("#cambio").val(formatCantidadSinCero(cambio));

    // Se habilita o deshabilita el botón guardar según si el cambio es negativo o no
    $("#btnGuardar").prop("disabled", cambio < 0);
}

// Al cargar la vista se verifica el nombre del cliente y se deshabilitan campos en caso de "Cliente Mostrador"
$(document).ready(function () {
    let clienteMostrador = "CLIENTES VARIOS";
    let thirdName = $("#name_cliente").val();

    if (thirdName === clienteMostrador) {
        $("#forma_pago_credito_id").prop("disabled", true);
        $("#codigo_pago_credito").prop("disabled", true);
        $("#valor_a_pagar_credito").prop("disabled", true);
    }
});

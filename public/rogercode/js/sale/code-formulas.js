var valorA_Pagar = 0;
var valorPagado = 0;
var valorCambio = 0;

document.getElementById("valor_pagado").value = valorPagado;

// Al modificar el valor del pago en efectivo se calcula automáticamente el pago con tarjeta
document.getElementById("valor_a_pagar_efectivo").addEventListener("change", function () {
    // Formateamos y actualizamos el valor ingresado en efectivo
    let valorEfectivo = formatMoneyNumber($("#valor_a_pagar_efectivo").val());
    $("#valor_a_pagar_efectivo").val(formatCantidadSinCero(valorEfectivo));

    // Obtenemos el total a pagar (valor leído de la vista, input readonly)
    let totalAPagar = formatMoneyNumber($("#valor_a_pagar").val());

    // Calculamos el valor a pagar con tarjeta restando el efectivo del total
    let valorTarjeta = totalAPagar - valorEfectivo;
    $("#valor_a_pagar_tarjeta").val(formatCantidadSinCero(valorTarjeta));

    // Se vuelve a calcular el total pagado y el cambio
    calculavalorapagar();
});

// Si existen otros inputs que el usuario pueda modificar se mantiene su lógica
document.getElementById("valor_a_pagar_otros").addEventListener("change", function () {
    let valorOtros = formatMoneyNumber($("#valor_a_pagar_otros").val());
    $("#valor_a_pagar_otros").val(formatCantidadSinCero(valorOtros));
    calculavalorapagar();
});

document.getElementById("valor_a_pagar_credito").addEventListener("change", function () {
    let valorCredito = formatMoneyNumber($("#valor_a_pagar_credito").val());
    $("#valor_a_pagar_credito").val(formatCantidadSinCero(valorCredito));
    calculavalorapagar();
});

// Función para calcular el total pagado sumando todos los métodos de pago y luego calcular el cambio
const calculavalorapagar = () => {
    let efectivo = formatMoneyNumber($("#valor_a_pagar_efectivo").val());
    let tarjeta = formatMoneyNumber($("#valor_a_pagar_tarjeta").val());
    let otros   = formatMoneyNumber($("#valor_a_pagar_otros").val());
    let credito = formatMoneyNumber($("#valor_a_pagar_credito").val());

    let totalPagado = efectivo + tarjeta + otros + credito;
    $("#valor_pagado").val(formatCantidadSinCero(totalPagado));
    calcularCambio();
};

function calcularCambio() {
    let valorPagado = formatMoneyNumber($("#valor_pagado").val());
    let valorAbonado = formatMoneyNumber($("#valor_a_pagar").val());
    let cambio = valorPagado - valorAbonado;
    $("#cambio").val(formatCantidadSinCero(cambio));
    
    // Habilita o deshabilita el botón de guardar según el cambio calculado
    $("#btnGuardar").prop("disabled", cambio < 0);
}

// Si el cliente es "Cliente Mostrador" se deshabilitan ciertos campos
$(document).ready(function () {
    let clienteMostrador = "Cliente Mostrador";
    let thirdName = $("#name_cliente").val();

    if (thirdName === clienteMostrador) {
        $("#forma_pago_credito_id").prop("disabled", true);
        $("#codigo_pago_credito").prop("disabled", true);
        $("#valor_a_pagar_credito").prop("disabled", true);
    }
});

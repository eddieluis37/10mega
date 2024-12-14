import { sendData } from "../../exportModule/core/rogercode-core.js";
import {
    successToastMessage,
    errorMessage,
} from "../../exportModule/message/rogercode-message.js";

const refresh_table = () => {
    let table = $("#tableProducto").dataTable();
    table.fnDraw(false);
};

btnAddLote.addEventListener("click", async (e) => {
    e.preventDefault();
    console.log("log");
    const dataform = new FormData(formLote);
    sendData("/lotesave", dataform, token).then((resp) => {
        console.log(resp);
        if (resp.status == 1) {
            formLote.reset();
            btnClose.click();
            successToastMessage(resp.message);
            refreshLoteSelect(); // Llama a la función para recargar el select de lotes del index
            refreshLote(); // Llama a la función para recargar el select de lotes del index
            if (resp.registroId != 0) {
                // Puedes redirigir o hacer otras acciones aquí si es necesario
            }
        } else {
            let errors = resp.errors;
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

// Función para recargar el select de lotes del index
function refreshLoteSelect() {
    fetch("/lote-data")
        .then((response) => response.json())
        .then((data) => {
            const $loteSelect = $("#lote");
            $loteSelect.empty();
            $loteSelect.append('<option value="">Seleccione el lote</option>'); // Opción por defecto

            data.forEach((option) => {
                $loteSelect.append(
                    `<option value="${option.id}" data="${option}">${option.name}</option>`
                );
            });
        });
}

btnAddProducto.addEventListener("click", async (e) => {
    e.preventDefault();
    console.log("log");
    const dataform = new FormData(formProducto);
    sendData("/productlotesave", dataform, token).then((resp) => {
        console.log(resp);
        if (resp.status == 1) {
            formProducto.reset();
            btnClose.click();
            successToastMessage(resp.message);
            refresh_table();
            if (resp.registroId != 0) {
                //for new register
                refresh_table();
                /*   window.location.href = `caja/create/${resp.registroId}`; */
            } else {
                //refresh_table();
            }
        }
        if (resp.status == 0) {
            let errors = resp.errors;
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

// Limpiar mensajes de error al cerrar la ventana modal
$("#modal-create-producto").on("hidden.bs.modal", function () {
    $(".error-message").text("");
});

import {sendData} from '../exportModule/core/rogercode-core.js';
import { successToastMessage, errorMessage } from '../exportModule/message/rogercode-message.js';
btnAddCompensadoRes.addEventListener("click", async (e) => {
    e.preventDefault();
    const dataform = new FormData(formCompensadoRes);
    sendData('/compensadosave',dataform,token).then((resp) => {
        console.log(resp);
        if (resp.status == 1) {
            formCompensadoRes.reset();   
            btnClose.click();
            successToastMessage(resp.message); 
            if (resp.registroId != 0) {//for new register
                window.location.href = `compensado/create/${resp.registroId}`;
            }else{
                refresh_table();
            }
        }
        if (resp.status == 0) {
            let errors = resp.errors;
            console.log(errors);
            $.each(errors, function(field, messages) {
                console.log(field, messages)
                let $input = $('[name="' + field + '"]');
                let $errorContainer = $input.closest('.form-group').find('.error-message');
                $errorContainer.html(messages[0]);
                $errorContainer.show();
            });        
        }
    });
})

function showModalcreateLote() {
    // Lógica para mostrar el modal
    $('#modal-create-lote').modal('show');
}

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
                    `<option value="${option.id}" data="${option}">${option.codigo}</option>`
                );
            });
        });
}

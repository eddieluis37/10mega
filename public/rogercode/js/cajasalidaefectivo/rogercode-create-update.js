import {sendData} from '../exportModule/core/rogercode-core.js';
import { successToastMessage, errorMessage } from '../exportModule/message/rogercode-message.js';

const refresh_table = () => {
    let table = $("#tableCajaSalidaEfectivo").dataTable();
    table.fnDraw(false);
};

btnAddSalidaEfectivo.addEventListener("click", async (e) => {
    e.preventDefault();
    console.log("log")
    const dataform = new FormData(formProducto);
    sendData('/csesave',dataform,token).then((resp) => {
        console.log(resp);
        if (resp.status == 1) {
            formProducto.reset();   
            btnClose.click();
            successToastMessage(resp.message); 
               refresh_table();
            if (resp.registroId != 0) {//for new register
                refresh_table();
              /*   window.location.href = `caja/create/${resp.registroId}`; */
            }else{
                //refresh_table();
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

// Limpiar mensajes de error al cerrar la ventana modal
$('#modal-create-salida').on('hidden.bs.modal', function () {
    $('.error-message').text('');
});
console.log("Comienza Devolucion parcial Starting");
import {sendData} from '../exportModule/core/rogercode-core.js';
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

// Espera a que el DOM esté listo
document.addEventListener("DOMContentLoaded", function () {
    // Define la función en el ámbito global para que pueda ser llamada desde el botón
    window.confirmPartialReturnSubmit = function () {
        // Usamos el diálogo de confirmación nativo de JavaScript
        if (confirm("¿Estás seguro de procesar la devolución parcial?")) {
            let form = document.getElementById("partialReturnForm");
            let dataform = new FormData(form);

            sendData("/sale/partialreturn",dataform,token)
                .then((data) => {
                    if (dataform.message) {
                        alert("Éxito: " + dataform.message);
                        window.location.href = "/sales";
                    } else if (dataform.error) {
                        alert("Error: " + dataform.error);
                    }
                })
                .catch((error) => {
                    console.error(error);
                    alert("Ocurrió un error al procesar la devolución.");
                });
        }
    };
});

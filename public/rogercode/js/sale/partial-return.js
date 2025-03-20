console.log("Comienza Devolucion parcial Starting");

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


    const sendData = async (dataform, ruta) => {
        let response = await fetch(ruta, {
          headers: {
            "X-CSRF-TOKEN": token,
          },
          method: "POST",
          body: dataform,
        });
        let data = await response.json();
        return data;
      };

      
// Espera a que el DOM esté listo
document.addEventListener("DOMContentLoaded", function () {
    // Define la función en el ámbito global para que pueda ser llamada desde el botón
    window.confirmPartialReturnSubmit = function () {
        // Usamos el diálogo de confirmación nativo de JavaScript
        if (confirm("¿Estás seguro de procesar la devolución parcial?")) {
            let form = document.getElementById("partialReturnForm");
            let dataform = new FormData(form);

            sendData("/sale/partial-return",dataform,token).then((data) => {
                    if (data.message) {
                        alert("Éxito: " + data.message);
                        window.location.href = "/sales";
                    } else if (data.error) {
                        alert("Error: " + data.error);
                    }
                })
                .catch((error) => {
                    console.error(error);
                    alert("Ocurrió un error al procesar la devolución.");
                });
        }
    };
});

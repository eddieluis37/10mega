console.log("Comienza Devolucion parcial Starting");
import {
    sendData
} from "../exportModule/core/rogercode-core.js";


// Espera a que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Obtenemos el token CSRF
    var token = document.querySelector('meta[name="csrf-token"]').getAttribute("content");


  /*   const sendData = async (dataform, ruta) => {
        let response = await fetch(ruta, {
            headers: {
                "X-CSRF-TOKEN": token,
            },
            method: "POST",
            body: dataform,
        });
        let data = await response.json();
        //console.log(data);
        return data;
    }; */

    function confirmPartialReturnSubmit() {
        swal({
            title: "Confirmar Devolución Parcial",
            text: "¿Estás seguro de procesar la devolución parcial?",
            icon: "warning",
            buttons: {
                cancel: "Cancelar",
                confirm: "Sí, procesar"
            },
            dangerMode: true,
        }).then((willProcess) => {
            if (willProcess) {
                let form = document.getElementById('partialReturnForm');
                let dataform = new FormData(form);

                // En lugar de usar fetch, utilizamos la función sendData:
                sendData(dataform, '/sale/partial-return')
                    .then(data => {
                        if (data.message) {
                            swal("Éxito", data.message, "success").then(() => {
                                window.location.href = "/sales";
                            });
                        } else if (data.error) {
                            swal("Error", data.error, "error");
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        swal("Error", "Ocurrió un error al procesar la devolución.", "error");
                    });
            }
        });
    }


});
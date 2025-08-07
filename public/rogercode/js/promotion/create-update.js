import { sendData } from "../exportModule/core/rogercode-core.js";
import {
    successToastMessage,
    errorMessage,
} from "../exportModule/message/rogercode-message.js";
btnAddVentaDomicilio.addEventListener("click", async (e) => {
    e.preventDefault();
    const dataform = new FormData(formCompensadoRes);
    sendData("/ventasave_autoservicio", dataform, token).then((resp) => {
        console.log(resp);
        if (resp.status == 1) {
            formCompensadoRes.reset();
            btnClose.click();
            successToastMessage(resp.message);
            if (resp.registroId != 0) {
                //for new register
                window.location.href = `promotion/create/${resp.registroId}`;
            } else {
                refresh_table();
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


const storeBtn = document.getElementById("storePromotionBtn");
let isProcessing = false;

storeBtn.addEventListener("click", async (e) => {
  e.preventDefault();

  // Si ya estamos procesando, no hacemos nada
  if (isProcessing) return;

  isProcessing = true;
  // Deshabilitar visualmente el enlace
  storeBtn.classList.add("opacity-50", "pointer-events-none");

  const dataform = new FormData(formCompensadoRes);

  try {
    const resp = await sendData("/store-promotion", dataform, token);
    console.log(resp);

    if (resp.status === 1) {
      formCompensadoRes.reset();
      btnClose.click();
      successToastMessage(resp.message);

      if (resp.registroId && resp.registroId != 0) {
        // Redirigir a creación de venta
        window.location.href = `promotion/create/${resp.registroId}`;
      } else {
        refresh_table();
      }
    } else if (resp.status === 0) {
      // Mostrar errores de validación
      const errors = resp.errors || {};
      Object.entries(errors).forEach(([field, messages]) => {
        const $input = document.querySelector(`[name="${field}"]`);
        const $errorContainer = $input.closest(".form-group").querySelector(".error-message");
        $errorContainer.innerHTML = messages[0];
        $errorContainer.style.display = "block";
      });
    } else if (resp.status === 2) {
      // Ya hay una venta en curso
      warningToastMessage(resp.message || "Ya hay una venta en proceso");
      // opcional: redirigir directamente si recibes registroId
      if (resp.registroId) {
        window.location.href = `promotion/create/${resp.registroId}`;
      }
    }

  } catch (err) {
    console.error("Error en la petición:", err);
    errorToastMessage("Ocurrió un error, inténtalo de nuevo.");
  } finally {
    // Reactivar botón
    isProcessing = false;
    storeBtn.classList.remove("opacity-50", "pointer-events-none");
  }
});

export const saveForm = async (url,form,token) => {
    try {
        const dataform = new FormData(form);
        let response = await fetch(url, {
        headers: {
            'X-CSRF-TOKEN': token
        },
        method: 'POST',
        body: dataform
        });
        let data = await response.json();
        return data;
    } catch (error) {
        console.log(error);
    }
        
}


export const sendData = async (url,form,token) => {
    try {
        let response = await fetch(url, {
        headers: {
            'X-CSRF-TOKEN': token
        },
        method: 'POST',
        body: form
        });
        let data = await response.json();
        return data;
    } catch (error) {
        console.log(error);
    }
        
}

// rogercode-core.js  (fragmento / override seguro)
//
// Reemplaza la implementación actual de loadingStart / loadingEnd por esta versión segura.
// Si tu archivo ya exporta `loadingStart`/`loadingEnd`, sustituye su cuerpo por el siguiente.
// Si no los exporta así exactamente, aplica la lógica de comprobación `if (btn)` alrededor
// de cualquier acceso a btn.disabled o btn.innerHTML.

export function loadingStart() {
    try {
        // Si tu core ya tiene lógica, aquí la mantienes y la envuelves en try/catch.
        // Ejemplo seguro genérico:
        // 1) Intentar deshabilitar un botón si existe (no asumir que 'btn' está definido)
        const btn = document.querySelector(".btn-loading") || document.querySelector("#btnAdd") || null;
        if (btn) {
            try { btn.disabled = true; } catch(e) { /* ignore */ }
        }

        // 2) Mostrar overlay si existe
        const overlay = document.getElementById("rc-loading-overlay");
        if (overlay) {
            overlay.style.display = "flex";
        } else {
            // Si quieres usar la overlay creada por este módulo, crea una mínima
            // (evita manipular DOM si no hace falta).
            // No crear repetidamente: sólo si no existe, lo añadimos.
            try {
                const existing = document.getElementById("rc-loading-overlay");
                if (!existing) {
                    const el = document.createElement("div");
                    el.id = "rc-loading-overlay";
                    el.style.cssText = "position:fixed;left:0;top:0;right:0;bottom:0;background:rgba(255,255,255,0.6);z-index:99999;display:flex;align-items:center;justify-content:center;font-size:14px;color:#333";
                    el.innerText = "Procesando…";
                    el.style.display = "flex";
                    document.body.appendChild(el);
                }
            } catch(e){ /* ignore */ }
        }
    } catch (err) {
        // No dejar que un error rompa la ejecución del resto
        console.warn("loadingStart safe wrapper error:", err);
    }
}

export function loadingEnd() {
    try {
        const btn = document.querySelector(".btn-loading") || document.querySelector("#btnAdd") || null;
        if (btn) {
            try { btn.disabled = false; } catch(e) { /* ignore */ }
        }

        const overlay = document.getElementById("rc-loading-overlay");
        if (overlay) {
            overlay.style.display = "none";
        }
    } catch (err) {
        console.warn("loadingEnd safe wrapper error:", err);
    }
}

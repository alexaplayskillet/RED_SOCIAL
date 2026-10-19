(function () {

    const claves = {
        toggleDeriva: "cosmix_deriva_activa",
        toggleContador: "cosmix_contador_activo",
        toggleNotificaciones: "cosmix_notificaciones_activas"
    };

    Object.keys(claves).forEach(function (idInput) {

        const input = document.getElementById(idInput);

        if (!input) {
            return;
        }

        const clave = claves[idInput];
        const guardado = localStorage.getItem(clave);

        // Si no hay valor guardado, se respeta el "checked" por defecto del HTML
        if (guardado !== null) {
            input.checked = guardado === "true";
        }

        input.addEventListener("change", function () {
            localStorage.setItem(clave, input.checked);
        });

    });

})();
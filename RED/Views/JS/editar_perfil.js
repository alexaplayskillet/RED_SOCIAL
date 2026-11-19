(function () {
    const btnAbrir = document.getElementById('btnEditarPerfil');
    const overlay = document.getElementById('editarPerfilOverlay');
    const form = document.getElementById('formEditarPerfil');
    const btnCerrar = document.getElementById('cerrarEditarPerfil');
    const btnCancelar = document.getElementById('cancelarEditarPerfil');
    const mensaje = document.getElementById('editarPerfilMensaje');

    if (!btnAbrir || !overlay || !form) return;

    function abrir() {
        mensaje.textContent = '';
        overlay.classList.add('active');
    }

    function cerrar() {
        overlay.classList.remove('active');
    }

    btnAbrir.addEventListener('click', abrir);
    btnCerrar.addEventListener('click', cerrar);
    btnCancelar.addEventListener('click', cerrar);

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) cerrar();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') cerrar();
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        mensaje.textContent = '';

        const boton = form.querySelector('button[type="submit"]');
        boton.disabled = true;

        try {
            const resp = await fetch('../Controllers/EditarPerfilController.php', {
                method: 'POST',
                body: new FormData(form)
            });
            const r = await resp.json();

            if (r.ok) {
                location.reload();
                return;
            }
            mensaje.textContent = r.mensaje;
        } catch (err) {
            mensaje.textContent = 'No se pudieron guardar los cambios';
        }

        boton.disabled = false;
    });
})();
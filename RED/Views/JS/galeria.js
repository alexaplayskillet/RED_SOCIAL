(function () {
    const URL_GALERIA = '../Controllers/GaleriaController.php';

    const gridRecientes = document.getElementById('galeriaRecientes');
    const vacioRecientes = document.getElementById('galeriaRecientesVacio');
    const gridCompleta = document.getElementById('galeriaGrid');
    const vacioCompleta = document.getElementById('galeriaVacio');
    const visor = document.getElementById('visorFoto');
    const visorImg = document.getElementById('visorFotoImg');
    const visorTexto = document.getElementById('visorFotoTexto');
    const visorCerrar = document.getElementById('visorFotoCerrar');

    const editarOverlay = document.getElementById('editarFotoOverlay');
    const editarTexto = document.getElementById('editarFotoTexto');
    const editarMensaje = document.getElementById('editarFotoMensaje');
    const editarGuardar = document.getElementById('editarFotoGuardar');
    const editarCancelar = document.getElementById('editarFotoCancelar');
    const editarCerrar = document.getElementById('editarFotoCerrar');

    const borrarOverlay = document.getElementById('eliminarFotoOverlay');
    const borrarMensaje = document.getElementById('eliminarFotoMensaje');
    const borrarAceptar = document.getElementById('eliminarFotoAceptar');
    const borrarCancelar = document.getElementById('eliminarFotoCancelar');
    const borrarCerrar = document.getElementById('eliminarFotoCerrar');

    let fotoActual = null;

    if (!gridRecientes && !gridCompleta) return;

    function urlImagen(foto) {
        return URL_GALERIA + '?id=' + foto.id;
    }

    function abrirVisor(foto) {
        visorImg.src = urlImagen(foto);
        visorTexto.textContent = foto.texto || '';
        visorTexto.style.display = foto.texto ? 'block' : 'none';
        visor.classList.add('active');
    }

    function cerrarVisor() {
        visor.classList.remove('active');
        visorImg.removeAttribute('src');
    }

    function crearImagen(foto) {
        const img = document.createElement('img');
        img.loading = 'lazy';
        img.alt = foto.texto || 'Foto publicada';
        img.src = urlImagen(foto);
        img.onerror = function () { img.remove(); };
        return img;
    }

    function crearBoton(clase, titulo, icono, accion) {
        const boton = document.createElement('button');
        boton.type = 'button';
        boton.className = clase;
        boton.title = titulo;
        boton.innerHTML = '<i class="' + icono + '"></i>';
        boton.addEventListener('click', function (e) {
            e.stopPropagation();
            accion();
        });
        return boton;
    }

    function pintar(grid, vacio, fotos, claseItem, conAcciones) {
        if (!grid) return;
        grid.innerHTML = '';
        fotos.forEach(function (foto) {
            const item = document.createElement('div');
            item.className = claseItem;
            item.appendChild(crearImagen(foto));

            if (conAcciones) {
                const acciones = document.createElement('div');
                acciones.className = 'foto-card-acciones';
                acciones.appendChild(crearBoton('foto-card-btn', 'Editar texto', 'ion-md-create', function () { abrirEditar(foto); }));
                acciones.appendChild(crearBoton('foto-card-btn foto-card-btn-borrar', 'Eliminar foto', 'ion-md-trash', function () { abrirEliminar(foto); }));
                item.appendChild(acciones);
            }

            item.addEventListener('click', function () { abrirVisor(foto); });
            grid.appendChild(item);
        });
        if (vacio) vacio.style.display = fotos.length ? 'none' : 'block';
    }

    function cargar(limite) {
        const url = URL_GALERIA + (limite ? '?limite=' + limite : '');
        return fetch(url)
            .then(function (r) { return r.json(); })
            .then(function (d) { return d.ok ? d.fotos : []; })
            .catch(function () { return []; });
    }

    if (visor) {
        visorCerrar.addEventListener('click', cerrarVisor);
        visor.addEventListener('click', function (e) {
            if (e.target === visor) cerrarVisor();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && visor.classList.contains('active')) cerrarVisor();
        });
    }

    function enviar(accion, foto, texto) {
        const datos = new FormData();
        datos.append('accion', accion);
        datos.append('id', foto.id);
        if (texto !== undefined) datos.append('texto', texto);
        return fetch(URL_GALERIA, { method: 'POST', body: datos })
            .then(function (r) { return r.json(); })
            .catch(function () { return { ok: false, mensaje: 'Error de conexión. Intenta de nuevo.' }; });
    }

    function recargar() {
        return cargar(0).then(function (fotos) {
            pintar(gridCompleta, vacioCompleta, fotos, 'foto-card', true);
            pintar(gridRecientes, vacioRecientes, fotos.slice(0, 6), 'galeria-item', false);
        });
    }

    function abrirEditar(foto) {
        fotoActual = foto;
        editarTexto.value = foto.texto || '';
        editarMensaje.textContent = '';
        editarGuardar.disabled = false;
        editarOverlay.classList.add('active');
        editarTexto.focus();
    }

    function cerrarEditar() {
        editarOverlay.classList.remove('active');
        fotoActual = null;
    }

    function abrirEliminar(foto) {
        fotoActual = foto;
        borrarMensaje.textContent = '';
        borrarAceptar.disabled = false;
        borrarOverlay.classList.add('active');
    }

    function cerrarEliminar() {
        borrarOverlay.classList.remove('active');
        fotoActual = null;
    }

    if (editarOverlay) {
        editarGuardar.addEventListener('click', function () {
            if (!fotoActual) return;
            editarGuardar.disabled = true;
            enviar('editar', fotoActual, editarTexto.value.trim()).then(function (res) {
                if (res.ok) {
                    cerrarEditar();
                    recargar();
                } else {
                    editarMensaje.textContent = res.mensaje;
                    editarGuardar.disabled = false;
                }
            });
        });
        editarCancelar.addEventListener('click', cerrarEditar);
        editarCerrar.addEventListener('click', cerrarEditar);
        editarOverlay.addEventListener('click', function (e) {
            if (e.target === editarOverlay) cerrarEditar();
        });
    }

    if (borrarOverlay) {
        borrarAceptar.addEventListener('click', function () {
            if (!fotoActual) return;
            borrarAceptar.disabled = true;
            enviar('eliminar', fotoActual).then(function (res) {
                if (res.ok) {
                    cerrarEliminar();
                    recargar();
                } else {
                    borrarMensaje.textContent = res.mensaje;
                    borrarAceptar.disabled = false;
                }
            });
        });
        borrarCancelar.addEventListener('click', cerrarEliminar);
        borrarCerrar.addEventListener('click', cerrarEliminar);
        borrarOverlay.addEventListener('click', function (e) {
            if (e.target === borrarOverlay) cerrarEliminar();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        if (editarOverlay && editarOverlay.classList.contains('active')) cerrarEditar();
        if (borrarOverlay && borrarOverlay.classList.contains('active')) cerrarEliminar();
    });

    recargar();
})();
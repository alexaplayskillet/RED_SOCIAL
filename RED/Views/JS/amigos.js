(function () {
    const URL_AMIGOS = '../Controllers/AmigosController.php';
    const URL_FOTO = '../Controllers/ImagenController.php';

    const gridRecientes = document.getElementById('amigosRecientes');
    const gridTodos = document.getElementById('amigosGrid');
    const subtitulo = document.getElementById('amigosSubtitulo');
    const buscador = document.getElementById('amigosBuscar');
    const vacioTodos = document.getElementById('amigosVacio');
    const vacioRecientes = document.getElementById('amigosRecientesVacio');

    const overlay = document.getElementById('confirmarAmigoOverlay');
    const textoConfirmar = document.getElementById('confirmarAmigoTexto');
    const mensajeConfirmar = document.getElementById('confirmarAmigoMensaje');
    const btnConfirmar = document.getElementById('confirmarAmigoAceptar');
    const btnCancelar = document.getElementById('confirmarAmigoCancelar');
    const btnCerrar = document.getElementById('confirmarAmigoCerrar');

    if (!gridRecientes && !gridTodos) return;

    let amigoPendiente = null;
    let peticionActual = 0;
    let temporizador = null;
    const version = Date.now();

    function crearFoto(amigo, clase) {
        const contenedor = document.createElement('div');
        contenedor.className = clase;

        const iniciales = document.createElement('span');
        iniciales.textContent = amigo.iniciales;
        contenedor.appendChild(iniciales);

        if (amigo.tieneFoto) {
            const img = document.createElement('img');
            img.alt = 'Foto de ' + amigo.tag;
            img.src = URL_FOTO + '?tipo=foto&id=' + amigo.id + '&v=' + version;
            img.onload = function () { iniciales.style.display = 'none'; };
            img.onerror = function () { img.remove(); };
            contenedor.appendChild(img);
        }
        return contenedor;
    }

    function pintarRecientes(amigos) {
        if (!gridRecientes) return;
        gridRecientes.innerHTML = '';
        amigos.forEach(function (amigo) {
            const item = document.createElement('div');
            item.className = 'amigo-item';
            item.appendChild(crearFoto(amigo, 'amigo-foto'));

            const nombre = document.createElement('div');
            nombre.className = 'amigo-nombre';
            nombre.textContent = '@' + amigo.tag;
            item.appendChild(nombre);

            gridRecientes.appendChild(item);
        });
        if (vacioRecientes) vacioRecientes.style.display = amigos.length ? 'none' : 'block';
    }

    function pintarTodos(amigos, total, hayBusqueda) {
        if (!gridTodos) return;
        gridTodos.innerHTML = '';

        amigos.forEach(function (amigo) {
            const tarjeta = document.createElement('div');
            tarjeta.className = 'amigo-card';

            const eliminar = document.createElement('button');
            eliminar.type = 'button';
            eliminar.className = 'amigo-card-eliminar';
            eliminar.title = 'Eliminar amigo';
            eliminar.innerHTML = '<i class="ion-md-trash"></i>';
            eliminar.addEventListener('click', function () { abrirConfirmacion(amigo); });
            tarjeta.appendChild(eliminar);

            tarjeta.appendChild(crearFoto(amigo, 'amigo-card-foto'));

            const nombre = document.createElement('div');
            nombre.className = 'amigo-card-nombre';
            nombre.textContent = '@' + amigo.tag;
            tarjeta.appendChild(nombre);

            const real = document.createElement('div');
            real.className = 'amigo-card-real';
            real.textContent = amigo.nombre;
            tarjeta.appendChild(real);

            const mutuos = document.createElement('div');
            mutuos.className = 'amigo-card-mutuos';
            mutuos.textContent = amigo.mutuos + (amigo.mutuos === 1 ? ' amigo en común' : ' amigos en común');
            tarjeta.appendChild(mutuos);

            gridTodos.appendChild(tarjeta);
        });

        if (subtitulo) {
            subtitulo.textContent = total + (total === 1 ? ' amigo' : ' amigos');
        }
        if (vacioTodos) {
            if (amigos.length) {
                vacioTodos.style.display = 'none';
            } else {
                vacioTodos.textContent = hayBusqueda && total > 0
                    ? 'No se encontraron amigos con esa búsqueda.'
                    : 'Aún no tienes amigos agregados.';
                vacioTodos.style.display = 'block';
            }
        }
    }

    function cargarRecientes() {
        if (!gridRecientes) return Promise.resolve();
        return fetch(URL_AMIGOS + '?limite=4')
            .then(function (r) { return r.json(); })
            .then(function (datos) { if (datos.ok) pintarRecientes(datos.amigos); })
            .catch(function () {});
    }

    function cargarTodos() {
        if (!gridTodos) return Promise.resolve();
        const q = buscador ? buscador.value.trim() : '';
        const numero = ++peticionActual;
        return fetch(URL_AMIGOS + '?q=' + encodeURIComponent(q))
            .then(function (r) { return r.json(); })
            .then(function (datos) {
                if (numero !== peticionActual || !datos.ok) return;
                pintarTodos(datos.amigos, datos.total, q !== '');
            })
            .catch(function () {});
    }

    function abrirConfirmacion(amigo) {
        amigoPendiente = amigo;
        textoConfirmar.textContent = '¿Quieres eliminar a @' + amigo.tag + ' de tus amigos?';
        mensajeConfirmar.textContent = '';
        btnConfirmar.disabled = false;
        overlay.classList.add('active');
    }

    function cerrarConfirmacion() {
        overlay.classList.remove('active');
        amigoPendiente = null;
    }

    function eliminarAmigo() {
        if (!amigoPendiente) return;
        btnConfirmar.disabled = true;

        const datos = new FormData();
        datos.append('accion', 'eliminar');
        datos.append('id_amigo', amigoPendiente.id);

        fetch(URL_AMIGOS, { method: 'POST', body: datos })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (res.ok) {
                    cerrarConfirmacion();
                    cargarRecientes();
                    cargarTodos();
                } else {
                    mensajeConfirmar.textContent = res.mensaje || 'No se pudo eliminar al amigo.';
                    btnConfirmar.disabled = false;
                }
            })
            .catch(function () {
                mensajeConfirmar.textContent = 'Error de conexión. Intenta de nuevo.';
                btnConfirmar.disabled = false;
            });
    }

    if (buscador) {
        buscador.addEventListener('input', function () {
            clearTimeout(temporizador);
            temporizador = setTimeout(cargarTodos, 300);
        });
    }

    if (overlay) {
        btnConfirmar.addEventListener('click', eliminarAmigo);
        btnCancelar.addEventListener('click', cerrarConfirmacion);
        btnCerrar.addEventListener('click', cerrarConfirmacion);
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) cerrarConfirmacion();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay.classList.contains('active')) cerrarConfirmacion();
        });
    }

    cargarRecientes();
    cargarTodos();
})();
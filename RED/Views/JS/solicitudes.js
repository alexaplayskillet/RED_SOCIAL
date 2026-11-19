(function () {
    const URL_SOLI = '../Controllers/SolicitudesController.php';
    const URL_FOTO = '../Controllers/ImagenController.php';
    const version = Date.now();

    const panelSugerencias = document.getElementById('sugerenciasLista');
    const gridSugerencias = document.getElementById('sugerenciasGrid');
    const gridRecibidas = document.getElementById('solicitudesGrid');
    const mensaje = document.getElementById('solicitudesMensaje');
    const vacio = document.getElementById('solicitudesVacio');
    const subtitulo = document.getElementById('solicitudesSubtitulo');

    if (!panelSugerencias && !gridSugerencias && !gridRecibidas) return;

    function avisar(texto, esError) {
        if (!mensaje) return;
        mensaje.textContent = texto;
        mensaje.className = 'solicitudes-msg' + (esError ? ' error' : ' ok');
    }

    function enviarPost(accion, idUsuario) {
        const datos = new FormData();
        datos.append('accion', accion);
        datos.append('id_usuario', idUsuario);
        return fetch(URL_SOLI, { method: 'POST', body: datos })
            .then(function (r) { return r.json(); })
            .catch(function () { return { ok: false, mensaje: 'Error de conexión. Intenta de nuevo.' }; });
    }

    function pedirLista(lista, limite) {
        let url = URL_SOLI + '?lista=' + lista;
        if (limite) url += '&limite=' + limite;
        return fetch(url)
            .then(function (r) { return r.json(); })
            .then(function (d) { return d.ok ? d.personas : []; })
            .catch(function () { return []; });
    }

    function urlFoto(persona) {
        return URL_FOTO + '?tipo=foto&id=' + persona.id + '&v=' + version;
    }

    function agregarImagen(contenedor, persona, iniciales) {
        if (!persona.tieneFoto) return;
        const img = document.createElement('img');
        img.alt = 'Foto de ' + persona.tag;
        img.src = urlFoto(persona);
        img.onload = function () { if (iniciales) iniciales.style.display = 'none'; };
        img.onerror = function () { img.remove(); };
        contenedor.appendChild(img);
    }

    function textoMutuos(n) {
        return n + (n === 1 ? ' amigo en común' : ' amigos en común');
    }

    function marcarEnviada(boton) {
        boton.textContent = 'Enviada';
        boton.disabled = true;
        boton.classList.add('enviada');
    }

    function conectarEnviar(boton, persona) {
        boton.addEventListener('click', function () {
            boton.disabled = true;
            enviarPost('enviar', persona.id).then(function (res) {
                if (res.ok) {
                    marcarEnviada(boton);
                    avisar('Solicitud enviada a @' + persona.tag + '.', false);
                } else {
                    boton.disabled = false;
                    avisar(res.mensaje, true);
                }
            });
        });
    }

    function pintarSugerenciasPanel(personas) {
        panelSugerencias.innerHTML = '';
        personas.forEach(function (persona) {
            const fila = document.createElement('div');
            fila.className = 'suggestion';

            const foto = document.createElement('div');
            foto.className = 'foto';
            agregarImagen(foto, persona, null);
            fila.appendChild(foto);

            const info = document.createElement('div');
            info.className = 'suggestion-info';
            const tag = document.createElement('p');
            tag.textContent = '@' + persona.tag;
            const sub = document.createElement('p');
            sub.textContent = persona.mutuos > 0 ? textoMutuos(persona.mutuos) : 'Recomendado';
            info.appendChild(tag);
            info.appendChild(sub);
            fila.appendChild(info);

            const boton = document.createElement('button');
            boton.type = 'button';
            boton.className = 'follow-btn';
            boton.textContent = 'Agregar';
            conectarEnviar(boton, persona);
            fila.appendChild(boton);

            panelSugerencias.appendChild(fila);
        });
        if (!personas.length) {
            const p = document.createElement('p');
            p.className = 'solicitudes-msg';
            p.textContent = 'No hay sugerencias por ahora.';
            panelSugerencias.appendChild(p);
        }
    }

    function crearTarjeta(persona) {
        const tarjeta = document.createElement('div');
        tarjeta.className = 'soli';

        const foto = document.createElement('div');
        foto.className = 'soli-foto';
        const iniciales = document.createElement('span');
        iniciales.textContent = persona.iniciales;
        foto.appendChild(iniciales);
        agregarImagen(foto, persona, iniciales);
        tarjeta.appendChild(foto);

        const nombre = document.createElement('p');
        nombre.textContent = persona.nombre;
        tarjeta.appendChild(nombre);

        const tag = document.createElement('p');
        const smallTag = document.createElement('small');
        smallTag.textContent = '@' + persona.tag;
        tag.appendChild(smallTag);
        tarjeta.appendChild(tag);

        const mutuos = document.createElement('p');
        const smallMutuos = document.createElement('small');
        smallMutuos.textContent = textoMutuos(persona.mutuos);
        mutuos.appendChild(smallMutuos);
        tarjeta.appendChild(mutuos);

        return tarjeta;
    }

    function actualizarVacio(grid, textoVacio, textoSubtitulo) {
        const total = grid.querySelectorAll('.soli').length;
        if (vacio) {
            vacio.textContent = textoVacio;
            vacio.style.display = total ? 'none' : 'block';
        }
        if (subtitulo && textoSubtitulo) {
            subtitulo.textContent = textoSubtitulo(total);
        }
    }

    function quitarTarjeta(tarjeta, despues) {
        tarjeta.classList.add('saliendo');
        setTimeout(function () {
            tarjeta.remove();
            despues();
        }, 250);
    }

    function pintarRecibidas(personas) {
        gridRecibidas.innerHTML = '';
        const texto = function (n) {
            if (n === 0) return 'No tienes solicitudes pendientes';
            return n + (n === 1 ? ' persona quiere conectar contigo' : ' personas quieren conectar contigo');
        };

        personas.forEach(function (persona) {
            const tarjeta = crearTarjeta(persona);

            const aceptar = document.createElement('button');
            aceptar.type = 'button';
            aceptar.className = 'btn-cerrar mar';
            aceptar.textContent = 'Aceptar';

            const rechazar = document.createElement('button');
            rechazar.type = 'button';
            rechazar.className = 'btn-devolver mar';
            rechazar.textContent = 'Rechazar';

            function responder(accion) {
                aceptar.disabled = true;
                rechazar.disabled = true;
                enviarPost(accion, persona.id).then(function (res) {
                    if (res.ok || res.mensaje === 'Esa solicitud ya no está disponible.') {
                        avisar(res.ok ? (accion === 'aceptar' ? 'Ahora eres amigo de @' + persona.tag + '.' : 'Solicitud de @' + persona.tag + ' rechazada.') : res.mensaje, !res.ok);
                        quitarTarjeta(tarjeta, function () {
                            actualizarVacio(gridRecibidas, 'No tienes solicitudes pendientes.', texto);
                        });
                    } else {
                        aceptar.disabled = false;
                        rechazar.disabled = false;
                        avisar(res.mensaje, true);
                    }
                });
            }

            aceptar.addEventListener('click', function () { responder('aceptar'); });
            rechazar.addEventListener('click', function () { responder('rechazar'); });

            tarjeta.appendChild(aceptar);
            tarjeta.appendChild(document.createElement('br'));
            tarjeta.appendChild(rechazar);
            gridRecibidas.appendChild(tarjeta);
        });

        actualizarVacio(gridRecibidas, 'No tienes solicitudes pendientes.', texto);
    }

    function pintarSugerenciasPagina(personas) {
        gridSugerencias.innerHTML = '';
        personas.forEach(function (persona) {
            const tarjeta = crearTarjeta(persona);

            const boton = document.createElement('button');
            boton.type = 'button';
            boton.className = 'btn-cerrar mar';
            boton.textContent = 'Enviar solicitud';
            conectarEnviar(boton, persona);

            tarjeta.appendChild(boton);
            gridSugerencias.appendChild(tarjeta);
        });
        actualizarVacio(gridSugerencias, 'No hay más usuarios para sugerirte por ahora.', null);
    }

    if (panelSugerencias) pedirLista('sugerencias', 3).then(pintarSugerenciasPanel);
    if (gridSugerencias) pedirLista('sugerencias', 24).then(pintarSugerenciasPagina);
    if (gridRecibidas) pedirLista('recibidas').then(pintarRecibidas);
})();
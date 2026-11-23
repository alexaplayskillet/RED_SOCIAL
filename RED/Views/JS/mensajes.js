(function () {
    const URL_MENSAJES = '../Controllers/MensajesController.php';
    const URL_FOTO = '../Controllers/ImagenController.php';
    const INTERVALO_MENSAJES = 4000;
    const INTERVALO_CHATS = 10000;

    const cuerpo = document.getElementById('bodyMensajes');
    const listaChats = document.getElementById('listaChats');
    const chatsVacio = document.getElementById('chatsVacio');
    const conv = document.getElementById('conv');
    const cajaMensajes = document.getElementById('mensajes');
    const convAvatar = document.getElementById('convAvatar');
    const convNombre = document.getElementById('convNombre');
    const convTag = document.getElementById('convTag');
    const formulario = document.getElementById('formMensaje');
    const entrada = document.getElementById('inputMensaje');
    const btnEnviar = document.getElementById('btnEnviar');
    const btnPlaneta = document.getElementById('btnPlaneta');

    if (!cuerpo || !listaChats || !conv) return;

    const esMovil = window.matchMedia('(max-width: 768px)');
    const version = Date.now();

    let chatActual = null;   // datos del usuario con quien se conversa
    let ultimoId = 0;        // id del ultimo mensaje pintado
    let token = 0;           // evita pintar respuestas de una conversacion anterior
    let enviando = false;

    /* ---------- utilidades ---------- */

    function peticion(url, opciones) {
        return fetch(url, opciones).then(function (r) {
            if (r.status === 401) {
                window.location.href = 'login.php';
                return new Promise(function () {});
            }
            return r.json();
        });
    }

    function fechaDe(texto) {
        return new Date(String(texto).replace(' ', 'T'));
    }

    function hora(texto) {
        return fechaDe(texto).toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
    }

    // Hora si es de hoy, "Ayer" o la fecha corta
    function fechaCorta(texto) {
        const f = fechaDe(texto);
        const hoy = new Date();
        const ayer = new Date();
        ayer.setDate(hoy.getDate() - 1);

        if (f.toDateString() === hoy.toDateString()) return hora(texto);
        if (f.toDateString() === ayer.toDateString()) return 'Ayer';
        return f.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: '2-digit' });
    }

    function crearAvatar(usuario) {
        const contenedor = document.createElement('div');
        contenedor.className = 'chat-avatar';

        const iniciales = document.createElement('span');
        iniciales.textContent = usuario.iniciales;
        contenedor.appendChild(iniciales);

        if (usuario.tieneFoto) {
            const img = document.createElement('img');
            img.alt = 'Foto de ' + usuario.tag;
            img.src = URL_FOTO + '?tipo=foto&id=' + usuario.id + '&v=' + version;
            img.onload = function () { iniciales.style.display = 'none'; };
            img.onerror = function () { img.remove(); };
            contenedor.appendChild(img);
        }
        return contenedor;
    }

    function irAlFinal() {
        cajaMensajes.scrollTop = cajaMensajes.scrollHeight;
    }

    function cercaDelFinal() {
        return cajaMensajes.scrollHeight - cajaMensajes.scrollTop - cajaMensajes.clientHeight < 80;
    }

    /* ---------- vista movil: chats <-> conversacion ---------- */

    function mostrarChats(mostrar) {
        cuerpo.classList.toggle('ver-chats', mostrar);
    }

    if (btnPlaneta) {
        btnPlaneta.addEventListener('click', function (e) {
            // En PC el planeta sigue llevando al inicio
            if (!esMovil.matches) return;
            e.preventDefault();
            mostrarChats(!cuerpo.classList.contains('ver-chats'));
        });
    }

    /* ---------- lista de chats ---------- */

    function pintarChats(chats) {
        listaChats.innerHTML = '';

        chats.forEach(function (chat) {
            const item = document.createElement('li');
            item.dataset.id = chat.id;
            if (chatActual && chatActual.id === chat.id) item.classList.add('activo');

            item.appendChild(crearAvatar(chat));

            const bloque = document.createElement('div');
            bloque.className = 'blok chat-datos';

            const nombre = document.createElement('p');
            nombre.textContent = chat.nombre;
            bloque.appendChild(nombre);

            const ultimo = document.createElement('small');
            if (chat.ultimo !== null) {
                ultimo.textContent = (chat.ultimoMio ? 'Tú: ' : '') + chat.ultimo;
            } else {
                ultimo.textContent = 'Escribe el primer mensaje';
            }
            bloque.appendChild(ultimo);
            item.appendChild(bloque);

            if (chat.ultimaFecha) {
                const cuando = document.createElement('span');
                cuando.className = 'chat-hora';
                cuando.textContent = fechaCorta(chat.ultimaFecha);
                item.appendChild(cuando);
            }

            item.addEventListener('click', function () { abrirConversacion(chat.id); });
            listaChats.appendChild(item);
        });

        chatsVacio.style.display = chats.length ? 'none' : 'block';
    }

    function cargarChats() {
        return peticion(URL_MENSAJES + '?accion=chats')
            .then(function (datos) { if (datos.ok) pintarChats(datos.chats); })
            .catch(function () {});
    }

    /* ---------- conversacion ---------- */

    function crearMensaje(m) {
        const caja = document.createElement('div');
        caja.className = m.mio ? 'enviado' : 'recibido';
        caja.dataset.id = m.id;
        caja.title = fechaDe(m.fecha).toLocaleString('es-MX');

        const p = document.createElement('p');
        const texto = document.createElement('span');
        texto.className = 'mensaje-texto';
        texto.textContent = m.texto;
        p.appendChild(texto);

        const h = document.createElement('span');
        h.className = 'mensaje-hora';
        h.textContent = hora(m.fecha);
        p.appendChild(h);

        if (m.mio) {
            const palomita = document.createElement('span');
            palomita.className = 'palomitas';
            palomita.textContent = '✓';
            p.appendChild(palomita);
        }

        caja.appendChild(p);
        return caja;
    }

    function agregarMensajes(mensajes) {
        if (!mensajes.length) return;
        mensajes.forEach(function (m) {
            if (m.id <= ultimoId) return;
            cajaMensajes.appendChild(crearMensaje(m));
            ultimoId = m.id;
        });
    }

    function marcarActivo() {
        Array.prototype.forEach.call(listaChats.children, function (li) {
            li.classList.toggle('activo', !!chatActual && Number(li.dataset.id) === chatActual.id);
        });
    }

    function abrirConversacion(idOtro) {
        const miToken = ++token;

        peticion(URL_MENSAJES + '?accion=abrir&id=' + encodeURIComponent(idOtro))
            .then(function (datos) {
                if (miToken !== token) return;

                if (!datos.ok) {
                    alert(datos.mensaje || 'No se pudo abrir el chat.');
                    return;
                }

                chatActual = datos.chat;
                ultimoId = 0;
                cajaMensajes.innerHTML = '';

                convAvatar.innerHTML = '';
                convAvatar.appendChild(crearAvatar(chatActual));
                convNombre.textContent = chatActual.nombre;
                convTag.textContent = '@' + chatActual.tag;

                entrada.disabled = !chatActual.puedeEscribir;
                btnEnviar.disabled = !chatActual.puedeEscribir;
                entrada.placeholder = chatActual.puedeEscribir
                    ? 'Escribe un mensaje...'
                    : 'Ya no son amigos, no puedes enviar mensajes';

                conv.classList.remove('sin-chat');
                mostrarChats(false);
                history.replaceState(null, '', 'messages.php?con=' + chatActual.id);

                // Si el chat es nuevo todavia no esta en la lista
                if (!listaChats.querySelector('li[data-id="' + chatActual.id + '"]')) {
                    cargarChats();
                }
                marcarActivo();

                return peticion(URL_MENSAJES + '?accion=mensajes&id=' + chatActual.id)
                    .then(function (res) {
                        if (miToken !== token || !res.ok) return;
                        agregarMensajes(res.mensajes);
                        irAlFinal();
                        if (chatActual.puedeEscribir) entrada.focus();
                    });
            })
            .catch(function () {});
    }

    // Revisa si llegaron mensajes nuevos en la conversacion abierta
    function actualizarConversacion() {
        if (!chatActual || document.hidden) return;
        const miToken = token;
        const idChat = chatActual.id;

        peticion(URL_MENSAJES + '?accion=mensajes&id=' + idChat + '&ultimo=' + ultimoId)
            .then(function (res) {
                if (miToken !== token || !res.ok || !res.mensajes.length) return;
                const abajo = cercaDelFinal();
                agregarMensajes(res.mensajes);
                if (abajo) irAlFinal();
                cargarChats();
            })
            .catch(function () {});
    }

    /* ---------- envio ---------- */

    formulario.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!chatActual || enviando) return;

        const texto = entrada.value.trim();
        if (texto === '') return;

        enviando = true;
        btnEnviar.disabled = true;

        const datos = new FormData();
        datos.append('accion', 'enviar');
        datos.append('id', chatActual.id);
        datos.append('texto', texto);

        peticion(URL_MENSAJES, { method: 'POST', body: datos })
            .then(function (res) {
                if (!res.ok) {
                    alert(res.mensaje || 'No se pudo enviar el mensaje.');
                    return;
                }
                entrada.value = '';
                agregarMensajes([res.mensaje]);
                irAlFinal();
                cargarChats();
            })
            .catch(function () { alert('Error de conexión. Intenta de nuevo.'); })
            .then(function () {
                enviando = false;
                btnEnviar.disabled = !chatActual || !chatActual.puedeEscribir;
                entrada.focus();
            });
    });

    /* ---------- inicio ---------- */

    const idInicial = parseInt(new URLSearchParams(window.location.search).get('con'), 10);

    cargarChats();
    if (idInicial > 0) {
        abrirConversacion(idInicial);
    }

    setInterval(actualizarConversacion, INTERVALO_MENSAJES);
    setInterval(cargarChats, INTERVALO_CHATS);
})();

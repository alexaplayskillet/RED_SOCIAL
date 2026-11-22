(function () {
  const URL_NOTIF = "../Controllers/NotificacionController.php";
  const URL_SOLI = "../Controllers/SolicitudesController.php";

  const menu = document.getElementById("notifMenu");
  const campana = document.getElementById("notifTrigger");
  const lista = document.getElementById("notifLista");

  // Sin sesion no hay campana ni panel
  if (!menu || !campana || !lista) {
    return;
  }

  // Texto de cada tipo de notificacion y el icono que lleva sobre la foto
  const TEXTOS = {
    like_pub: "reaccionó a tu publicación",
    comentario: "comentó tu publicación",
    respuesta: "respondió a tu comentario",
    like_com: "reaccionó a tu comentario",
    solicitud: "te envió una solicitud de amistad",
    aceptada: "aceptó tu solicitud de amistad"
  };

  const ICONOS = {
    like_pub: ["ion-md-heart", "notif-badge-like"],
    like_com: ["ion-md-heart", "notif-badge-like"],
    comentario: ["ion-md-chatbubbles", "notif-badge-comentario"],
    respuesta: ["ion-md-chatbubbles", "notif-badge-comentario"],
    solicitud: ["ion-md-person-add", "notif-badge-personas"],
    aceptada: ["ion-md-people", "notif-badge-personas"]
  };

  let notificaciones = [];
  let filtro = "todas";

  const contador = document.createElement("span");
  contador.className = "notif-contador";
  contador.hidden = true;
  campana.appendChild(contador);

  async function pedirJSON(url, opciones) {
    const resp = await fetch(url, opciones);
    return resp.json();
  }

  function enviarPost(url, campos) {
    const datos = new FormData();
    Object.entries(campos).forEach(([clave, valor]) => datos.append(clave, valor));
    return pedirJSON(url, { method: "POST", body: datos });
  }

  function pintarContador(total) {
    contador.textContent = total > 9 ? "9+" : String(total);
    contador.hidden = total <= 0;
  }

  async function actualizarContador() {
    try {
      const json = await pedirJSON(URL_NOTIF + "?accion=contar");
      if (json.ok) {
        pintarContador(json.total);
      }
    } catch (error) {
      console.error("No se pudo revisar las notificaciones.");
    }
  }

  function mensajeLista(texto) {
    lista.innerHTML = "";
    const p = document.createElement("p");
    p.className = "notif-vacio";
    p.textContent = texto;
    lista.appendChild(p);
  }

  function fotoUsuario(idUsuario) {
    return "../Controllers/ImagenController.php?tipo=foto&id=" + idUsuario;
  }

  function crearItem(n) {
    const item = document.createElement("div");
    item.className = "notif-item" + (n.leida ? "" : " notif-no-leida");
    item.dataset.idNotif = n.id;

    const avatar = document.createElement("div");
    avatar.className = "notif-avatar";

    const img = document.createElement("img");
    img.alt = "usuario";
    img.src = fotoUsuario(n.id_origen);
    img.onerror = () => {
      img.onerror = null;
      img.src = "./resources/foto.jpg";
    };

    const [icono, claseBadge] = ICONOS[n.tipo] || ["ion-md-notifications", "notif-badge-campana"];
    const badge = document.createElement("span");
    badge.className = "notif-badge " + claseBadge;
    const i = document.createElement("i");
    i.className = icono;
    badge.appendChild(i);

    avatar.append(img, badge);

    const cuerpo = document.createElement("div");
    cuerpo.className = "notif-texto";

    const p = document.createElement("p");
    const nombre = document.createElement("strong");
    nombre.textContent = n.nombre;
    p.append(nombre, " " + (TEXTOS[n.tipo] || "tiene algo nuevo para ti"));

    if (n.texto) {
      const cita = document.createElement("span");
      cita.className = "notif-cita";
      cita.textContent = ": “" + n.texto + "”";
      p.appendChild(cita);
    }

    const tiempo = document.createElement("span");
    tiempo.className = "notif-tiempo";
    tiempo.textContent = n.hace;

    cuerpo.append(p, tiempo);

    // Las solicitudes pendientes se pueden confirmar o eliminar aqui mismo
    if (n.tipo === "solicitud" && n.estado_solicitud === 1) {
      const acciones = document.createElement("div");
      acciones.className = "notif-acciones";

      const confirmar = document.createElement("button");
      confirmar.type = "button";
      confirmar.className = "btn-notif-confirmar";
      confirmar.textContent = "Confirmar";
      confirmar.dataset.respuesta = "aceptar";

      const eliminar = document.createElement("button");
      eliminar.type = "button";
      eliminar.className = "btn-notif-eliminar";
      eliminar.textContent = "Eliminar";
      eliminar.dataset.respuesta = "rechazar";

      acciones.append(confirmar, eliminar);
      cuerpo.appendChild(acciones);
    } else if (n.tipo === "solicitud" && n.estado_solicitud === 2) {
      const estado = document.createElement("span");
      estado.className = "notif-estado";
      estado.textContent = "Ahora son amigos";
      cuerpo.appendChild(estado);
    }

    item.append(avatar, cuerpo);

    if (!n.leida) {
      const punto = document.createElement("span");
      punto.className = "notif-dot";
      item.appendChild(punto);
    }

    return item;
  }

  function crearTituloGrupo(texto, conBoton, esAnteriores) {
    const titulo = document.createElement("div");
    titulo.className = "notif-grupo-titulo" + (esAnteriores ? " notif-grupo-anteriores" : "");

    const span = document.createElement("span");
    span.textContent = texto;
    titulo.appendChild(span);

    if (conBoton) {
      const boton = document.createElement("button");
      boton.type = "button";
      boton.className = "notif-ver-todo";
      boton.id = "notifMarcarTodas";
      boton.textContent = "Marcar todas como leídas";
      titulo.appendChild(boton);
    }

    return titulo;
  }

  // "Nuevas" son las no leidas y "Anteriores" las que ya se leyeron
  function pintarLista() {
    const visibles = filtro === "noleidas" ? notificaciones.filter((n) => !n.leida) : notificaciones;

    if (visibles.length === 0) {
      mensajeLista(filtro === "noleidas" ? "No tienes notificaciones sin leer." : "Todavía no tienes notificaciones.");
      return;
    }

    lista.innerHTML = "";
    const nuevas = visibles.filter((n) => !n.leida);
    const anteriores = visibles.filter((n) => n.leida);

    if (nuevas.length > 0) {
      lista.appendChild(crearTituloGrupo("Nuevas", true, false));
      nuevas.forEach((n) => lista.appendChild(crearItem(n)));
    }

    if (anteriores.length > 0) {
      lista.appendChild(crearTituloGrupo("Anteriores", false, nuevas.length > 0));
      anteriores.forEach((n) => lista.appendChild(crearItem(n)));
    }
  }

  async function cargarLista() {
    if (notificaciones.length === 0) {
      mensajeLista("Cargando notificaciones...");
    }

    try {
      const json = await pedirJSON(URL_NOTIF + "?accion=listar");

      if (json.ok) {
        notificaciones = json.notificaciones;
        pintarLista();
        pintarContador(notificaciones.filter((n) => !n.leida).length);
      } else {
        mensajeLista(json.mensaje);
      }
    } catch (error) {
      mensajeLista("No se pudieron cargar las notificaciones.");
    }
  }

  async function marcarLeida(n) {
    if (n.leida) {
      return;
    }
    n.leida = true;

    try {
      const json = await enviarPost(URL_NOTIF, { accion: "leer", id_notif: n.id });
      if (json.ok) {
        pintarContador(json.total);
      }
    } catch (error) {
      console.error("No se pudo marcar la notificación.");
    }
  }

  async function responderSolicitud(n, respuesta, boton) {
    boton.parentElement.querySelectorAll("button").forEach((b) => {
      b.disabled = true;
    });

    try {
      const json = await enviarPost(URL_SOLI, { accion: respuesta, id_usuario: n.id_origen });

      if (json.ok) {
        if (respuesta === "aceptar") {
          n.estado_solicitud = 2;
          n.leida = true;
        } else {
          notificaciones = notificaciones.filter((otra) => otra.id !== n.id);
        }
        pintarLista();
        actualizarContador();
        return;
      }
      alert(json.mensaje);
    } catch (error) {
      console.error("No se pudo responder la solicitud.");
    }

    boton.parentElement.querySelectorAll("button").forEach((b) => {
      b.disabled = false;
    });
  }

  lista.addEventListener("click", async (e) => {
    if (e.target.closest("#notifMarcarTodas")) {
      notificaciones.forEach((n) => {
        n.leida = true;
      });
      pintarLista();
      pintarContador(0);
      try {
        await enviarPost(URL_NOTIF, { accion: "leer_todas" });
      } catch (error) {
        console.error("No se pudieron marcar las notificaciones.");
      }
      return;
    }

    const item = e.target.closest(".notif-item");
    if (!item) {
      return;
    }

    const n = notificaciones.find((otra) => otra.id === Number(item.dataset.idNotif));
    if (!n) {
      return;
    }

    const boton = e.target.closest(".notif-acciones button");
    if (boton) {
      responderSolicitud(n, boton.dataset.respuesta, boton);
      return;
    }

    await marcarLeida(n);

    // Lleva a la publicacion o a las solicitudes segun el tipo
    if (n.id_pub) {
      window.location.href = "verPublicacion.php?id=" + n.id_pub;
    } else if (n.tipo === "solicitud") {
      window.location.href = "solicitudes.php";
    } else {
      pintarLista();
    }
  });

  // buttons.js ya abre y cierra el panel; aqui solo se cargan los datos al abrirlo
  campana.addEventListener("click", () => {
    setTimeout(() => {
      if (menu.classList.contains("active")) {
        cargarLista();
      }
    }, 0);
  });

  menu.querySelectorAll(".notif-tab").forEach((tab) => {
    tab.addEventListener("click", () => {
      filtro = tab.dataset.notiftab === "noleidas" ? "noleidas" : "todas";
      pintarLista();
    });
  });

  actualizarContador();
  setInterval(actualizarContador, 30000);
})();
const URL_PUBLICACIONES = "../Controllers/PublicacionController.php";

// <BODY data-sesion="1"> si hay alguien con sesion iniciada, "0" si es un visitante
const HAY_SESION = document.body.dataset.sesion !== "0";

function pedirSesion() {
  const aviso = document.getElementById("avisoSesion");
  if (aviso) {
    aviso.classList.add("activo");
  } else {
    window.location.href = "login.php";
  }
}

// Los visitantes pueden ver todo, pero al intentar publicar, comentar o dar like
// se les muestra el aviso para iniciar sesion. Se escucha en la fase de captura (true)
// para detener el clic antes de que llegue a los demas scripts.
if (!HAY_SESION) {
  const ACCIONES_CON_CUENTA = ".like_btn, .btn-like-com, .btn-responder, .guardar_btn, .form-comentario, #crear, .create-post-btn, .crear-historia-fija, .filtro-opcion[data-filtro='amigos']";

  document.addEventListener("click", (e) => {
    const aviso = document.getElementById("avisoSesion");

    if (aviso && (e.target === aviso || e.target.closest(".aviso-cerrar"))) {
      aviso.classList.remove("activo");
      return;
    }

    if (e.target.closest(ACCIONES_CON_CUENTA)) {
      e.preventDefault();
      e.stopPropagation();
      pedirSesion();
    }
  }, true);

  document.addEventListener("focusin", (e) => {
    if (e.target.closest(".form-comentario")) {
      e.target.blur();
      pedirSesion();
    }
  });

  document.addEventListener("keydown", (e) => {
    const aviso = document.getElementById("avisoSesion");
    if (e.key === "Escape" && aviso) {
      aviso.classList.remove("activo");
    }
  });
}

// Manda una accion al controlador y regresa la respuesta en JSON
async function enviarAccion(campos) {
  const datos = new FormData();
  Object.entries(campos).forEach(([clave, valor]) => datos.append(clave, valor));

  const resp = await fetch(URL_PUBLICACIONES, {
    method: "POST",
    body: datos
  });
  const json = await resp.json();

  // Si la sesion se cerro o vencio, se pide iniciar sesion otra vez
  if (json.login) {
    pedirSesion();
  }
  return json;
}

function animarIcono(icon) {
  if (!icon) {
    return;
  }
  icon.classList.add("animate");
  setTimeout(() => {
    icon.classList.remove("animate");
  }, 200);
}

// Todos los lugares donde aparece una publicacion (la pagina y el visor)
function contenedoresDe(idPub) {
  return document.querySelectorAll('[data-id-pub="' + idPub + '"]');
}

function pintarLike(btn, activo) {
  const icon = btn.querySelector(".like");
  icon.classList.toggle("ion-md-heart", activo);
  icon.classList.toggle("ion-md-heart-empty", !activo);
  btn.classList.toggle("active", activo);
}

function actualizarLikes(idPub, activo, total) {
  contenedoresDe(idPub).forEach((contenedor) => {
    contenedor.querySelectorAll(".like_btn").forEach((btn) => pintarLike(btn, activo));
    contenedor.querySelectorAll(".contador-likes").forEach((span) => {
      span.textContent = total;
    });
  });
}

function actualizarTotalComentarios(idPub, total) {
  contenedoresDe(idPub).forEach((contenedor) => {
    contenedor.querySelectorAll(".contador-comentarios").forEach((span) => {
      span.textContent = total;
    });
  });
}

function fotoUsuario(idUsuario) {
  return "../Controllers/ImagenController.php?tipo=foto&id=" + idUsuario;
}

// Crea el HTML de un comentario; si no es respuesta, tambien crea el espacio para sus respuestas
function crearComentario(comentario, esRespuesta) {
  const div = document.createElement("div");
  div.className = "comentario";
  div.dataset.idCom = comentario.id;
  if (!esRespuesta) {
    div.dataset.raiz = "1";
  }

  const img = document.createElement("img");
  img.className = "foto";
  img.alt = "foto";
  img.src = fotoUsuario(comentario.id_usuario);
  img.onerror = () => {
    img.onerror = null;
    img.src = "./resources/foto.jpg";
  };

  const cuerpo = document.createElement("div");
  cuerpo.className = "comentario-cuerpo";

  const burbuja = document.createElement("div");
  burbuja.className = "comentario-burbuja";

  const nombre = document.createElement("strong");
  nombre.textContent = comentario.nombre;

  const texto = document.createElement("p");
  texto.textContent = comentario.texto;

  burbuja.append(nombre, texto);

  const acciones = document.createElement("div");
  acciones.className = "comentario-acciones";

  const tiempo = document.createElement("span");
  tiempo.className = "comentario-tiempo";
  tiempo.textContent = comentario.hace;
  tiempo.title = comentario.fecha;

  const btnLike = document.createElement("button");
  btnLike.type = "button";
  btnLike.className = "btn-like-com";
  btnLike.textContent = "Me gusta";

  const btnResponder = document.createElement("button");
  btnResponder.type = "button";
  btnResponder.className = "btn-responder";
  btnResponder.textContent = "Responder";
  btnResponder.dataset.nombre = comentario.nombre;

  const total = document.createElement("span");
  total.className = "like-com-total";
  const corazon = document.createElement("i");
  corazon.className = "ion-md-heart";
  const contador = document.createElement("span");
  contador.className = "contador-like-com";
  total.append(corazon, contador);

  acciones.append(tiempo, btnLike, btnResponder, total);
  pintarLikeComentario(acciones, comentario.dio_like, comentario.likes);

  cuerpo.append(burbuja, acciones);

  if (!esRespuesta) {
    const respuestas = document.createElement("div");
    respuestas.className = "respuestas";
    (comentario.respuestas || []).forEach((respuesta) => {
      respuestas.appendChild(crearComentario(respuesta, true));
    });
    cuerpo.appendChild(respuestas);
  }

  div.append(img, cuerpo);
  return div;
}

function pintarLikeComentario(acciones, activo, total) {
  acciones.querySelector(".btn-like-com").classList.toggle("activo", activo);
  acciones.querySelector(".contador-like-com").textContent = total;
  acciones.querySelector(".like-com-total").hidden = total <= 0;
}

function mensajeLista(lista, texto) {
  lista.innerHTML = "";
  const p = document.createElement("p");
  p.className = "sin-comentarios";
  p.textContent = texto;
  lista.appendChild(p);
}

function pintarComentarios(lista, comentarios) {
  if (comentarios.length === 0) {
    mensajeLista(lista, "Aún no hay comentarios.");
    return;
  }

  lista.innerHTML = "";
  comentarios.forEach((comentario) => {
    lista.appendChild(crearComentario(comentario, false));
  });
}

async function cargarComentarios(lista, idPub) {
  mensajeLista(lista, "Cargando comentarios...");

  try {
    const json = await enviarAccion({ accion: "comentarios", id_pub: idPub });

    if (json.ok) {
      pintarComentarios(lista, json.comentarios);
    } else {
      mensajeLista(lista, json.mensaje);
    }
  } catch (error) {
    mensajeLista(lista, "No se pudieron cargar los comentarios.");
  }
}

// Agrega un comentario recien creado a la lista que esta en el mismo contenedor
function agregarComentarioNuevo(contenedor, comentario) {
  const lista = contenedor.querySelector(".lista-comentarios");
  if (!lista) {
    return;
  }

  if (comentario.id_padre) {
    const raiz = lista.querySelector('.comentario[data-raiz="1"][data-id-com="' + comentario.id_padre + '"]');
    if (raiz) {
      const respuestas = raiz.querySelector(".respuestas");
      const form = respuestas.querySelector(".form-respuesta");
      respuestas.insertBefore(crearComentario(comentario, true), form);
      return;
    }
  }

  const vacio = lista.querySelector(".sin-comentarios");
  if (vacio) {
    vacio.remove();
  }

  const nuevo = crearComentario(comentario, false);
  lista.appendChild(nuevo);
  nuevo.scrollIntoView({ block: "nearest" });
}

// Muestra la caja para responder debajo del comentario principal
function abrirFormRespuesta(btn) {
  const raiz = btn.closest('.comentario[data-raiz="1"]');
  if (!raiz) {
    return;
  }

  const respuestas = raiz.querySelector(".respuestas");
  let form = respuestas.querySelector(".form-respuesta");

  if (!form) {
    form = document.createElement("form");
    form.className = "form-comentario form-respuesta";
    form.dataset.idPadre = raiz.dataset.idCom;

    const input = document.createElement("input");
    input.type = "text";
    input.name = "texto";
    input.maxLength = 255;
    input.autocomplete = "off";
    input.className = "inputs";

    form.appendChild(input);
    respuestas.appendChild(form);
  }

  const input = form.querySelector("input");
  input.placeholder = "Responder a " + btn.dataset.nombre;
  input.focus();
}

async function darLikePublicacion(btn) {
  const contenedor = btn.closest("[data-id-pub]");
  if (!contenedor || !contenedor.dataset.idPub || btn.disabled) {
    return;
  }

  btn.disabled = true;
  animarIcono(btn.querySelector(".like"));

  try {
    const json = await enviarAccion({ accion: "like", id_pub: contenedor.dataset.idPub });

    if (json.ok) {
      actualizarLikes(contenedor.dataset.idPub, json.dio_like, json.total);
    } else {
      console.error(json.mensaje);
    }
  } catch (error) {
    console.error("No se pudo conectar con el servidor.");
  } finally {
    btn.disabled = false;
  }
}

async function darLikeComentario(btn) {
  const comentario = btn.closest(".comentario");
  if (!comentario || btn.disabled) {
    return;
  }

  btn.disabled = true;
  const idCom = comentario.dataset.idCom;

  try {
    const json = await enviarAccion({ accion: "like_com", id_com: idCom });

    if (json.ok) {
      document.querySelectorAll('.comentario[data-id-com="' + idCom + '"]').forEach((elemento) => {
        const acciones = elemento.querySelector(".comentario-cuerpo > .comentario-acciones");
        pintarLikeComentario(acciones, json.dio_like, json.total);
      });
    } else {
      console.error(json.mensaje);
    }
  } catch (error) {
    console.error("No se pudo conectar con el servidor.");
  } finally {
    btn.disabled = false;
  }
}

async function enviarComentario(form) {
  const contenedor = form.closest("[data-id-pub]");
  const input = form.querySelector("input[name='texto']");
  const texto = input.value.trim();

  if (!contenedor || !contenedor.dataset.idPub || texto === "") {
    return;
  }

  const campos = {
    accion: "comentar",
    id_pub: contenedor.dataset.idPub,
    texto: texto
  };

  if (form.dataset.idPadre) {
    campos.id_padre = form.dataset.idPadre;
  }

  input.disabled = true;

  try {
    const json = await enviarAccion(campos);

    if (json.ok) {
      input.value = "";
      actualizarTotalComentarios(contenedor.dataset.idPub, json.total);
      contenedoresDe(contenedor.dataset.idPub).forEach((c) => {
        agregarComentarioNuevo(c, json.comentario);
      });
    } else {
      console.error(json.mensaje);
    }
  } catch (error) {
    console.error("No se pudo conectar con el servidor.");
  } finally {
    input.disabled = false;
    input.focus();
  }
}

// Los eventos se escuchan en todo el documento para que tambien funcionen
// en los comentarios que se agregan despues y dentro del visor
document.addEventListener("click", (e) => {
  const btnLike = e.target.closest(".like_btn");
  if (btnLike) {
    darLikePublicacion(btnLike);
    return;
  }

  const btnLikeCom = e.target.closest(".btn-like-com");
  if (btnLikeCom) {
    darLikeComentario(btnLikeCom);
    return;
  }

  const btnResponder = e.target.closest(".btn-responder");
  if (btnResponder) {
    abrirFormRespuesta(btnResponder);
    return;
  }

  const btnAnimado = e.target.closest(".comment_btn, .guardar_btn");
  if (btnAnimado) {
    btnAnimado.classList.toggle("active");
    animarIcono(btnAnimado.querySelector(".int"));
  }
});

document.addEventListener("submit", (e) => {
  const form = e.target.closest(".form-comentario");
  if (!form) {
    return;
  }

  e.preventDefault();
  enviarComentario(form);
});

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".like_btn[data-liked]").forEach((btn) => {
    pintarLike(btn, btn.dataset.liked === "1");
  });

  // En verPublicacion.php la lista de comentarios se llena al abrir la pagina
  document.querySelectorAll(".lista-comentarios[data-cargar]").forEach((lista) => {
    const contenedor = lista.closest("[data-id-pub]");
    if (contenedor) {
      cargarComentarios(lista, contenedor.dataset.idPub);
    }
  });
});
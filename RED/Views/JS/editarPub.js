const editor = document.getElementById("editarPubli");

if (editor) {
  const formEditar = document.getElementById("form-editar-publi");
  const textoEditar = formEditar.querySelector("textarea");
  const inputArchivo = document.getElementById("editar-archivo");
  const previewEditar = document.getElementById("editar-preview");
  const btnQuitarArchivo = document.getElementById("editar-quitar");
  const mensajeEditar = document.getElementById("editar-mensaje");
  const btnGuardar = formEditar.querySelector(".btn-publicar");

  const confirmar = document.getElementById("confirmarEliminar");
  const btnAceptarEliminar = document.getElementById("aceptarEliminar");
  const mensajeEliminar = document.getElementById("eliminar-mensaje");

  let idPubActual = null;
  let quitarMedia = false;

  function cerrarMenus(excepto) {
    document.querySelectorAll(".post-menu.abierto").forEach((menu) => {
      if (menu !== excepto) {
        menu.classList.remove("abierto");
      }
    });
  }

  // Llena el editor con el texto y el archivo que ya tiene la publicacion
  function abrirEditor(post) {
    idPubActual = post.dataset.idPub;
    quitarMedia = false;
    inputArchivo.value = "";
    mensajeEditar.textContent = "";

    const texto = post.querySelector(".post-text");
    textoEditar.value = texto ? texto.textContent.trim() : "";

    const video = post.querySelector(".post-video");
    const img = post.querySelector(".post-img");

    if (video) {
      mostrarPreviaMedia(previewEditar, video.currentSrc || video.src, true);
    } else if (img) {
      mostrarPreviaMedia(previewEditar, img.src, false);
    } else {
      ocultarPreviaMedia(previewEditar);
    }

    editor.classList.add("active");
    textoEditar.focus();
  }

  function cerrarEditor() {
    editor.classList.remove("active");
    ocultarPreviaMedia(previewEditar);
    idPubActual = null;
  }

  function abrirConfirmacion(post) {
    idPubActual = post.dataset.idPub;
    mensajeEliminar.textContent = "";
    btnAceptarEliminar.disabled = false;
    confirmar.classList.add("activo");
  }

  function cerrarConfirmacion() {
    confirmar.classList.remove("activo");
    idPubActual = null;
  }

  function elegirArchivo(tipos) {
    inputArchivo.accept = tipos.join(",");
    inputArchivo.click();
  }

  document.addEventListener("click", (e) => {
    const btnMenu = e.target.closest(".post-menu-btn");
    if (btnMenu) {
      const menu = btnMenu.closest(".post-menu");
      cerrarMenus(menu);
      menu.classList.toggle("abierto");
      return;
    }

    const btnEditar = e.target.closest(".post-editar");
    if (btnEditar) {
      cerrarMenus();
      abrirEditor(btnEditar.closest(".post"));
      return;
    }

    const btnEliminar = e.target.closest(".post-eliminar");
    if (btnEliminar) {
      cerrarMenus();
      abrirConfirmacion(btnEliminar.closest(".post"));
      return;
    }

    if (!e.target.closest(".post-menu")) {
      cerrarMenus();
    }

    if (editor.classList.contains("active") && !e.target.closest("#editarPubli")) {
      cerrarEditor();
    }

    if (e.target === confirmar) {
      cerrarConfirmacion();
    }
  });

  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") {
      return;
    }
    if (editor.classList.contains("active")) {
      cerrarEditor();
    }
    if (confirmar.classList.contains("activo")) {
      cerrarConfirmacion();
    }
  });

  document.getElementById("editarCerrar").addEventListener("click", cerrarEditor);
  document.getElementById("cancelarEliminar").addEventListener("click", cerrarConfirmacion);
  document.getElementById("editar-btn-foto").addEventListener("click", () => elegirArchivo(MEDIA_TIPOS_FOTO));
  document.getElementById("editar-btn-video").addEventListener("click", () => elegirArchivo(MEDIA_TIPOS_VIDEO));

  inputArchivo.addEventListener("change", () => {
    const archivo = inputArchivo.files[0];
    mensajeEditar.textContent = "";

    if (!archivo) {
      return;
    }

    const error = validarArchivoMedia(archivo);
    if (error !== "") {
      mensajeEditar.textContent = error;
      inputArchivo.value = "";
      return;
    }

    quitarMedia = false;
    mostrarPreviaMedia(previewEditar, URL.createObjectURL(archivo), archivo.type.startsWith("video/"));
  });

  btnQuitarArchivo.addEventListener("click", () => {
    inputArchivo.value = "";
    ocultarPreviaMedia(previewEditar);
    quitarMedia = true;
  });

  formEditar.addEventListener("submit", async (e) => {
    e.preventDefault();

    const texto = textoEditar.value.trim();
    const archivoNuevo = inputArchivo.files[0];
    const tendraArchivo = archivoNuevo !== undefined || previewEditar.style.display !== "none";

    if (texto === "" && !tendraArchivo) {
      mensajeEditar.textContent = "La publicación necesita texto o una foto o video.";
      return;
    }

    const datos = new FormData();
    datos.append("accion", "editar");
    datos.append("id_pub", idPubActual);
    datos.append("texto", texto);
    datos.append("quitar_media", quitarMedia ? "1" : "0");
    if (archivoNuevo) {
      datos.append("archivo", archivoNuevo);
    }

    btnGuardar.disabled = true;
    btnGuardar.textContent = "Guardando...";
    mensajeEditar.textContent = "";

    try {
      const json = await (await fetch(URL_PUBLICACIONES, { method: "POST", body: datos })).json();

      if (json.ok) {
        window.location.reload();
        return;
      }
      mensajeEditar.textContent = json.mensaje;
    } catch (error) {
      mensajeEditar.textContent = "No se pudo conectar con el servidor.";
    }

    btnGuardar.disabled = false;
    btnGuardar.textContent = "Guardar";
  });

  btnAceptarEliminar.addEventListener("click", async () => {
    if (!idPubActual) {
      return;
    }

    btnAceptarEliminar.disabled = true;
    mensajeEliminar.textContent = "";

    try {
      const json = await enviarAccion({ accion: "eliminar", id_pub: idPubActual });

      if (!json.ok) {
        mensajeEliminar.textContent = json.mensaje;
        btnAceptarEliminar.disabled = false;
        return;
      }

      // En la pagina de la publicacion ya no hay nada que ver, se regresa al inicio
      if (window.location.pathname.includes("verPublicacion")) {
        window.location.href = "index.php";
        return;
      }

      document.querySelectorAll('.post[data-id-pub="' + idPubActual + '"]').forEach((post) => post.remove());
      cerrarConfirmacion();
    } catch (error) {
      mensajeEliminar.textContent = "No se pudo conectar con el servidor.";
      btnAceptarEliminar.disabled = false;
    }
  });
}
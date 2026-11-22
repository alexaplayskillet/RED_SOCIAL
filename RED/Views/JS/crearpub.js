const crearTrigger = document.getElementById("crear");
const publicarBtn = document.querySelector(".create-post-btn");
const publi = document.querySelector(".crear-publi");

if (publi) {
  const formPubli = document.getElementById("form-crear-publi");
  const inputImagen = document.getElementById("publi-imagen");
  const btnFoto = document.getElementById("publi-btn-foto");
  const btnVideo = document.getElementById("publi-btn-video");
  const preview = document.getElementById("publi-preview");
  const btnQuitar = document.getElementById("publi-quitar-imagen");
  const mensaje = document.getElementById("publi-mensaje");
  const btnEnviar = formPubli.querySelector(".btn-publicar");
  const btnCerrar = publi.querySelector(".cerrar-publi");

  function abrirCrearPubli(e) {
    e.stopPropagation();
    publi.classList.toggle("active");
  }

  function cerrarCrearPubli() {
    publi.classList.remove("active");
    preview.querySelector("video").pause();
  }

  function quitarArchivo() {
    inputImagen.value = "";
    ocultarPreviaMedia(preview);
  }

  // El mismo campo sirve para foto o video; solo cambia que archivos deja elegir
  function elegirArchivo(tipos) {
    inputImagen.accept = tipos.join(",");
    inputImagen.click();
  }

  if (crearTrigger) {
    crearTrigger.addEventListener("click", abrirCrearPubli);
  }

  if (publicarBtn) {
    publicarBtn.addEventListener("click", abrirCrearPubli);
  }

  btnCerrar.addEventListener("click", cerrarCrearPubli);

  document.addEventListener("click", (e) => {
    const dentro = e.target.closest(".crear-publi") || e.target.closest("#crear") || e.target.closest(".create-post-btn");
    if (!dentro && publi.classList.contains("active")) {
      cerrarCrearPubli();
    }
  });

  btnFoto.addEventListener("click", () => elegirArchivo(MEDIA_TIPOS_FOTO));
  btnVideo.addEventListener("click", () => elegirArchivo(MEDIA_TIPOS_VIDEO));

  inputImagen.addEventListener("change", () => {
    const archivo = inputImagen.files[0];
    mensaje.textContent = "";

    if (!archivo) {
      quitarArchivo();
      return;
    }

    const error = validarArchivoMedia(archivo);
    if (error !== "") {
      mensaje.textContent = error;
      quitarArchivo();
      return;
    }

    mostrarPreviaMedia(preview, URL.createObjectURL(archivo), archivo.type.startsWith("video/"));
  });

  btnQuitar.addEventListener("click", quitarArchivo);

  formPubli.addEventListener("submit", async (e) => {
    e.preventDefault();

    const texto = formPubli.texto.value.trim();

    if (texto === "" && inputImagen.files.length === 0) {
      mensaje.textContent = "Escribe algo o agrega una foto o video.";
      return;
    }

    const datos = new FormData(formPubli);
    datos.append("accion", "crear");

    btnEnviar.disabled = true;
    btnEnviar.textContent = "Publicando...";
    mensaje.textContent = "";

    try {
      const resp = await fetch("../Controllers/PublicacionController.php", {
        method: "POST",
        body: datos
      });
      const json = await resp.json();

      if (json.ok) {
        window.location.reload();
        return;
      }
      mensaje.textContent = json.mensaje;
    } catch (error) {
      mensaje.textContent = "No se pudo conectar con el servidor.";
    }

    btnEnviar.disabled = false;
    btnEnviar.textContent = "Publicar";
  });
}
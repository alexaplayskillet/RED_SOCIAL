const visor = document.getElementById("visor");

if (visor) {
  const visorImg = document.getElementById("visorImg");
  const visorPanel = document.getElementById("visorPanel");
  const visorFoto = document.getElementById("visorAutorFoto");
  const visorNombre = document.getElementById("visorNombre");
  const visorTiempo = document.getElementById("visorTiempo");
  const visorTexto = document.getElementById("visorTexto");
  const visorLike = document.getElementById("visorLike");
  const visorContLikes = document.getElementById("visorContLikes");
  const visorContComentarios = document.getElementById("visorContComentarios");
  const visorComentarios = document.getElementById("visorComentarios");
  const visorInput = visorPanel.querySelector(".visor-form input");

  function textoDe(post, selector) {
    const elemento = post.querySelector(selector);
    return elemento ? elemento.textContent.trim() : "";
  }

  // Copia los datos de la publicacion al visor y carga sus comentarios
  function abrirVisor(post) {
    const imagen = post.querySelector(".post-img");
    const enlace = post.querySelector(".post-enlace");
    const btnLike = post.querySelector(".like_btn");
    const texto = textoDe(post, ".post-text");

    visorImg.src = imagen.src;
    visorPanel.dataset.idPub = post.dataset.idPub;

    visorFoto.src = post.querySelector(".userinfo .foto").src;
    visorNombre.textContent = textoDe(post, ".username p:first-child");
    visorTiempo.textContent = enlace ? enlace.textContent.trim() : "";
    visorTiempo.title = enlace ? enlace.title : "";

    visorTexto.textContent = texto;
    visorTexto.style.display = texto === "" ? "none" : "";

    pintarLike(visorLike, btnLike ? btnLike.classList.contains("active") : false);
    visorContLikes.textContent = textoDe(post, ".contador-likes") || "0";
    visorContComentarios.textContent = textoDe(post, ".contador-comentarios") || "0";

    visorInput.value = "";
    visor.classList.add("activo");
    visor.setAttribute("aria-hidden", "false");
    document.body.classList.add("visor-abierto");

    cargarComentarios(visorComentarios, post.dataset.idPub);
  }

  function cerrarVisor() {
    visor.classList.remove("activo");
    visor.setAttribute("aria-hidden", "true");
    document.body.classList.remove("visor-abierto");

    visorImg.removeAttribute("src");
    visorPanel.dataset.idPub = "";
    visorComentarios.innerHTML = "";
  }

  document.addEventListener("click", (e) => {
    const imagen = e.target.closest(".post .post-img");
    if (imagen) {
      e.preventDefault();
      abrirVisor(imagen.closest(".post"));
    }
  });

  document.getElementById("visorCerrar").addEventListener("click", cerrarVisor);

  document.getElementById("visorBtnComentar").addEventListener("click", () => {
    visorInput.focus();
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && visor.classList.contains("activo")) {
      cerrarVisor();
    }
  });
}
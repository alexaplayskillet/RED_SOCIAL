/* mostrar/ocultar panel de crear publicación */
const crearTrigger = document.getElementById("crear");
const publicarBtn = document.querySelector(".create-post-btn");
const publi = document.querySelector(".crear-publi");

function abrirCrearPubli(e){
  e.stopPropagation();
  publi.classList.toggle("active");
}

crearTrigger.addEventListener("click", abrirCrearPubli);
publicarBtn.addEventListener("click", abrirCrearPubli);

document.addEventListener("click", (e) => {
  const isClickInside = e.target.closest(".crear-publi") || e.target.closest("#crear") || e.target.closest(".create-post-btn");
  if (!isClickInside && publi.classList.contains("active")) {
    publi.classList.remove("active");
  }
});
/* mostrar/ocultar panel de crear publicación */
const crearTrigger = document.getElementById("crear");
const publi = document.querySelector(".crear-publi");

crearTrigger.addEventListener("click", (e) => {
  e.stopPropagation();
  publi.classList.toggle("active");
});

document.addEventListener("click", (e) => {
  const isClickInside = e.target.closest(".crear-publi") || e.target.closest("#crear");
  if (!isClickInside && publi.classList.contains("active")) {
    publi.classList.remove("active");
  }
});
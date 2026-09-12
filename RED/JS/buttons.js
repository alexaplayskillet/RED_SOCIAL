/*menu hamburguesa */
const toggleBtns = document.querySelectorAll(".hamburger");
const menu = document.querySelector(".menu");
const bodyContainer = document.querySelector(".body");

toggleBtns.forEach(btn => {
  btn.addEventListener("click", () => {
    menu.classList.toggle("active");
    bodyContainer.classList.toggle("menu-active");
  });
});

/*menu de usuario */
const profileTriggers = document.querySelectorAll(".profile");
  const menu2 = document.querySelector(".menu2");

  profileTriggers.forEach(trigger => {
    trigger.addEventListener("click", (e) => {
      e.stopPropagation(); 
      menu2.classList.toggle("active");
    });
  });

  document.addEventListener("click", (e) => {
    const isClickInside = e.target.closest(".menu2") || e.target.closest(".profile");
    if (!isClickInside && menu2.classList.contains("active")) {
      menu2.classList.remove("active");
    }
  });

  /*carrusel de historias */
const storiesContainer = document.getElementById("storiesContainer");
const flechaIzq = document.querySelector(".stories-flecha-izq");
const flechaDer = document.querySelector(".stories-flecha-der");

if (storiesContainer && flechaIzq && flechaDer) {
  const scrollAmount = 220; 

  flechaIzq.addEventListener("click", () => {
    storiesContainer.scrollBy({ left: -scrollAmount, behavior: "smooth" });
  });

  flechaDer.addEventListener("click", () => {
    storiesContainer.scrollBy({ left: scrollAmount, behavior: "smooth" });
  });
}

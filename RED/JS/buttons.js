/*menu hamburguesa */
const toggleBtn = document.getElementById("toggleMenu");
const menu = document.querySelector(".menu");
const bodyContainer = document.querySelector(".body");

toggleBtn.addEventListener("click", () => {
  menu.classList.toggle("active");
  bodyContainer.classList.toggle("menu-active");
});

/*menu de usuario */
const profileTriggers = document.querySelectorAll(".profile");
  const menu2 = document.querySelector(".menu2");

  profileTriggers.forEach(trigger => {
    trigger.addEventListener("click", (e) => {
      e.stopPropagation(); // evita que el clic cierre el menu inmediatamente
      menu2.classList.toggle("active");
    });
  });

  document.addEventListener("click", (e) => {
    const isClickInside = e.target.closest(".menu2") || e.target.closest(".profile");
    if (!isClickInside && menu2.classList.contains("active")) {
      menu2.classList.remove("active");
    }
  });

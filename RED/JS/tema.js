
const THEME_KEY = "cosmix-tema";


function aplicarTema(tema) {
    const esClaro = tema === "light";

    document.documentElement.classList.toggle("modo-claro", esClaro);
    document.documentElement.classList.toggle("modo-oscuro", !esClaro);

    const botonClaro = document.getElementById("themeLight");
    const botonOscuro = document.getElementById("themeDark");

    if (botonClaro) {
        botonClaro.classList.toggle("active", esClaro);
        botonClaro.setAttribute("aria-pressed", String(esClaro));
    }

    if (botonOscuro) {
        botonOscuro.classList.toggle("active", !esClaro);
        botonOscuro.setAttribute("aria-pressed", String(!esClaro));
    }
}


function setTheme(tema) {
    if (tema !== "light" && tema !== "dark") return;

    localStorage.setItem(THEME_KEY, tema);
    aplicarTema(tema);
}

// Cargar el tema guardado
function cargarTema() {
    const temaGuardado = localStorage.getItem(THEME_KEY) || "dark";
    aplicarTema(temaGuardado);
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", cargarTema);
} else {
    cargarTema();
}


window.addEventListener("storage", function(event) {
    if (event.key === THEME_KEY) {
        cargarTema();
    }
});

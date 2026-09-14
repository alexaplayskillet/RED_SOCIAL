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

/*Accesibilidad*/
// ---- Navegación entre menú de perfil y accesibilidad ----
function toggleAccessMenu(show){
    document.getElementById('profileMenu').classList.remove('active');
    document.getElementById('accessMenu').classList.toggle('active', show);
    if(!show){
        document.getElementById('profileMenu').classList.add('active');
    }
}

// ---- Modo claro / oscuro ----
function setTheme(mode){
    document.getElementById('themeLight').classList.toggle('active', mode === 'light');
    document.getElementById('themeDark').classList.toggle('active', mode === 'dark');
    document.body.classList.toggle('light-mode', mode === 'light');
}

// ---- Tamaño de letra ----
document.addEventListener("DOMContentLoaded", () => {
    const fontValue = document.getElementById('fontValue');
    const root = document.documentElement;

    const getFontScale = () => {
        const val = getComputedStyle(root).getPropertyValue('--font-size').trim();
        return parseFloat(val) || 1;
    };

    const updateFontDisplay = () => {
        fontValue.textContent = Math.round(getFontScale() * 100) + '%';
    };

    document.getElementById('fontIncrease').addEventListener('click', () => {
        const newSize = Math.min(getFontScale() + 0.1, 1.5);
        root.style.setProperty('--font-size', `${newSize}em`);
        updateFontDisplay();
    });

    document.getElementById('fontDecrease').addEventListener('click', () => {
        const newSize = Math.max(getFontScale() - 0.1, 0.7);
        root.style.setProperty('--font-size', `${newSize}em`);
        updateFontDisplay();
    });

    updateFontDisplay();

    // ---- Alto contraste ----
    document.getElementById('contrastToggle').addEventListener('change', (e) => {
        document.body.classList.toggle('high-contrast', e.target.checked);
    });
});

function setupImageUpload(inputId, previewId, hideElementId){
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    input.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if(!file) return;
        const reader = new FileReader();
        reader.onload = (ev) => {
            preview.src = ev.target.result;
            preview.style.display = 'block';
            if(hideElementId) document.getElementById(hideElementId).style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
}
setupImageUpload('coverUpload', 'coverPreview');
setupImageUpload('avatarUpload', 'avatarPreview', 'avatarInitials');

document.querySelectorAll('.profile-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        // Quitar "active" de todas las pestañas y ponerlo solo en la clickeada
        document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        // Ocultar todos los paneles y mostrar solo el correspondiente
        const nombre = tab.dataset.tab;
        document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
        const panelDestino = document.getElementById('tab-' + nombre);
        if (panelDestino) panelDestino.classList.add('active');
    });
});

// Para los links "Ver todos los amigos" / "Ver toda la galería"
document.querySelectorAll('[data-tab-link]').forEach(link => {
    link.addEventListener('click', () => {
        const nombre = link.dataset.tabLink;
        const tabCorrespondiente = document.querySelector(`.profile-tab[data-tab="${nombre}"]`);
        if (tabCorrespondiente) tabCorrespondiente.click();
    });
});

document.querySelectorAll('.fotos-subtab').forEach(subtab => {
    subtab.addEventListener('click', () => {
        document.querySelectorAll('.fotos-subtab').forEach(t => t.classList.remove('active'));
        subtab.classList.add('active');
        // aquí luego puedes cargar/filtrar el contenido según subtab.dataset.subtab
    });
});


//Reportes estadisticas
document.getElementById('btnGenerarReporte').addEventListener('click', () => {
    const tipoSeleccionado = document.getElementById('tipoReporte').value;

    // Oculta el placeholder
    document.getElementById('reportePlaceholder').style.display = 'none';

    // Oculta todos los reportes y muestra solo el elegido
    document.querySelectorAll('.reporte-contenido').forEach(p => p.classList.remove('active'));
    const panelElegido = document.querySelector(`[data-reporte-panel="${tipoSeleccionado}"]`);
    if(panelElegido){
        panelElegido.classList.add('active');

        // Si es el reporte general, inicializa contadores y gráfico
        if(tipoSeleccionado === 'general'){
            inicializarEstadisticas();
        }
    }
});
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

if (menu2) {
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
}

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
function toggleAccessMenu(show){
    document.getElementById('profileMenu').classList.remove('active');
    document.getElementById('accessMenu').classList.toggle('active', show);
    if(!show){
        document.getElementById('profileMenu').classList.add('active');
    }
}

function setTheme(mode){
    document.getElementById('themeLight').classList.toggle('active', mode === 'light');
    document.getElementById('themeDark').classList.toggle('active', mode === 'dark');
    document.body.classList.toggle('light-mode', mode === 'light');
}

document.addEventListener("DOMContentLoaded", () => {
    const fontValue = document.getElementById('fontValue');
    const fontIncrease = document.getElementById('fontIncrease');
    const fontDecrease = document.getElementById('fontDecrease');
    const contrastToggle = document.getElementById('contrastToggle');
    const root = document.documentElement;

    contrastToggle?.addEventListener('change', (e) => {
        document.body.classList.toggle('high-contrast', e.target.checked);
    });

    if (!fontValue || !fontIncrease || !fontDecrease) return;

    const getFontScale = () => {
        const val = getComputedStyle(root).getPropertyValue('--font-size').trim();
        return parseFloat(val) || 1;
    };

    const updateFontDisplay = () => {
        fontValue.textContent = Math.round(getFontScale() * 100) + '%';
    };

    fontIncrease.addEventListener('click', () => {
        const newSize = Math.min(getFontScale() + 0.1, 1.5);
        root.style.setProperty('--font-size', `${newSize}em`);
        updateFontDisplay();
    });

    fontDecrease.addEventListener('click', () => {
        const newSize = Math.max(getFontScale() - 0.1, 0.7);
        root.style.setProperty('--font-size', `${newSize}em`);
        updateFontDisplay();
    });

    updateFontDisplay();
});

/*Imagenes de perfil: vista previa y guardado en la base de datos */
const URL_IMAGEN_PERFIL = '../Controllers/PerfilImagenController.php';
const MAX_BYTES_IMAGEN = 5 * 1024 * 1024;

function setupImageUpload(inputId, previewId, hideElementId, tipo){
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if(!input || !preview) return;

    input.addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if(!file) return;

        if(file.size > MAX_BYTES_IMAGEN){
            alert('La imagen pesa más de 5 MB');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (ev) => {
            preview.src = ev.target.result;
            preview.style.display = 'block';
            if(hideElementId) document.getElementById(hideElementId).style.display = 'none';
        };
        reader.readAsDataURL(file);

        const datos = new FormData();
        datos.append('accion', 'guardar');
        datos.append('tipo', tipo);
        datos.append('imagen', file);

        try {
            const resp = await fetch(URL_IMAGEN_PERFIL, { method: 'POST', body: datos });
            const r = await resp.json();
            if(!r.ok) alert(r.mensaje);
        } catch (err) {
            alert('No se pudo guardar la imagen');
        }
        input.value = '';
    });
}

async function eliminarImagen(tipo, previewId, mostrarElementId){
    if(!confirm('¿Eliminar esta imagen?')) return;

    const datos = new FormData();
    datos.append('accion', 'eliminar');
    datos.append('tipo', tipo);

    try {
        const resp = await fetch(URL_IMAGEN_PERFIL, { method: 'POST', body: datos });
        const r = await resp.json();
        if(!r.ok){
            alert(r.mensaje);
            return;
        }

        const preview = document.getElementById(previewId);
        preview.removeAttribute('src');
        preview.style.display = 'none';
        if(mostrarElementId) document.getElementById(mostrarElementId).style.display = '';
    } catch (err) {
        alert('No se pudo eliminar la imagen');
    }
}

setupImageUpload('coverUpload', 'coverPreview', null, 'portada');
setupImageUpload('avatarUpload', 'avatarPreview', 'avatarInitials', 'foto');

document.getElementById('btnEliminarPortada')
    ?.addEventListener('click', () => eliminarImagen('portada', 'coverPreview'));

document.querySelectorAll('.profile-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        const nombre = tab.dataset.tab;
        document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
        const panelDestino = document.getElementById('tab-' + nombre);
        if (panelDestino) panelDestino.classList.add('active');
    });
});

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
    });
});

//Reportes estadisticas
document.getElementById('btnGenerarReporte')?.addEventListener('click', () => {
    const tipoSeleccionado = document.getElementById('tipoReporte').value;

    document.getElementById('reportePlaceholder').style.display = 'none';

    document.querySelectorAll('.reporte-contenido').forEach(p => p.classList.remove('active'));
    const panelElegido = document.querySelector(`[data-reporte-panel="${tipoSeleccionado}"]`);
    if(panelElegido){
        panelElegido.classList.add('active');

        if(tipoSeleccionado === 'general' && typeof inicializarEstadisticas === 'function'){
            inicializarEstadisticas();
        }
    }
});

//Notificaciones
const notifMenu = document.getElementById('notifMenu');
const notifTrigger = document.getElementById('notifTrigger');

if(notifTrigger){
    notifTrigger.addEventListener('click', (e) => {
        e.stopPropagation();
        notifMenu.classList.toggle('active');
    });
}

document.addEventListener('click', (e) => {
    const isClickInside = e.target.closest('.notif-menu') || e.target.closest('#notifTrigger');
    if(notifMenu && !isClickInside && notifMenu.classList.contains('active')){
        notifMenu.classList.remove('active');
    }
});

document.querySelectorAll('.notif-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.notif-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
    });
});

const notifAviso = document.getElementById('notifAviso');
document.getElementById('notifAvisoCerrar')?.addEventListener('click', () => {
    if(notifAviso) notifAviso.style.display = 'none';
});
document.getElementById('notifAhoraNo')?.addEventListener('click', () => {
    if(notifAviso) notifAviso.style.display = 'none';
});
const historiaOverlay = document.getElementById('historiaOverlay');

const historiaImagenInput = document.getElementById('historiaImagen');
const historiaVideoInput = document.getElementById('historiaVideo');

const historiaPreview = document.getElementById('historiaPreview');
const historiaPreviewContenido = document.getElementById('historiaPreviewContenido');

const historiaFiltros = document.getElementById('historiaFiltros');
const historiaPublicar = document.getElementById('historiaPublicar');

let archivoHistoria = null;
let tipoHistoria = null;

function abrirHistoria() {
    historiaOverlay.classList.add('activo');
}

function cerrarHistoria() {
    historiaOverlay.classList.remove('activo');

    historiaImagenInput.value = '';
    historiaVideoInput.value = '';

    historiaPreviewContenido.innerHTML = '';
    historiaPreview.classList.remove('activo');

    historiaFiltros.classList.remove('activo');

    historiaPublicar.disabled = true;

    archivoHistoria = null;
    tipoHistoria = null;
}

function seleccionarImagen() {
    historiaImagenInput.click();
}

function seleccionarVideo() {
    historiaVideoInput.click();
}

historiaImagenInput.addEventListener('change', function () {
    mostrarVistaPrevia(this.files[0], 'imagen');
});

historiaVideoInput.addEventListener('change', function () {
    mostrarVistaPrevia(this.files[0], 'video');
});

function mostrarVistaPrevia(file, tipo) {

    if (!file) return;

    archivoHistoria = file;
    tipoHistoria = tipo;

    historiaPreviewContenido.innerHTML = '';

    const url = URL.createObjectURL(file);

    if (tipo === 'imagen') {

        const img = document.createElement('img');

        img.src = url;
        img.alt = 'Vista previa de la historia';

        historiaPreviewContenido.appendChild(img);

    } else {

        const video = document.createElement('video');

        video.src = url;
        video.controls = true;
        video.muted = true;
        video.playsInline = true;

        historiaPreviewContenido.appendChild(video);
    }

    historiaPreview.classList.add('activo');

    historiaFiltros.classList.add('activo');

    historiaPublicar.disabled = false;
}

function publicarHistoria() {

    if (!archivoHistoria) return;

    const stories = document.getElementById('storiesContainer');

    const story = document.createElement('div');
    story.className = 'story historia-nueva';

    const storyRing = document.createElement('div');
    storyRing.className = 'story-ring';

    const ringContenido = document.createElement('div');

    const url = URL.createObjectURL(archivoHistoria);

    if (tipoHistoria === 'imagen') {

        const img = document.createElement('img');

        img.src = url;
        img.alt = 'Historia';

        ringContenido.appendChild(img);

    } else {

        const video = document.createElement('video');

        video.src = url;
        video.muted = true;
        video.autoplay = true;
        video.loop = true;
        video.playsInline = true;

        ringContenido.appendChild(video);
    }

    storyRing.appendChild(ringContenido);
    story.appendChild(storyRing);

    const nombre = document.createElement('span');
    nombre.textContent = '@usuario';

    story.appendChild(nombre);

    stories.appendChild(story);

    cerrarHistoria();
}

historiaOverlay.addEventListener('click', function (evento) {

    if (evento.target === historiaOverlay) {
        cerrarHistoria();
    }

});


/* ===== SELECCIÓN DE FILTRO ===== */

const botonesFiltros = document.querySelectorAll('.historia-filtro-btn');

botonesFiltros.forEach(boton => {

    boton.addEventListener('click', function () {

        botonesFiltros.forEach(btn => {
            btn.classList.remove('active');
        });

        this.classList.add('active');

    });

});
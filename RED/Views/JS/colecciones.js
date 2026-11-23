const API = '../Controllers/ColeccionController.php'; // ajusta la ruta según tu carpeta

let idColeccionActual = null;


function separarEmoji(nombre) {
  const m = nombre.match(/^(\p{Extended_Pictographic}[\uFE0F\u200D\p{Extended_Pictographic}]*)\s+(.*)$/u);
  return m ? { emoji: m[1], texto: m[2] } : { emoji: '📁', texto: nombre };
}

async function cargarColecciones() {
  const r = await fetch(`${API}?accion=listar`);
  const json = await r.json();
  const cont = document.getElementById('lista-colecciones');
  cont.innerHTML = '';
  cont.className = 'lista-colecciones';

  if (!json.ok) { cont.textContent = json.error; return; }
  if (json.datos.length === 0) { cont.textContent = 'Aún no tienes colecciones.'; return; }

  json.datos.forEach(c => {
    const { emoji, texto } = separarEmoji(c.nombre);
    const total = Number(c.total);

    const card = document.createElement('div');
    card.className = 'coleccion-menu';
    card.dataset.id = c.id_tablero;
    if (Number(c.id_tablero) === idColeccionActual) card.classList.add('activo');

    card.innerHTML = `
      <div class="mini-coleccion nueva"><span>${esc(emoji)}</span></div>
      <div class="info-menu-coleccion">
        <span>${esc(texto)}</span>
        <small>${total} ${total === 1 ? 'publicación' : 'publicaciones'}</small>
      </div>`;

    /* toda la tarjeta es clicable */
    card.addEventListener('click', () => {
      cont.querySelectorAll('.coleccion-menu').forEach(i => i.classList.remove('activo'));
      card.classList.add('activo');
      verGuardados(c.id_tablero, texto, emoji);
    });

    cont.appendChild(card);
  });
}

const RUTA_MEDIA = '../Controllers/MediaController.php?id=';
const RUTA_FOTO  = '../Controllers/ImagenController.php?tipo=FOTO&id=';

function esc(t) {
  const d = document.createElement('div');
  d.textContent = t ?? '';
  return d.innerHTML;
}

function formatearDuracion(seg) {
  if (!isFinite(seg)) return '';
  const m = Math.floor(seg / 60);
  const s = Math.floor(seg % 60);
  return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
}

function crearTarjeta(p, idTablero) {
  const esVideo   = (p.tipo_multimedia || '').startsWith('video/');
  const tieneMedia = Number(p.tiene_media) === 1;
  const src       = RUTA_MEDIA + p.id_pub;

  let miniatura;
  if (!tieneMedia) {
    miniatura = '<div class="sin-media"><ion-icon name="document-text-outline"></ion-icon></div>';
  } else if (esVideo) {
    miniatura = `<video src="${src}#t=0.1" preload="metadata" muted playsinline></video>
                 <div class="play-publicacion"><ion-icon name="play"></ion-icon></div>
                 <span class="duracion">--:--</span>`;
  } else {
    miniatura = `<img src="${src}" alt="" loading="lazy">`;
  }

  const card = document.createElement('article');
  card.className = 'publicacion-guardada';
  card.innerHTML = `
    <div class="imagen-publicacion">${miniatura}</div>

    <div class="datos-publicacion">
      <h3>${esc(p.texto) || 'Publicación sin texto'}</h3>

      <span class="tipo-publicacion">${esVideo ? 'Reels' : 'Publicación'} · ${esc(p.tag)}</span>

      <div class="usuario-publicacion">
        <img src="${RUTA_FOTO}${Number(p.id_usuarioP)}" alt="">
        <span>Se guardó desde la publicación de <b>${esc(p.tag)}</b></span>
      </div>

      <div class="acciones-publicacion">
        <button class="agregar-coleccion">
          <ion-icon name="bookmark-outline"></ion-icon>
          Agregar a colección
        </button>
        <button class="accion" data-accion="compartir" title="Compartir">
          <ion-icon name="share-outline"></ion-icon>
        </button>
        <button class="accion" data-accion="quitar" title="Quitar de esta colección">
          <ion-icon name="ellipsis-horizontal"></ion-icon>
        </button>
      </div>
    </div>`;

  /* duración real del video, leída de sus metadatos */
  const video = card.querySelector('video');
  if (video) {
    video.addEventListener('loadedmetadata', () => {
      card.querySelector('.duracion').textContent = formatearDuracion(video.duration);
    });
  }

  /* si la foto de perfil no existe, se oculta en lugar de mostrarse rota */
  card.querySelector('.usuario-publicacion img')
      .addEventListener('error', e => { e.target.style.visibility = 'hidden'; });

  card.querySelector('[data-accion="quitar"]').addEventListener('click', async () => {
    if (!confirm('¿Quitar esta publicación de la colección?')) return;
    await enviar('quitar', { id_tablero: idTablero, id_pub: p.id_pub });
    verGuardados(idTablero, nombreColeccionActual, emojiColeccionActual);
    cargarColecciones();
  });

  return card;
}

let nombreColeccionActual = '';
let emojiColeccionActual = '';

async function verGuardados(idTablero, nombre = '', emoji = '') {
  nombreColeccionActual = nombre;
  emojiColeccionActual = emoji;
  idColeccionActual = Number(idTablero);

  const r = await fetch(`${API}?accion=guardados&id_tablero=${idTablero}`);
  const json = await r.json();

  const cont     = document.getElementById('lista-publicaciones');
  const titulo   = document.getElementById('titulo-coleccion');
  const subtitulo = document.getElementById('subtitulo-coleccion');
  cont.innerHTML = '';

  if (nombre) titulo.textContent = (emoji ? emoji + ' ' : '') + nombre;

  if (!json.ok) { cont.innerHTML = `<div class="estado-vacio"><p>${esc(json.error)}</p></div>`; return; }

  subtitulo.textContent = json.datos.length === 1
    ? '1 publicación guardada'
    : `${json.datos.length} publicaciones guardadas`;

  if (json.datos.length === 0) {
    cont.innerHTML = `<div class="estado-vacio">
        <ion-icon name="albums-outline"></ion-icon>
        <p>Esta colección está vacía.</p>
      </div>`;
    return;
  }

  json.datos.forEach(p => cont.appendChild(crearTarjeta(p, idTablero)));
}

async function enviar(accion, datos) {
  const body = new URLSearchParams({ accion, ...datos });
  const r = await fetch(API, { method: 'POST', body });
  return r.json();
}

document.getElementById('form-coleccion')?.addEventListener('submit', async e => {
  e.preventDefault();
  const f = e.target;
  const json = await enviar('crear', { nombre: f.nombre.value, descripcion: f.descripcion.value });
  if (json.ok) { f.reset(); cargarColecciones(); }
  else alert(json.error);
});

cargarColecciones();
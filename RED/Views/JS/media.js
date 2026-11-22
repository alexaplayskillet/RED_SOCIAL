// Reglas para subir fotos y videos; las usan crearpub.js y editarpub.js
const MEDIA_MAX_BYTES = 10 * 1024 * 1024;
const MEDIA_TIPOS_FOTO = ["image/jpeg", "image/png", "image/gif", "image/webp"];
const MEDIA_TIPOS_VIDEO = ["video/mp4", "video/webm", "video/ogg"];

// Regresa el mensaje de error, o "" si el archivo se puede subir
function validarArchivoMedia(archivo) {
  const esFoto = MEDIA_TIPOS_FOTO.includes(archivo.type);
  const esVideo = MEDIA_TIPOS_VIDEO.includes(archivo.type);

  if (!esFoto && !esVideo) {
    return "Formato no permitido. Fotos: JPG, PNG, GIF o WEBP. Videos: MP4, WEBM u OGG.";
  }

  if (archivo.size > MEDIA_MAX_BYTES) {
    return "El archivo pesa más de 10 MB.";
  }

  return "";
}

// Muestra una foto o un video dentro de un contenedor de vista previa
function mostrarPreviaMedia(contenedor, src, esVideo) {
  const img = contenedor.querySelector("img");
  const video = contenedor.querySelector("video");

  if (esVideo) {
    img.style.display = "none";
    img.removeAttribute("src");
    video.src = src;
    video.style.display = "block";
  } else {
    video.pause();
    video.removeAttribute("src");
    video.style.display = "none";
    img.src = src;
    img.style.display = "block";
  }

  contenedor.style.display = "block";
}

function ocultarPreviaMedia(contenedor) {
  const img = contenedor.querySelector("img");
  const video = contenedor.querySelector("video");

  video.pause();
  video.removeAttribute("src");
  video.load();
  img.removeAttribute("src");
  contenedor.style.display = "none";
}
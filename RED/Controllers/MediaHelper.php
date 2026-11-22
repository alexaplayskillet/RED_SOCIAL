<?php
// Direccion de la foto o video de una publicacion; se entrega desde MediaController.php
function mediaSrc($publicacion) {
    if (empty($publicacion['tiene_media'])) {
        return '';
    }
    return '../Controllers/MediaController.php?id=' . (int) $publicacion['id_pub'];
}

function esVideo($publicacion) {
    return strpos((string) ($publicacion['tipo_multimedia'] ?? ''), 'video/') === 0;
}
?>
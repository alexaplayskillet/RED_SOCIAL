<?php
function imagenSrc(?string $blob, string $porDefecto = './resources/foto.jpg'): string {
    if (empty($blob)) {
        return $porDefecto;
    }
    // detecta el tipo real (png, jpeg, gif...) en vez de asumir jpeg
    $mime = (new finfo(FILEINFO_MIME_TYPE))->buffer($blob) ?: 'image/jpeg';
    return 'data:' . $mime . ';base64,' . base64_encode($blob);
}
?>
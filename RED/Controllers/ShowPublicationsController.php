<?php
    require_once '../model/conexion.php';
    require_once '../model/PublicacionModelo.php';
    require_once 'ImageController.php';
    require_once 'MediaHelper.php';
    require_once 'TiempoHelper.php';

    /*creo el objeto del modelo con conexion a la bd */
    $modelo = new PublicacionModelo($conexion);

    //filtro elegido en el boton de Filtros del index (index.php?filtro=...)
    $filtroActual = $_GET['filtro'] ?? '';
    if (!in_array($filtroActual, ['amigos', 'recientes', 'likes'], true)) {
        $filtroActual = '';
    }
    //el filtro de amigos solo tiene sentido con sesion iniciada
    if ($filtroActual === 'amigos' && !isset($_SESSION['id_usuario'])) {
        $filtroActual = '';
    }

    //sin filtro las publicaciones salen en orden aleatorio
    if ($filtroActual === '') {
        $publicaciones = $modelo->CargarPublicacionesAleatorias()->fetch_all(MYSQLI_ASSOC);
    } else {
        $publicaciones = $modelo->CargarPublicacionesFiltradas($filtroActual, (int) ($_SESSION['id_usuario'] ?? 0))->fetch_all(MYSQLI_ASSOC);
    }

    //publicaciones a las que el usuario ya les dio like (sin sesion no hay ninguna)
    $idsConLike = isset($_SESSION['id_usuario']) ? $modelo->IdsConLike((int) $_SESSION['id_usuario']) : [];

    foreach ($publicaciones as &$pub) {
        $pub['foto_src'] = imagenSrc($pub['fotografia']);
        $pub['media_src'] = mediaSrc($pub);
        $pub['es_video'] = esVideo($pub);
        $pub['dio_like'] = in_array((int) $pub['id_pub'], $idsConLike, true);
        $pub['hace'] = tiempoRelativo($pub['segundos'] ?? null, $pub['fecha_pub']);
        unset($pub['fotografia']);
    }
    unset($pub);
?>
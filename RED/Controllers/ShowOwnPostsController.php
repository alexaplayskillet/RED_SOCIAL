<?php
    require_once '../model/conexion.php';
    require_once '../model/PublicacionModelo.php';
    require_once 'ImageController.php';
    require_once 'MediaHelper.php';
    require_once 'TiempoHelper.php';

    /*creo el objeto del modelo con conexion a la bd */
    $modelo = new PublicacionModelo($conexion);
    $id = $_SESSION['id_usuario'];

    //cargo las publicaciones del usuario
    $publicaciones = $modelo->CargarPublicacionesPropias($id)->fetch_all(MYSQLI_ASSOC);

    //publicaciones a las que el usuario ya les dio like
    $idsConLike = $modelo->IdsConLike((int) $_SESSION['id_usuario']);

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
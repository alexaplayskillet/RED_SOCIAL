<?php
    require_once '../model/conexion.php';
    require_once '../model/PublicacionModelo.php';
    require_once 'ImageController.php';
    require_once 'MediaHelper.php';
    require_once 'TiempoHelper.php';

    /*creo el objeto del modelo con conexion a la bd */
    $modelo = new PublicacionModelo($conexion);

    //cargo la publicacion
    $id = (int)($_GET['id'] ?? 0);
    $publicaciones = $modelo->CargarPublicacion($id);

    if ($publicaciones === null) {
        header("Location: index.php");
        exit();
    }

    //foto del usuario y direccion de la foto o video de la publicacion
    $publicaciones['foto_src']  = imagenSrc($publicaciones['fotografia']);
    $publicaciones['media_src'] = mediaSrc($publicaciones);
    $publicaciones['es_video']  = esVideo($publicaciones);
    unset($publicaciones['fotografia']);

    $publicaciones['dio_like'] = isset($_SESSION['id_usuario'])
        && in_array($id, $modelo->IdsConLike((int) $_SESSION['id_usuario']), true);
    $publicaciones['hace'] = tiempoRelativo($publicaciones['segundos'] ?? null, $publicaciones['fecha_pub']);

    //los comentarios se cargan desde JS/interaccion.js con PublicacionController.php
?>
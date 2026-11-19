<?php
    require_once '../model/conexion.php';
    require_once '../model/PublicacionModelo.php';
    require_once '../model/ComentarioModelo.php';
    require_once 'ImageController.php';

    /*creo el objeto del modelo con conexion a la bd */
    $modelo = new PublicacionModelo($conexion);

    //cargo las publicaciones
    $id = (int)($_GET['id'] ?? 0);
    $publicaciones = $modelo->CargarPublicacion($id);

    

    if ($publicaciones === null) {
        header("Location: index.php");
        exit();
    }

    //llama a la funcion de las imagenes para que las guarde en las publicaciones
    $publicaciones['foto_src']  = imagenSrc($publicaciones['fotografia']);
    $publicaciones['media_src'] = imagenSrc($publicaciones['multimedia'], '');
    unset($publicaciones['fotografia'], $publicaciones['multimedia']);
    
    //carga los comentarios
    $modeloCom = new ComentarioModelo($conexion);
    $comentarios = $modeloCom->CargarComentarios($id);

    foreach ($comentarios as &$com) {
        $com['foto_src'] = imagenSrc($com['fotografia']);
        unset($com['fotografia']);
    }
    unset($com);
    //return $publicaciones;
?>
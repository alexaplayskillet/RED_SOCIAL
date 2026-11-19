<?php
    require_once '../model/conexion.php';
    require_once '../model/PublicacionModelo.php';
    require_once 'ImageController.php';

    /*creo el objeto del modelo con conexion a la bd */
    $modelo = new PublicacionModelo($conexion);
    $id = $_SESSION['id_usuario'];
    //cargo las publicaciones

    $publicaciones = $modelo->CargarPublicacionesPropias($id)-> fetch_all(MYSQLI_ASSOC);


    foreach ($publicaciones as &$pub) {
        $pub['foto_src'] = imagenSrc($pub['fotografia']);
        $pub['media_src'] = imagenSrc($pub['multimedia'], '');
        unset($pub['fotografia'], $pub['multimedia']); 
    }
    unset($pub);
    //return $publicaciones;
?>
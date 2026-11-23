<?php

    session_start();
    require_once '../model/conexion.php';
    require_once '../model/EstadisticasModelo.php';

    $modelo = new EstadisticasModelo($conexion);
    $id = $_SESSION['id_usuario'];

    if(empty($id) || !isset($id)){
        header("../Views/login.php");
    }

    $likes = $modelo->obtenerLikes($id);
?>
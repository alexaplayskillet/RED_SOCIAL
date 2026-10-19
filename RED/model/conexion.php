<?php
    /*CREO LA VARIABLE QUE GUARDA EL SERVIDOR, USUARIO, CONTRASEÑA, BASE DE DATOS Y PUERTO, SI NO PONGO EL PUERTO ES EL DEFECTO*/
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conexion = mysqli_connect("localhost", "root", "", "cosmix");
    if (!$conexion){//si no se logra hacer la conexion
        die("No se pudo conectar".mysqli_connect_error());
    } 
    mysqli_set_charset($conexion, "utf8mb4");
?>
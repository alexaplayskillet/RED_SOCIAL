<?php
    /*CREO LA VARIABLE QUE GUARDA EL SERVIDOR, USUARIO, CONTRASEÑA, BASE DE DATOS Y PUERTO, SI NO PONGO EL PUERTO ES EL DEFECTO*/
    $conexion = new mysqli("127.0.0.1", "root", "vega1705", "cosmix");
    if ($conexion->connect_error) {
        die("Error de conexión: " . $mysqli->connect_error);
    }

?>

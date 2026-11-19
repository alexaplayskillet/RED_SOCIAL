<?php
class UsuarioModelo {
    private mysqli $cone;

    public function __construct(mysqli $cone) {
        $this->cone = $cone;
    }

    public function obtenerUsuarioTag($tag) {
    
        $consulta = $this->cone->prepare("CALL login_usuario(?, @id, @hash)");//llama mi procedimiento
        $consulta->bind_param("s", $tag);//adjuntamos el parametro de entrada, representado con el ?
        $consulta->execute();
        $consulta->close();

    
        $resultado = $this->cone->query("SELECT @id AS id, @hash AS hash");
        $fila = $resultado->fetch_assoc(); //representa los datos de la consulta, siendo id y hash
        $resultado->free();

        return $fila['hash'] !== null ? $fila : null; //si la contraseña es nula no regresa nada, si hay info regresa todo el registro
    }

    public function registrarUsuario($nombre, $tag, $fechaNac, $correo, $contras) {
        $consulta = $this->cone->prepare("CALL registrar_usuario(?, ?, ?, ?, ?, @id, @codigo)");
        $consulta->bind_param("sssss", $nombre, $tag, $fechaNac, $correo, $contras);
        $consulta->execute();
        $consulta->close();

        $resultado = $this->cone->query("SELECT @id AS id, @codigo AS codigo");
        $fila = $resultado->fetch_assoc();
        $resultado->free();

        return $fila;
    }
}
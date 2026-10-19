<?php
class UsuarioModelo {
    private $cone;

    public function __construct($cone) {
        $this->cone = $cone;
    }

    // Devuelve ['id' => ..., 'hash' => ...] o null si no existe / está inhabilitado
    public function obtenerUsuarioTag($tag) {
        $consulta = mysqli_prepare($this->cone, "CALL login_usuario(?, @id, @hash)");
        mysqli_stmt_bind_param($consulta, "s", $tag);
        mysqli_stmt_execute($consulta);
        mysqli_stmt_close($consulta);

        /*saco los datos del procedimiento y los guardo en un arreglo donde sus claves son id y hash respectivamente
        
        es decir
        fila {
            "id" = id que trae el procedimiento;
            "hash" = contraseña que trae el procedimiento
        }*/
        $resultado = mysqli_query($this->cone, "SELECT @id AS id, @hash AS hash");
        $fila = mysqli_fetch_assoc($resultado);

        return $fila['hash'] !== null ? $fila : null;
    }

    public function registrarUsuario($nombre, $tag, $fechaNac, $correo, $contras){
        $consulta = mysqli_prepare($this->cone, "CALL registrar_usuario(?, ?, ?, ?, ?, @id, @codigo)");

        mysqli_stmt_bind_param($consulta, "sssss", $nombre, $tag, $fechaNac, $correo, $contras);
        mysqli_stmt_execute($consulta);
        mysqli_stmt_close($consulta);

        $resultado = mysqli_query($this->cone, "SELECT @id AS id, @codigo AS codigo");
        return mysqli_fetch_assoc($resultado);
    }
}
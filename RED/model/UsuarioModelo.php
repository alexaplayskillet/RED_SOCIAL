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

    public function obtenerPerfil($idUsuario) {
        $accion = "OBTENER";
        $consulta = mysqli_prepare($this->cone, "CALL gestionar_perfil(?, ?, NULL, NULL, NULL, NULL)");
        mysqli_stmt_bind_param($consulta, "si", $accion, $idUsuario);
        mysqli_stmt_execute($consulta);

        $resultado = mysqli_stmt_get_result($consulta);
        $fila = mysqli_fetch_assoc($resultado);

        while (mysqli_stmt_more_results($consulta)) {
            mysqli_stmt_next_result($consulta);
        }
        mysqli_stmt_close($consulta);

        if (!$fila || (int) $fila['codigo'] !== 0) {
            return null;
        }
        return $fila;
    }

    public function editarPerfil($idUsuario, $nombre, $tag, $correo, $fechaNac) {
        $accion = "EDITAR";
        $consulta = mysqli_prepare($this->cone, "CALL gestionar_perfil(?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($consulta, "sissss", $accion, $idUsuario, $nombre, $tag, $correo, $fechaNac);
        mysqli_stmt_execute($consulta);

        $resultado = mysqli_stmt_get_result($consulta);
        $fila = mysqli_fetch_assoc($resultado);

        while (mysqli_stmt_more_results($consulta)) {
            mysqli_stmt_next_result($consulta);
        }
        mysqli_stmt_close($consulta);

        return $fila;
    }

    private function llamarAmigos($accion, $idUsuario, $idAmigo, $busqueda, $limite) {
        $consulta = mysqli_prepare($this->cone, "CALL gestionar_amigos(?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($consulta, "siisi", $accion, $idUsuario, $idAmigo, $busqueda, $limite);
        mysqli_stmt_execute($consulta);

        $resultado = mysqli_stmt_get_result($consulta);
        $filas = [];
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $filas[] = $fila;
        }

        while (mysqli_stmt_more_results($consulta)) {
            mysqli_stmt_next_result($consulta);
        }
        mysqli_stmt_close($consulta);

        return $filas;
    }

    public function listarAmigos($idUsuario, $busqueda = null, $limite = null) {
        return $this->llamarAmigos("LISTAR", $idUsuario, null, $busqueda, $limite);
    }

    public function contarAmigos($idUsuario) {
        $filas = $this->llamarAmigos("CONTAR", $idUsuario, null, null, null);
        return $filas ? (int) $filas[0]["total"] : 0;
    }

    public function eliminarAmigo($idUsuario, $idAmigo) {
        $filas = $this->llamarAmigos("ELIMINAR", $idUsuario, $idAmigo, null, null);
        return $filas ? (int) $filas[0]["codigo"] : 2;
    }

    private function llamarSolicitudes($accion, $idUsuario, $idOtro, $limite) {
        $consulta = mysqli_prepare($this->cone, "CALL gestionar_solicitudes(?, ?, ?, ?)");
        mysqli_stmt_bind_param($consulta, "siii", $accion, $idUsuario, $idOtro, $limite);
        mysqli_stmt_execute($consulta);

        $resultado = mysqli_stmt_get_result($consulta);
        $filas = [];
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $filas[] = $fila;
        }

        while (mysqli_stmt_more_results($consulta)) {
            mysqli_stmt_next_result($consulta);
        }
        mysqli_stmt_close($consulta);

        return $filas;
    }

    public function obtenerSugerencias($idUsuario, $limite = null) {
        return $this->llamarSolicitudes("SUGERIR", $idUsuario, null, $limite);
    }

    public function obtenerSolicitudesRecibidas($idUsuario) {
        return $this->llamarSolicitudes("RECIBIDAS", $idUsuario, null, null);
    }

    public function responderSolicitud($accion, $idUsuario, $idOtro) {
        $filas = $this->llamarSolicitudes($accion, $idUsuario, $idOtro, null);
        return $filas ? (int) $filas[0]["codigo"] : 7;
    }

    private function llamarGaleria($accion, $idUsuario, $idPub, $texto, $limite) {
        $consulta = mysqli_prepare($this->cone, "CALL gestionar_galeria(?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($consulta, "siisi", $accion, $idUsuario, $idPub, $texto, $limite);
        mysqli_stmt_execute($consulta);

        $resultado = mysqli_stmt_get_result($consulta);
        $filas = [];
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $filas[] = $fila;
        }

        while (mysqli_stmt_more_results($consulta)) {
            mysqli_stmt_next_result($consulta);
        }
        mysqli_stmt_close($consulta);

        return $filas;
    }

    public function listarGaleria($idUsuario, $limite = null) {
        return $this->llamarGaleria("LISTAR", $idUsuario, null, null, $limite);
    }

    public function obtenerImagenGaleria($idUsuario, $idPub) {
        $filas = $this->llamarGaleria("OBTENER", $idUsuario, $idPub, null, null);
        if (!$filas || (int) $filas[0]["codigo"] !== 0 || $filas[0]["imagen"] === null) {
            return null;
        }
        return $filas[0]["imagen"];
    }

    public function editarFoto($idUsuario, $idPub, $texto) {
        $filas = $this->llamarGaleria("EDITAR", $idUsuario, $idPub, $texto, null);
        return $filas ? (int) $filas[0]["codigo"] : 7;
    }

    public function eliminarFoto($idUsuario, $idPub) {
        $filas = $this->llamarGaleria("ELIMINAR", $idUsuario, $idPub, null, null);
        return $filas ? (int) $filas[0]["codigo"] : 7;
    }
}
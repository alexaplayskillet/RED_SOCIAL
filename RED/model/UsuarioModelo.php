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
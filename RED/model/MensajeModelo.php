<?php
class MensajeModelo {
    private mysqli $cone;

    public function __construct(mysqli $cone) {
        $this->cone = $cone;
    }

    // Llama a gestionar_mensajes y regresa todas las filas del resultado
    private function llamar($accion, $idUsuario, $idOtro, $texto, $idUltimo) {
        $consulta = mysqli_prepare($this->cone, "CALL gestionar_mensajes(?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($consulta, "siisi", $accion, $idUsuario, $idOtro, $texto, $idUltimo);
        mysqli_stmt_execute($consulta);

        $resultado = mysqli_stmt_get_result($consulta);
        $filas = [];
        if ($resultado) {
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $filas[] = $fila;
            }
        }

        while (mysqli_stmt_more_results($consulta)) {
            mysqli_stmt_next_result($consulta);
        }
        mysqli_stmt_close($consulta);

        return $filas;
    }

    // Chats del usuario, del mas reciente al mas antiguo
    public function listarChats($idUsuario) {
        return $this->llamar("CHATS", $idUsuario, null, null, null);
    }

    // Abre (o crea) el chat con otro usuario. Regresa la fila con "codigo"
    public function abrirChat($idUsuario, $idOtro) {
        $filas = $this->llamar("ABRIR", $idUsuario, $idOtro, null, null);
        return $filas ? $filas[0] : ["codigo" => 7];
    }

    // Mensajes de la conversacion. Con $idUltimo solo los mas nuevos
    public function listarMensajes($idUsuario, $idOtro, $idUltimo = null) {
        $filas = $this->llamar("MENSAJES", $idUsuario, $idOtro, null, $idUltimo);
        // Si fue un error el procedimiento regresa una sola fila con codigo distinto de 0
        if ($filas && !isset($filas[0]["id_mensaje"])) {
            return (int) $filas[0]["codigo"];
        }
        return $filas;
    }

    // Guarda un mensaje. Regresa la fila con "codigo" (y id_mensaje si salio bien)
    public function enviarMensaje($idUsuario, $idOtro, $texto) {
        $filas = $this->llamar("ENVIAR", $idUsuario, $idOtro, $texto, null);
        return $filas ? $filas[0] : ["codigo" => 7];
    }
}

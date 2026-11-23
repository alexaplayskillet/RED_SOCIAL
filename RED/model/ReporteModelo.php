<?php
class ReporteModelo {
    private mysqli $cone;

    public function __construct(mysqli $cone) {
        $this->cone = $cone;
    }

    private function llamar($accion, $idUsuario) {
        $consulta = mysqli_prepare($this->cone, "CALL gestionar_reportes(?, ?)");
        mysqli_stmt_bind_param($consulta, "si", $accion, $idUsuario);
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

    // Solicitudes de amistad aceptadas durante el mes actual
    public function amigosDelMes($idUsuario) {
        return $this->llamar("AMIGOS", $idUsuario);
    }

    // Publicaciones creadas durante el mes actual
    public function publicacionesDelMes($idUsuario) {
        return $this->llamar("PUBS", $idUsuario);
    }
}

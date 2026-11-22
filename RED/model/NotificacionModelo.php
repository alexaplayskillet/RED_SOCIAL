<?php
class NotificacionModelo {
    private mysqli $cone;

    public function __construct(mysqli $cone) {
        $this->cone = $cone;
    }

    // Llama al procedimiento gestionar_notificaciones y regresa todas las filas
    private function LlamarProcedimiento($accion, $idUsuario, $idNotif = null, $limite = null) {
        $consulta = $this->cone->prepare("CALL gestionar_notificaciones(?, ?, ?, ?)");
        $consulta->bind_param("siii", $accion, $idUsuario, $idNotif, $limite);
        $consulta->execute();

        $resultado = $consulta->get_result();
        $filas = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];

        while ($consulta->more_results()) {
            $consulta->next_result();
        }
        $consulta->close();

        return $filas;
    }

    // Ultimas notificaciones del usuario, de la mas nueva a la mas vieja
    public function Listar(int $idUsuario, int $limite = 30): array {
        $filas = $this->LlamarProcedimiento("LISTAR", $idUsuario, null, $limite);

        if (!empty($filas) && (int) $filas[0]["codigo"] !== 0) {
            return [];
        }
        return $filas;
    }

    public function ContarNoLeidas(int $idUsuario): int {
        $filas = $this->LlamarProcedimiento("CONTAR", $idUsuario);
        return (int) ($filas[0]["total"] ?? 0);
    }

    public function MarcarLeida(int $idUsuario, int $idNotif): void {
        $this->LlamarProcedimiento("LEER", $idUsuario, $idNotif);
    }

    public function MarcarTodasLeidas(int $idUsuario): void {
        $this->LlamarProcedimiento("LEER_TODAS", $idUsuario);
    }
}
?>
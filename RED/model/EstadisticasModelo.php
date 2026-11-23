<?php
class EstadisticasModelo {
    private mysqli $cone;

    public function __construct(mysqli $cone) {
        $this->cone = $cone;
    }

    public function obtenerLikes(int $usuario): int {
        $consulta = $this->cone->prepare("SELECT Est_Likes(?) AS total");
        $consulta->bind_param("i", $usuario);
        $consulta->execute();

        $consulta->bind_result($total);
        $consulta->fetch();
        $consulta->close();

        return (int) $total;
    }

    public function obtenerComentarios(int $usuario): int {
        $consulta = $this->cone->prepare("SELECT Est_Comms(?) AS total");
        $consulta->bind_param("i", $usuario);
        $consulta->execute();

        $consulta->bind_result($total);
        $consulta->fetch();
        $consulta->close();

        return (int) $total;
    }

    public function obtenerPublis(int $usuario): int {
        $consulta = $this->cone->prepare("SELECT Est_Pubs(?) AS total");
        $consulta->bind_param("i", $usuario);
        $consulta->execute();

        $consulta->bind_result($total);
        $consulta->fetch();
        $consulta->close();

        return (int) $total;
    }

     public function obtenerAmigos(int $usuario): int {
        $consulta = $this->cone->prepare("SELECT Est_Amigos(?) AS total");
        $consulta->bind_param("i", $usuario);
        $consulta->execute();

        $consulta->bind_result($total);
        $consulta->fetch();
        $consulta->close();

        return (int) $total;
    }
}

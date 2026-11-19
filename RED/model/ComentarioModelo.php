<?php
class ComentarioModelo {
    private mysqli $cone;

    public function __construct(mysqli $cone) {
        $this->cone = $cone;
    }

    public function CargarComentarios(int $idPub): array {
        $consulta = $this->cone->prepare(
            "SELECT * FROM v_cargaComentarios WHERE id_pubC = ? ORDER BY fechaC ASC"
        );
        $consulta->bind_param("i", $idPub);
        $consulta->execute();
        $comentarios = $consulta->get_result()->fetch_all(MYSQLI_ASSOC);
        $consulta->close();

        return $comentarios;
    }
}
?>
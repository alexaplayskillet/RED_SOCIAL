<?php
class ComentarioModelo {
    private mysqli $cone;

    public function __construct(mysqli $cone) {
        $this->cone = $cone;
    }

    // Llama al procedimiento gestionar_publicaciones para las acciones de comentarios
    private function LlamarProcedimiento($accion, $idUsuario, $idPub, $idCom = null, $texto = null) {
        $consulta = $this->cone->prepare("CALL gestionar_publicaciones(?, ?, ?, ?, ?, NULL, NULL)");
        $consulta->bind_param("siiis", $accion, $idUsuario, $idPub, $idCom, $texto);
        $consulta->execute();

        $resultado = $consulta->get_result();
        $filas = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];

        while ($consulta->more_results()) {
            $consulta->next_result();
        }
        $consulta->close();

        return $filas;
    }

    // Regresa los comentarios y respuestas de una publicacion, del mas viejo al mas nuevo
    public function CargarComentarios(int $idPub, int $idUsuario): array {
        $filas = $this->LlamarProcedimiento("LISTAR_COM", $idUsuario, $idPub);

        if (!empty($filas) && (int) $filas[0]["codigo"] !== 0) {
            return [];
        }

        return $filas;
    }

    // $idPadre es el comentario al que se responde; null si es un comentario normal
    public function Comentar(int $idUsuario, int $idPub, string $texto, ?int $idPadre = null) {
        $filas = $this->LlamarProcedimiento("COMENTAR", $idUsuario, $idPub, $idPadre, $texto);
        return $filas[0] ?? null;
    }

    public function AlternarLikeComentario(int $idUsuario, int $idCom) {
        $filas = $this->LlamarProcedimiento("LIKE_COM", $idUsuario, null, $idCom);
        return $filas[0] ?? null;
    }
}
?>
<?php
class ColeccionModelo {
    private mysqli $cone;

    public function __construct(mysqli $cone) {
        $this->cone = $cone;
    }

    public function listarColecciones(int $usuario): array {
        $sql = "SELECT t.id_tablero, t.nombre, t.descripcion, t.fecha_creacion,
                       COUNT(g.id_guar) AS total
                FROM tableros t
                LEFT JOIN guardados g
                       ON g.id_tableroG = t.id_tablero AND g.estado = 1
                WHERE t.id_usuarioT = ? AND t.estado = 1
                GROUP BY t.id_tablero
                ORDER BY t.fecha_creacion DESC";
        $st = $this->cone->prepare($sql);
        $st->bind_param("i", $usuario);
        $st->execute();
        $filas = $st->get_result()->fetch_all(MYSQLI_ASSOC);
        $st->close();
        return $filas;
    }

    public function crearColeccion(int $usuario, string $nombre, string $descripcion): int {
        $st = $this->cone->prepare("CALL crear_coleccion(?, ?, ?, @id_tablero)");
        $st->bind_param("iss", $usuario, $nombre, $descripcion);
        $st->execute();
        $st->close();

        $res = $this->cone->query("SELECT @id_tablero AS id");
        return (int) $res->fetch_assoc()['id'];
    }

    public function guardarPublicacion(int $usuario, int $tablero, int $pub): void {
        $st = $this->cone->prepare("CALL guardar_publicacion(?, ?, ?)");
        $st->bind_param("iii", $usuario, $tablero, $pub);
        $st->execute();
        $st->close();
    }

    public function quitarGuardado(int $usuario, int $tablero, int $pub): void {
        $st = $this->cone->prepare("CALL quitar_guardado(?, ?, ?)");
        $st->bind_param("iii", $usuario, $tablero, $pub);
        $st->execute();
        $st->close();
    }

    public function listarGuardados(int $usuario, int $tablero): array {
        /* ya no mandamos el BLOB en base64: la imagen/video se pide aparte a
           MediaController.php?id=ID_PUB y la foto del autor a ImagenController.php */
        $sql = "SELECT p.id_pub, p.id_usuarioP, p.texto, p.fecha_pub,
                       p.tipo_multimedia, (p.multimedia IS NOT NULL) AS tiene_media,
                       u.tag
                FROM guardados g
                INNER JOIN tableros t ON t.id_tablero = g.id_tableroG
                INNER JOIN publicaciones p ON p.id_pub = g.id_pubG
                INNER JOIN usuario u ON u.id_usuario = p.id_usuarioP
                WHERE g.id_tableroG = ? AND t.id_usuarioT = ?
                  AND g.estado = 1 AND t.estado = 1 AND u.estado = 1
                ORDER BY g.fechaG DESC";
        $st = $this->cone->prepare($sql);
        $st->bind_param("ii", $tablero, $usuario);
        $st->execute();
        $filas = $st->get_result()->fetch_all(MYSQLI_ASSOC);
        $st->close();
        return $filas;
    }
}
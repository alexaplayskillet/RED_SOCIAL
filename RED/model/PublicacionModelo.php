<?php
    class PublicacionModelo{
        private mysqli $cone;

        public function __construct(mysqli $cone){
            $this->cone = $cone;
        }

        public function CargarPublicaciones(){
            $consulta = $this->cone->prepare("SELECT * FROM v_cargaPublicaciones ORDER BY fecha_pub DESC");
            $consulta->execute();
            $resultado = $consulta->get_result();

            $consulta->close();

            return $resultado;
        }

        // Todas las publicaciones de los usuarios activos, en orden aleatorio (para el index)
        public function CargarPublicacionesAleatorias(){
            $consulta = $this->cone->prepare("SELECT * FROM v_cargaPublicaciones ORDER BY RAND()");
            $consulta->execute();
            $resultado = $consulta->get_result();

            $consulta->close();

            return $resultado;
        }

        // Publicaciones para el filtro del index:
        //   amigos:    solo de los amigos del usuario, de la mas nueva a la mas vieja
        //   recientes: todas, de la mas nueva a la mas vieja
        //   likes:     todas, de la que tiene mas likes a la que tiene menos
        public function CargarPublicacionesFiltradas($filtro, $idUsuario){
            if ($filtro === "amigos") {
                $consulta = $this->cone->prepare(
                    "SELECT v.* FROM v_cargaPublicaciones v
                     WHERE v.id_usuario IN (
                         SELECT IF(a.id_usuario1 = ?, a.id_usuario2, a.id_usuario1)
                         FROM amistad a
                         WHERE a.estado = 1 AND (a.id_usuario1 = ? OR a.id_usuario2 = ?)
                     )
                     ORDER BY v.fecha_pub DESC"
                );
                $consulta->bind_param("iii", $idUsuario, $idUsuario, $idUsuario);
            } elseif ($filtro === "likes") {
                $consulta = $this->cone->prepare("SELECT * FROM v_cargaPublicaciones ORDER BY likes DESC, fecha_pub DESC");
            } else {
                $consulta = $this->cone->prepare("SELECT * FROM v_cargaPublicaciones ORDER BY fecha_pub DESC");
            }

            $consulta->execute();
            $resultado = $consulta->get_result();

            $consulta->close();

            return $resultado;
        }

        public function CargarPublicacion($id){
            $consulta = $this->cone->prepare("SELECT * FROM v_cargaPublicaciones WHERE id_pub = ?");
            $consulta->bind_param("i", $id);
            $consulta->execute();
            $resultado = $consulta->get_result()->fetch_assoc();

            $consulta->close();

            return $resultado;
        }

        public function CargarPublicacionesPropias($id){
            $consulta = $this->cone->prepare("SELECT * FROM v_cargaPublicaciones WHERE id_usuario = ? ORDER BY fecha_pub DESC");
            $consulta->bind_param("i", $id);
            $consulta->execute();
            $resultado = $consulta->get_result();

            $consulta->close();

            return $resultado;
        }

        // Llama al procedimiento gestionar_publicaciones y regresa todas las filas del resultado
        private function LlamarProcedimiento($accion, $idUsuario, $idPub = null, $texto = null, $multimedia = null, $tipo = null){
            $consulta = $this->cone->prepare("CALL gestionar_publicaciones(?, ?, ?, NULL, ?, ?, ?)");
            $nulo = null;
            $consulta->bind_param("siisbs", $accion, $idUsuario, $idPub, $texto, $nulo, $tipo);

            if ($multimedia !== null) {
                $consulta->send_long_data(4, $multimedia);
            }

            $consulta->execute();
            $resultado = $consulta->get_result();
            $filas = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];

            while ($consulta->more_results()) {
                $consulta->next_result();
            }
            $consulta->close();

            return $filas;
        }

        public function CrearPublicacion($idUsuario, $texto, $multimedia, $tipo){
            $filas = $this->LlamarProcedimiento("CREAR", $idUsuario, null, $texto, $multimedia, $tipo);
            return $filas[0] ?? null;
        }

        // $multimedia y $tipo traen el archivo nuevo; si no hay archivo nuevo,
        // $tipo = "QUITAR" borra el actual y null lo deja como esta
        public function EditarPublicacion($idUsuario, $idPub, $texto, $multimedia, $tipo){
            $filas = $this->LlamarProcedimiento("EDITAR", $idUsuario, $idPub, $texto, $multimedia, $tipo);
            return $filas[0] ?? null;
        }

        public function EliminarPublicacion($idUsuario, $idPub){
            $filas = $this->LlamarProcedimiento("ELIMINAR", $idUsuario, $idPub);
            return $filas[0] ?? null;
        }

        // Regresa el archivo (foto o video) de una publicacion y su tipo, o null si no tiene
        public function ObtenerMedia($idUsuario, $idPub){
            $filas = $this->LlamarProcedimiento("MEDIA", $idUsuario, $idPub);
            if (empty($filas) || (int) $filas[0]["codigo"] !== 0) {
                return null;
            }
            return $filas[0];
        }

        public function AlternarLike($idUsuario, $idPub){
            $filas = $this->LlamarProcedimiento("LIKE", $idUsuario, $idPub);
            return $filas[0] ?? null;
        }

        // Regresa los id de las publicaciones a las que el usuario les dio like
        public function IdsConLike($idUsuario){
            $filas = $this->LlamarProcedimiento("LIKES_USR", $idUsuario);
            return array_map("intval", array_column($filas, "id_pub"));
        }
    }
?>
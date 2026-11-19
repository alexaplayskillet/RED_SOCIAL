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
    
        public function CargarPublicacion($id){
            $consulta = $this->cone->prepare("SELECT * FROM v_cargaPublicaciones WHERE id_pub = ?");
            $consulta -> bind_param("i", $id);
            $consulta->execute();
            $resultado = $consulta->get_result()->fetch_assoc();

            $consulta->close();

            return $resultado;
        }

          public function CargarPublicacionesPropias($id){
            $consulta = $this->cone->prepare("SELECT * FROM v_cargaPublicaciones WHERE id_usuario = ? ORDER BY fecha_pub DESC");
            $consulta -> bind_param("i", $id);
            $consulta->execute();
            $resultado = $consulta->get_result();

            $consulta->close();

            return $resultado;
        }

    }
?>
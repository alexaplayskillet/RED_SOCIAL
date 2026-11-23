-- Procedimiento de reportes de estadisticas del perfil.
-- Se puede ejecutar mas de una vez: reemplaza el procedimiento existente.
--
-- Acciones (p_accion), todas del mes actual:
--   AMIGOS  solicitudes de amistad aceptadas (las que tu enviaste y las que
--           aceptaste), con la fecha en que se aceptaron.
--   PUBS    publicaciones creadas, con sus likes, comentarios y guardados.
--
-- Nota: la tabla amistad no guarda fecha, por eso la fecha de cada amigo
-- nuevo sale de solicitudes.fecha_respuesta.
--
-- Codigo 2 = accion no valida.

USE cosmix;

DROP PROCEDURE IF EXISTS gestionar_reportes;

DELIMITER $$

CREATE PROCEDURE gestionar_reportes (
    IN p_accion     VARCHAR(10),
    IN p_id_usuario INT
)
BEGIN
    DECLARE v_inicio DATE;
    DECLARE v_fin    DATE;

    SET v_inicio = DATE_FORMAT(CURDATE(), '%Y-%m-01');
    SET v_fin    = v_inicio + INTERVAL 1 MONTH;

    IF p_accion = 'AMIGOS' THEN
        SELECT 0 AS codigo,
               s.fecha_respuesta AS fecha,
               u.nombre,
               u.tag,
               IF(s.usuario_envia = p_id_usuario, 1, 0) AS envie
        FROM solicitudes s
        JOIN usuario u
          ON u.id_usuario = IF(s.usuario_envia = p_id_usuario, s.usuario_recibe, s.usuario_envia)
        WHERE s.estado = 2
          AND (s.usuario_envia = p_id_usuario OR s.usuario_recibe = p_id_usuario)
          AND s.fecha_respuesta >= v_inicio
          AND s.fecha_respuesta <  v_fin
        ORDER BY s.fecha_respuesta DESC, s.id_soli DESC;

    ELSEIF p_accion = 'PUBS' THEN
        SELECT 0 AS codigo,
               p.fecha_pub AS fecha,
               p.texto,
               CASE
                   WHEN p.multimedia IS NULL THEN 'Texto'
                   WHEN p.tipo_multimedia LIKE 'video/%' THEN 'Video'
                   ELSE 'Foto'
               END AS tipo,
               (SELECT COUNT(*) FROM likes l       WHERE l.id_pubL = p.id_pub AND l.estado = 1) AS likes,
               (SELECT COUNT(*) FROM comentarios c WHERE c.id_pubC = p.id_pub AND c.estado = 1) AS comentarios,
               (SELECT COUNT(*) FROM guardados g   WHERE g.id_pubG = p.id_pub AND g.estado = 1) AS guardados
        FROM publicaciones p
        WHERE p.id_usuarioP = p_id_usuario
          AND p.fecha_pub >= v_inicio
          AND p.fecha_pub <  v_fin
        ORDER BY p.fecha_pub DESC, p.id_pub DESC;

    ELSE
        SELECT 2 AS codigo;
    END IF;
END$$

DELIMITER ;

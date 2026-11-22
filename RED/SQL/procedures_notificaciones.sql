USE cosmix;

CREATE TABLE IF NOT EXISTS notificaciones (
    id_notif   INT PRIMARY KEY AUTO_INCREMENT,
    id_destino INT NOT NULL,
    id_origen  INT NOT NULL,
    tipo       VARCHAR(20) NOT NULL,
    id_pub     INT NULL,
    id_come    INT NULL,
    id_soli    INT NULL,
    leida      TINYINT NOT NULL DEFAULT 0,
    fecha      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_notif_destino
        FOREIGN KEY (id_destino) REFERENCES usuario(id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_notif_origen
        FOREIGN KEY (id_origen) REFERENCES usuario(id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_notif_pub
        FOREIGN KEY (id_pub) REFERENCES publicaciones(id_pub)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_notif_come
        FOREIGN KEY (id_come) REFERENCES comentarios(id_come)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_notif_soli
        FOREIGN KEY (id_soli) REFERENCES solicitudes(id_soli)
        ON DELETE CASCADE ON UPDATE CASCADE,

    INDEX idx_notif_destino (id_destino, leida, fecha)
);

DROP TRIGGER IF EXISTS tr_notif_like_ins;
DROP TRIGGER IF EXISTS tr_notif_like_upd;
DROP TRIGGER IF EXISTS tr_notif_comentario_ins;
DROP TRIGGER IF EXISTS tr_notif_likecom_ins;
DROP TRIGGER IF EXISTS tr_notif_likecom_upd;
DROP TRIGGER IF EXISTS tr_notif_solicitud_ins;
DROP TRIGGER IF EXISTS tr_notif_solicitud_upd;
DROP PROCEDURE IF EXISTS gestionar_notificaciones;

DELIMITER $$


CREATE TRIGGER tr_notif_like_ins AFTER INSERT ON likes
FOR EACH ROW
BEGIN
    IF NEW.estado = 1 THEN
        DELETE FROM notificaciones
        WHERE tipo = 'like_pub' AND id_origen = NEW.id_usuarioL AND id_pub = NEW.id_pubL;

        INSERT INTO notificaciones (id_destino, id_origen, tipo, id_pub)
        SELECT p.id_usuarioP, NEW.id_usuarioL, 'like_pub', NEW.id_pubL
        FROM publicaciones p
        WHERE p.id_pub = NEW.id_pubL AND p.id_usuarioP <> NEW.id_usuarioL;
    END IF;
END$$

CREATE TRIGGER tr_notif_like_upd AFTER UPDATE ON likes
FOR EACH ROW
BEGIN
    IF NEW.estado = 1 AND OLD.estado <> 1 THEN
        DELETE FROM notificaciones
        WHERE tipo = 'like_pub' AND id_origen = NEW.id_usuarioL AND id_pub = NEW.id_pubL;

        INSERT INTO notificaciones (id_destino, id_origen, tipo, id_pub)
        SELECT p.id_usuarioP, NEW.id_usuarioL, 'like_pub', NEW.id_pubL
        FROM publicaciones p
        WHERE p.id_pub = NEW.id_pubL AND p.id_usuarioP <> NEW.id_usuarioL;
    ELSEIF NEW.estado <> 1 AND OLD.estado = 1 THEN
        DELETE FROM notificaciones
        WHERE tipo = 'like_pub' AND id_origen = NEW.id_usuarioL AND id_pub = NEW.id_pubL;
    END IF;
END$$

-- Comentarios y respuestas
CREATE TRIGGER tr_notif_comentario_ins AFTER INSERT ON comentarios
FOR EACH ROW
BEGIN
    DECLARE v_autor_padre INT DEFAULT NULL;

    IF NEW.id_padre IS NOT NULL THEN
        SET v_autor_padre = (SELECT id_usuarioC FROM comentarios WHERE id_come = NEW.id_padre);

        IF v_autor_padre IS NOT NULL AND v_autor_padre <> NEW.id_usuarioC THEN
            INSERT INTO notificaciones (id_destino, id_origen, tipo, id_pub, id_come)
            VALUES (v_autor_padre, NEW.id_usuarioC, 'respuesta', NEW.id_pubC, NEW.id_come);
        END IF;
    END IF;

    INSERT INTO notificaciones (id_destino, id_origen, tipo, id_pub, id_come)
    SELECT p.id_usuarioP, NEW.id_usuarioC, 'comentario', NEW.id_pubC, NEW.id_come
    FROM publicaciones p
    WHERE p.id_pub = NEW.id_pubC
      AND p.id_usuarioP <> NEW.id_usuarioC
      AND p.id_usuarioP <> IFNULL(v_autor_padre, 0);
END$$


CREATE TRIGGER tr_notif_likecom_ins AFTER INSERT ON likes_comentarios
FOR EACH ROW
BEGIN
    IF NEW.estado = 1 THEN
        DELETE FROM notificaciones
        WHERE tipo = 'like_com' AND id_origen = NEW.id_usuarioLC AND id_come = NEW.id_comeL;

        INSERT INTO notificaciones (id_destino, id_origen, tipo, id_pub, id_come)
        SELECT c.id_usuarioC, NEW.id_usuarioLC, 'like_com', c.id_pubC, NEW.id_comeL
        FROM comentarios c
        WHERE c.id_come = NEW.id_comeL AND c.id_usuarioC <> NEW.id_usuarioLC;
    END IF;
END$$

CREATE TRIGGER tr_notif_likecom_upd AFTER UPDATE ON likes_comentarios
FOR EACH ROW
BEGIN
    IF NEW.estado = 1 AND OLD.estado <> 1 THEN
        DELETE FROM notificaciones
        WHERE tipo = 'like_com' AND id_origen = NEW.id_usuarioLC AND id_come = NEW.id_comeL;

        INSERT INTO notificaciones (id_destino, id_origen, tipo, id_pub, id_come)
        SELECT c.id_usuarioC, NEW.id_usuarioLC, 'like_com', c.id_pubC, NEW.id_comeL
        FROM comentarios c
        WHERE c.id_come = NEW.id_comeL AND c.id_usuarioC <> NEW.id_usuarioLC;
    ELSEIF NEW.estado <> 1 AND OLD.estado = 1 THEN
        DELETE FROM notificaciones
        WHERE tipo = 'like_com' AND id_origen = NEW.id_usuarioLC AND id_come = NEW.id_comeL;
    END IF;
END$$


CREATE TRIGGER tr_notif_solicitud_ins AFTER INSERT ON solicitudes
FOR EACH ROW
BEGIN
    IF NEW.estado = 1 THEN
        INSERT INTO notificaciones (id_destino, id_origen, tipo, id_soli)
        VALUES (NEW.usuario_recibe, NEW.usuario_envia, 'solicitud', NEW.id_soli);
    END IF;
END$$

CREATE TRIGGER tr_notif_solicitud_upd AFTER UPDATE ON solicitudes
FOR EACH ROW
BEGIN
    IF OLD.estado = 1 AND NEW.estado = 2 THEN
    
        INSERT INTO notificaciones (id_destino, id_origen, tipo, id_soli)
        VALUES (NEW.usuario_envia, NEW.usuario_recibe, 'aceptada', NEW.id_soli);

        UPDATE notificaciones
        SET leida = 1
        WHERE tipo = 'solicitud' AND id_soli = NEW.id_soli;
    ELSEIF OLD.estado = 1 AND NEW.estado IN (3, 4) THEN
       
        DELETE FROM notificaciones
        WHERE tipo = 'solicitud' AND id_soli = NEW.id_soli;
    END IF;
END$$


CREATE PROCEDURE gestionar_notificaciones(
    IN p_accion     VARCHAR(10),
    IN p_id_usuario INT,
    IN p_id_notif   INT,
    IN p_limite     INT
)
BEGIN
    DECLARE v_limite INT DEFAULT 30;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SELECT 7 AS codigo;
    END;

    IF p_limite IS NOT NULL AND p_limite > 0 THEN
        SET v_limite = p_limite;
    END IF;

    IF p_accion = 'LISTAR' THEN
        SELECT 0 AS codigo, n.id_notif, n.tipo, n.id_pub, n.id_come, n.id_soli, n.leida, n.fecha,
               TIMESTAMPDIFF(SECOND, n.fecha, NOW()) AS segundos,
               u.id_usuario AS id_origen, u.nombre,
               IF(n.id_come IS NOT NULL, c.texto, p.texto) AS texto,
               s.estado AS estado_solicitud
        FROM notificaciones n
        INNER JOIN usuario u ON u.id_usuario = n.id_origen AND u.estado = 1
        LEFT JOIN publicaciones p ON p.id_pub = n.id_pub
        LEFT JOIN comentarios c ON c.id_come = n.id_come
        LEFT JOIN solicitudes s ON s.id_soli = n.id_soli
        WHERE n.id_destino = p_id_usuario
        ORDER BY n.fecha DESC, n.id_notif DESC
        LIMIT v_limite;

    ELSEIF p_accion = 'CONTAR' THEN
        SELECT 0 AS codigo, COUNT(*) AS total
        FROM notificaciones n
        INNER JOIN usuario u ON u.id_usuario = n.id_origen AND u.estado = 1
        WHERE n.id_destino = p_id_usuario AND n.leida = 0;

    ELSEIF p_accion = 'LEER' THEN
        UPDATE notificaciones
        SET leida = 1
        WHERE id_notif = p_id_notif AND id_destino = p_id_usuario;
        SELECT 0 AS codigo;

    ELSEIF p_accion = 'LEER_TODAS' THEN
      
        UPDATE notificaciones
        SET leida = 1
        WHERE id_notif > 0 AND id_destino = p_id_usuario AND leida = 0;
        SELECT 0 AS codigo;

    ELSE
        SELECT 2 AS codigo;
    END IF;
END$$

DELIMITER ;


INSERT INTO notificaciones (id_destino, id_origen, tipo, id_soli, fecha)
SELECT s.usuario_recibe, s.usuario_envia, 'solicitud', s.id_soli, s.fecha_envio
FROM solicitudes s
WHERE s.estado = 1
  AND NOT EXISTS (SELECT 1 FROM notificaciones n WHERE n.tipo = 'solicitud' AND n.id_soli = s.id_soli);
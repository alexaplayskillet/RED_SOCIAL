USE cosmix;

DROP PROCEDURE IF EXISTS gestionar_solicitudes;

DELIMITER $$

CREATE PROCEDURE gestionar_solicitudes(
    IN p_accion     VARCHAR(10),
    IN p_id_usuario INT,
    IN p_id_otro    INT,
    IN p_limite     INT
)
BEGIN
    DECLARE v_limite   INT DEFAULT 2147483647;
    DECLARE v_menor    INT;
    DECLARE v_mayor    INT;
    DECLARE v_id_soli  INT DEFAULT NULL;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 7 AS codigo;
    END;

    IF p_limite IS NOT NULL AND p_limite > 0 THEN
        SET v_limite = p_limite;
    END IF;
    SET v_menor = LEAST(p_id_usuario, IFNULL(p_id_otro, 0));
    SET v_mayor = GREATEST(p_id_usuario, IFNULL(p_id_otro, 0));

    IF p_accion = 'SUGERIR' THEN
        SELECT 0 AS codigo,
               u.id_usuario,
               u.nombre,
               u.tag,
               (u.fotografia IS NOT NULL) AS tiene_foto,
               (SELECT COUNT(*)
                FROM amistad b
                WHERE b.estado = 1
                  AND (b.id_usuario1 = u.id_usuario OR b.id_usuario2 = u.id_usuario)
                  AND IF(b.id_usuario1 = u.id_usuario, b.id_usuario2, b.id_usuario1) IN (
                        SELECT IF(m.id_usuario1 = p_id_usuario, m.id_usuario2, m.id_usuario1)
                        FROM amistad m
                        WHERE m.estado = 1
                          AND (m.id_usuario1 = p_id_usuario OR m.id_usuario2 = p_id_usuario)
                  )
               ) AS mutuos
        FROM usuario u
        WHERE u.estado = 1
          AND u.id_usuario <> p_id_usuario
          AND NOT EXISTS (
                SELECT 1 FROM amistad a
                WHERE a.estado = 1
                  AND a.id_usuario1 = LEAST(u.id_usuario, p_id_usuario)
                  AND a.id_usuario2 = GREATEST(u.id_usuario, p_id_usuario)
          )
          AND NOT EXISTS (
                SELECT 1 FROM solicitudes s
                WHERE s.estado = 1
                  AND ((s.usuario_envia = p_id_usuario AND s.usuario_recibe = u.id_usuario)
                    OR (s.usuario_envia = u.id_usuario AND s.usuario_recibe = p_id_usuario))
          )
        ORDER BY RAND()
        LIMIT v_limite;

    ELSEIF p_accion = 'RECIBIDAS' THEN
        SELECT 0 AS codigo,
               u.id_usuario,
               u.nombre,
               u.tag,
               (u.fotografia IS NOT NULL) AS tiene_foto,
               (SELECT COUNT(*)
                FROM amistad b
                WHERE b.estado = 1
                  AND (b.id_usuario1 = u.id_usuario OR b.id_usuario2 = u.id_usuario)
                  AND IF(b.id_usuario1 = u.id_usuario, b.id_usuario2, b.id_usuario1) IN (
                        SELECT IF(m.id_usuario1 = p_id_usuario, m.id_usuario2, m.id_usuario1)
                        FROM amistad m
                        WHERE m.estado = 1
                          AND (m.id_usuario1 = p_id_usuario OR m.id_usuario2 = p_id_usuario)
                  )
               ) AS mutuos
        FROM solicitudes s
        JOIN usuario u ON u.id_usuario = s.usuario_envia
        WHERE s.usuario_recibe = p_id_usuario
          AND s.estado = 1
          AND u.estado = 1
        ORDER BY s.fecha_envio DESC, s.id_soli DESC
        LIMIT v_limite;

    ELSEIF p_accion = 'ENVIAR' THEN
        IF p_id_otro = p_id_usuario THEN
            SELECT 2 AS codigo;
        ELSEIF NOT EXISTS (SELECT 1 FROM usuario WHERE id_usuario = p_id_otro AND estado = 1) THEN
            SELECT 1 AS codigo;
        ELSEIF EXISTS (SELECT 1 FROM amistad
                       WHERE id_usuario1 = v_menor AND id_usuario2 = v_mayor AND estado = 1) THEN
            SELECT 3 AS codigo;
        ELSEIF EXISTS (SELECT 1 FROM solicitudes
                       WHERE usuario_envia = p_id_usuario AND usuario_recibe = p_id_otro AND estado = 1) THEN
            SELECT 4 AS codigo;
        ELSEIF EXISTS (SELECT 1 FROM solicitudes
                       WHERE usuario_envia = p_id_otro AND usuario_recibe = p_id_usuario AND estado = 1) THEN
            SELECT 5 AS codigo;
        ELSE
            INSERT INTO solicitudes (usuario_envia, usuario_recibe, estado)
            VALUES (p_id_usuario, p_id_otro, 1);
            SELECT 0 AS codigo;
        END IF;

    ELSEIF p_accion = 'ACEPTAR' THEN
        START TRANSACTION;

        SELECT MAX(id_soli) INTO v_id_soli
        FROM solicitudes
        WHERE usuario_envia = p_id_otro AND usuario_recibe = p_id_usuario AND estado = 1;

        IF v_id_soli IS NULL THEN
            ROLLBACK;
            SELECT 6 AS codigo;
        ELSE
            UPDATE solicitudes
            SET estado = 2, fecha_respuesta = NOW()
            WHERE usuario_envia = p_id_otro AND usuario_recibe = p_id_usuario AND estado = 1;

            INSERT IGNORE INTO amistad (id_usuario1, id_usuario2, estado)
            VALUES (v_menor, v_mayor, 1);

            COMMIT;
            SELECT 0 AS codigo;
        END IF;

    ELSEIF p_accion = 'RECHAZAR' THEN
        UPDATE solicitudes
        SET estado = 3, fecha_respuesta = NOW()
        WHERE usuario_envia = p_id_otro AND usuario_recibe = p_id_usuario AND estado = 1;

        IF ROW_COUNT() = 0 THEN
            SELECT 6 AS codigo;
        ELSE
            SELECT 0 AS codigo;
        END IF;

    ELSE
        SELECT 9 AS codigo;
    END IF;
END$$

DELIMITER ;
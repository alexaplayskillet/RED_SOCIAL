-- Procedimiento de mensajeria (chats y mensajes).
-- Requiere las tablas usuario, amistad, chats y mensaje (creacion_bd.sql).
-- Se puede ejecutar mas de una vez: reemplaza el procedimiento existente.
--
-- Acciones (p_accion):
--   CHATS    lista los chats del usuario con el ultimo mensaje de cada uno.
--   ABRIR    devuelve (y crea si hace falta) el chat con otro usuario. Solo se
--            crea un chat nuevo si son amigos.
--   MENSAJES lista los mensajes del chat. Si p_id_ultimo > 0 solo devuelve los
--            mas nuevos que ese id (sirve para actualizar la conversacion).
--   ENVIAR   guarda un mensaje nuevo. Solo se puede enviar a amigos.
--
-- Codigos:
--   0 correcto
--   1 el otro usuario no existe
--   2 accion no valida
--   3 no son amigos
--   4 el mensaje esta vacio
--   5 no se puede chatear con uno mismo
--   6 el mensaje pasa de 255 caracteres
--   7 error inesperado

USE cosmix;

DROP PROCEDURE IF EXISTS gestionar_mensajes;

DELIMITER $$

CREATE PROCEDURE gestionar_mensajes (
    IN p_accion     VARCHAR(10),
    IN p_id_usuario INT,
    IN p_id_otro    INT,
    IN p_texto      VARCHAR(1000),
    IN p_id_ultimo  INT
)
BEGIN
    DECLARE v_menor      INT;
    DECLARE v_mayor      INT;
    DECLARE v_id_chat    INT DEFAULT NULL;
    DECLARE v_son_amigos INT DEFAULT 0;
    DECLARE v_texto      VARCHAR(1000);
    DECLARE v_id_mensaje INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 7 AS codigo;
    END;

    SET v_menor = LEAST(p_id_usuario, IFNULL(p_id_otro, 0));
    SET v_mayor = GREATEST(p_id_usuario, IFNULL(p_id_otro, 0));

    IF p_accion = 'CHATS' THEN
        SELECT 0 AS codigo,
               c.id_chat,
               u.id_usuario,
               u.nombre,
               u.tag,
               (u.fotografia IS NOT NULL) AS tiene_foto,
               m.contenido                AS ultimo,
               m.fecha_envio              AS ultima_fecha,
               m.usuario_envia            AS ultimo_emisor
        FROM chats c
        JOIN usuario u
          ON u.id_usuario = IF(c.id_usuario1 = p_id_usuario, c.id_usuario2, c.id_usuario1)
        LEFT JOIN mensaje m
          ON m.id_mensaje = (SELECT MAX(m2.id_mensaje)
                             FROM mensaje m2
                             WHERE m2.id_chatM = c.id_chat)
        WHERE (c.id_usuario1 = p_id_usuario OR c.id_usuario2 = p_id_usuario)
          AND u.estado = 1
        ORDER BY IFNULL(m.fecha_envio, NOW()) DESC, c.id_chat DESC;

    ELSEIF p_accion NOT IN ('ABRIR', 'MENSAJES', 'ENVIAR') THEN
        SELECT 2 AS codigo;

    ELSEIF p_id_otro IS NULL OR p_id_otro = p_id_usuario THEN
        SELECT 5 AS codigo;

    ELSEIF NOT EXISTS (SELECT 1 FROM usuario
                       WHERE id_usuario = p_id_otro AND estado = 1) THEN
        SELECT 1 AS codigo;

    ELSE
        SELECT COUNT(*) INTO v_son_amigos
        FROM amistad
        WHERE id_usuario1 = v_menor AND id_usuario2 = v_mayor AND estado = 1;

        SELECT id_chat INTO v_id_chat
        FROM chats
        WHERE id_usuario1 = v_menor AND id_usuario2 = v_mayor
        LIMIT 1;

        IF p_accion = 'ABRIR' THEN
            -- Si ya existia el chat se puede abrir aunque ya no sean amigos
            -- (solo lectura); uno nuevo solo se crea entre amigos.
            IF v_id_chat IS NULL AND v_son_amigos = 0 THEN
                SELECT 3 AS codigo;
            ELSE
                IF v_id_chat IS NULL THEN
                    INSERT INTO chats (id_usuario1, id_usuario2)
                    VALUES (v_menor, v_mayor);
                    SET v_id_chat = LAST_INSERT_ID();
                END IF;

                SELECT 0 AS codigo,
                       v_id_chat AS id_chat,
                       u.id_usuario,
                       u.nombre,
                       u.tag,
                       (u.fotografia IS NOT NULL) AS tiene_foto,
                       v_son_amigos AS puede_escribir
                FROM usuario u
                WHERE u.id_usuario = p_id_otro;
            END IF;

        ELSEIF p_accion = 'MENSAJES' THEN
            -- Sin chat todavia: no hay mensajes, devuelve una lista vacia.
            SELECT 0 AS codigo, t.id_mensaje, t.usuario_envia, t.contenido,
                   t.fecha_envio, t.mio
            FROM (
                SELECT m.id_mensaje, m.usuario_envia, m.contenido, m.fecha_envio,
                       (m.usuario_envia = p_id_usuario) AS mio
                FROM mensaje m
                WHERE m.id_chatM = v_id_chat
                  AND m.id_mensaje > IFNULL(p_id_ultimo, 0)
                ORDER BY m.id_mensaje DESC
                LIMIT 200
            ) t
            ORDER BY t.id_mensaje ASC;

        ELSE -- ENVIAR
            SET v_texto = TRIM(IFNULL(p_texto, ''));

            IF v_son_amigos = 0 THEN
                SELECT 3 AS codigo;
            ELSEIF v_texto = '' THEN
                SELECT 4 AS codigo;
            ELSEIF CHAR_LENGTH(v_texto) > 255 THEN
                SELECT 6 AS codigo;
            ELSE
                START TRANSACTION;

                IF v_id_chat IS NULL THEN
                    INSERT INTO chats (id_usuario1, id_usuario2)
                    VALUES (v_menor, v_mayor);
                    SET v_id_chat = LAST_INSERT_ID();
                END IF;

                INSERT INTO mensaje (id_chatM, usuario_envia, contenido)
                VALUES (v_id_chat, p_id_usuario, v_texto);
                SET v_id_mensaje = LAST_INSERT_ID();

                COMMIT;

                SELECT 0 AS codigo, id_mensaje, fecha_envio
                FROM mensaje
                WHERE id_mensaje = v_id_mensaje;
            END IF;
        END IF;
    END IF;
END$$

DELIMITER ;

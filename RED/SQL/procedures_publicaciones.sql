USE cosmix;


DROP PROCEDURE IF EXISTS migrar_comentarios;

DELIMITER $$

CREATE PROCEDURE migrar_comentarios()
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'comentarios'
          AND COLUMN_NAME = 'id_padre'
    ) THEN
        ALTER TABLE comentarios
            ADD COLUMN id_padre INT NULL AFTER id_usuarioC,
            ADD CONSTRAINT fk_padreC
                FOREIGN KEY (id_padre) REFERENCES comentarios(id_come)
                ON DELETE CASCADE ON UPDATE CASCADE;
    END IF;


    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'publicaciones'
          AND COLUMN_NAME = 'tipo_multimedia'
    ) THEN
        ALTER TABLE publicaciones
            ADD COLUMN tipo_multimedia VARCHAR(50) NULL AFTER multimedia;
    END IF;
END$$

DELIMITER ;

CALL migrar_comentarios();
DROP PROCEDURE IF EXISTS migrar_comentarios;


UPDATE publicaciones
SET tipo_multimedia = CASE
        WHEN HEX(LEFT(multimedia, 3)) = 'FFD8FF' THEN 'image/jpeg'
        WHEN HEX(LEFT(multimedia, 4)) = '89504E47' THEN 'image/png'
        WHEN HEX(LEFT(multimedia, 4)) = '47494638' THEN 'image/gif'
        WHEN HEX(LEFT(multimedia, 4)) = '52494646'
             AND HEX(SUBSTRING(multimedia, 9, 4)) = '57454250' THEN 'image/webp'
        ELSE 'application/octet-stream'
    END
WHERE id_pub > 0 AND multimedia IS NOT NULL AND tipo_multimedia IS NULL;

CREATE TABLE IF NOT EXISTS likes_comentarios (
    id_likeC     INT PRIMARY KEY AUTO_INCREMENT,
    id_comeL     INT NOT NULL,
    id_usuarioLC INT NOT NULL,
    fechaLC      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado       TINYINT NOT NULL DEFAULT 1,

    CONSTRAINT fk_comeL
        FOREIGN KEY (id_comeL) REFERENCES comentarios(id_come)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_usuarioLC
        FOREIGN KEY (id_usuarioLC) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT uq_like_com UNIQUE (id_comeL, id_usuarioLC)
);

CREATE OR REPLACE VIEW v_cargaPublicaciones AS
SELECT u.id_usuario, u.nombre, u.tag, u.fotografia,
       p.id_pub, p.texto, p.fecha_pub,
       (p.multimedia IS NOT NULL) AS tiene_media, p.tipo_multimedia,
       TIMESTAMPDIFF(SECOND, p.fecha_pub, NOW()) AS segundos,
       (SELECT COUNT(*) FROM likes l       WHERE l.id_pubL  = p.id_pub AND l.estado = 1) AS likes,
       (SELECT COUNT(*) FROM comentarios c WHERE c.id_pubC  = p.id_pub AND c.estado = 1) AS comentarios,
       (SELECT COUNT(*) FROM compartidos s WHERE s.id_pubcc = p.id_pub AND s.estado = 1) AS compartidos,
       (SELECT COUNT(*) FROM guardados g   WHERE g.id_pubG  = p.id_pub AND g.estado = 1) AS guardados
FROM usuario u
INNER JOIN publicaciones p ON u.id_usuario = p.id_usuarioP
WHERE u.estado = 1;

CREATE OR REPLACE VIEW v_cargaComentarios AS
SELECT u.id_usuario, u.nombre, u.fotografia,
       c.id_come, c.id_pubC, c.id_padre, c.texto, c.fechaC
FROM usuario u
INNER JOIN comentarios c ON u.id_usuario = c.id_usuarioC
WHERE c.estado = 1 AND u.estado = 1;

DROP PROCEDURE IF EXISTS gestionar_publicaciones;

DELIMITER $$

CREATE PROCEDURE gestionar_publicaciones(
    IN p_accion     VARCHAR(10),
    IN p_id_usuario INT,
    IN p_id_pub     INT,
    IN p_id_com     INT,
    IN p_texto      VARCHAR(1000),
    IN p_multimedia MEDIUMBLOB,
    IN p_tipo       VARCHAR(50)
)
BEGIN
    DECLARE v_texto   VARCHAR(1000);
    DECLARE v_existe  INT DEFAULT 0;
    DECLARE v_id_like INT DEFAULT NULL;
    DECLARE v_padre   INT DEFAULT NULL;
    DECLARE v_propia  INT DEFAULT 0;
    DECLARE v_media   INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 7 AS codigo;
    END;

    SET v_texto = NULLIF(TRIM(IFNULL(p_texto, '')), '');

    SELECT COUNT(*) INTO v_existe
    FROM publicaciones
    WHERE id_pub = p_id_pub;

    SELECT COUNT(*) INTO v_propia
    FROM publicaciones
    WHERE id_pub = p_id_pub AND id_usuarioP = p_id_usuario;

    IF p_accion = 'CREAR' THEN
        IF v_texto IS NULL AND p_multimedia IS NULL THEN
            SELECT 3 AS codigo;
        ELSEIF CHAR_LENGTH(IFNULL(v_texto, '')) > 255 THEN
            SELECT 4 AS codigo;
        ELSE
            INSERT INTO publicaciones (id_usuarioP, texto, multimedia, tipo_multimedia)
            VALUES (p_id_usuario, v_texto, p_multimedia,
                    IF(p_multimedia IS NULL, NULL, p_tipo));

            SELECT 0 AS codigo, LAST_INSERT_ID() AS id_pub;
        END IF;

    ELSEIF p_accion = 'EDITAR' THEN
        IF v_existe = 0 THEN
            SELECT 1 AS codigo;
        ELSEIF v_propia = 0 THEN
            SELECT 6 AS codigo;
        ELSEIF CHAR_LENGTH(IFNULL(v_texto, '')) > 255 THEN
            SELECT 4 AS codigo;
        ELSE
          
            IF p_multimedia IS NOT NULL THEN
                SET v_media = 1;
            ELSEIF p_tipo = 'QUITAR' THEN
                SET v_media = 0;
            ELSE
                SET v_media = (SELECT multimedia IS NOT NULL FROM publicaciones WHERE id_pub = p_id_pub);
            END IF;

            IF v_texto IS NULL AND v_media = 0 THEN
                SELECT 3 AS codigo;
            ELSE
                UPDATE publicaciones
                SET texto = v_texto,
                    multimedia = CASE
                        WHEN p_multimedia IS NOT NULL THEN p_multimedia
                        WHEN p_tipo = 'QUITAR' THEN NULL
                        ELSE multimedia
                    END,
                    tipo_multimedia = CASE
                        WHEN p_multimedia IS NOT NULL THEN p_tipo
                        WHEN p_tipo = 'QUITAR' THEN NULL
                        ELSE tipo_multimedia
                    END
                WHERE id_pub = p_id_pub AND id_usuarioP = p_id_usuario;

                SELECT 0 AS codigo;
            END IF;
        END IF;

    ELSEIF p_accion = 'ELIMINAR' THEN
        IF v_existe = 0 THEN
            SELECT 1 AS codigo;
        ELSEIF v_propia = 0 THEN
            SELECT 6 AS codigo;
        ELSE
            START TRANSACTION;

            
            DELETE FROM likes       WHERE id_pubL  = p_id_pub;
            DELETE FROM comentarios WHERE id_pubC  = p_id_pub;
            DELETE FROM compartidos WHERE id_pubcc = p_id_pub;
            DELETE FROM guardados   WHERE id_pubG  = p_id_pub;
            DELETE FROM publicaciones
            WHERE id_pub = p_id_pub AND id_usuarioP = p_id_usuario;

            COMMIT;
            SELECT 0 AS codigo;
        END IF;

    ELSEIF p_accion = 'MEDIA' THEN
        IF NOT EXISTS (SELECT 1 FROM publicaciones WHERE id_pub = p_id_pub AND multimedia IS NOT NULL) THEN
            SELECT 1 AS codigo;
        ELSE
            SELECT 0 AS codigo, multimedia AS media, tipo_multimedia AS tipo
            FROM publicaciones
            WHERE id_pub = p_id_pub;
        END IF;

    ELSEIF p_accion = 'LIKE' THEN
        IF v_existe = 0 THEN
            SELECT 1 AS codigo;
        ELSE
            SET v_id_like = (SELECT id_like FROM likes
                             WHERE id_pubL = p_id_pub AND id_usuarioL = p_id_usuario
                             ORDER BY id_like LIMIT 1);

            IF v_id_like IS NULL THEN
                INSERT INTO likes (id_pubL, id_usuarioL) VALUES (p_id_pub, p_id_usuario);
                SET v_id_like = LAST_INSERT_ID();
            ELSE
                UPDATE likes
                SET estado = IF(estado = 1, 0, 1), fechaL = NOW()
                WHERE id_like = v_id_like;
            END IF;

            SELECT 0 AS codigo,
                   (SELECT estado FROM likes WHERE id_like = v_id_like) AS dio_like,
                   (SELECT COUNT(*) FROM likes WHERE id_pubL = p_id_pub AND estado = 1) AS total;
        END IF;

    ELSEIF p_accion = 'COMENTAR' THEN
    
        IF p_id_com IS NOT NULL THEN
            SET v_padre = (SELECT IFNULL(id_padre, id_come) FROM comentarios
                           WHERE id_come = p_id_com AND id_pubC = p_id_pub AND estado = 1);
        END IF;

        IF v_existe = 0 THEN
            SELECT 1 AS codigo;
        ELSEIF p_id_com IS NOT NULL AND v_padre IS NULL THEN
            SELECT 5 AS codigo;
        ELSEIF v_texto IS NULL THEN
            SELECT 3 AS codigo;
        ELSEIF CHAR_LENGTH(v_texto) > 255 THEN
            SELECT 4 AS codigo;
        ELSE
            INSERT INTO comentarios (id_pubC, id_usuarioC, id_padre, texto)
            VALUES (p_id_pub, p_id_usuario, v_padre, v_texto);

            SELECT 0 AS codigo, c.id_come, c.id_padre, c.texto, c.fechaC,
                   0 AS segundos, 0 AS likes, 0 AS dio_like,
                   u.id_usuario, u.nombre,
                   (SELECT COUNT(*) FROM comentarios WHERE id_pubC = p_id_pub AND estado = 1) AS total
            FROM comentarios c
            INNER JOIN usuario u ON u.id_usuario = c.id_usuarioC
            WHERE c.id_come = LAST_INSERT_ID();
        END IF;

    ELSEIF p_accion = 'LIKE_COM' THEN
        IF NOT EXISTS (SELECT 1 FROM comentarios WHERE id_come = p_id_com AND estado = 1) THEN
            SELECT 5 AS codigo;
        ELSE
            INSERT INTO likes_comentarios (id_comeL, id_usuarioLC)
            VALUES (p_id_com, p_id_usuario)
            ON DUPLICATE KEY UPDATE estado = IF(estado = 1, 0, 1), fechaLC = NOW();

            SELECT 0 AS codigo,
                   (SELECT estado FROM likes_comentarios
                    WHERE id_comeL = p_id_com AND id_usuarioLC = p_id_usuario) AS dio_like,
                   (SELECT COUNT(*) FROM likes_comentarios
                    WHERE id_comeL = p_id_com AND estado = 1) AS total;
        END IF;

    ELSEIF p_accion = 'LISTAR_COM' THEN
        SELECT 0 AS codigo, c.id_come, c.id_padre, c.texto, c.fechaC,
               TIMESTAMPDIFF(SECOND, c.fechaC, NOW()) AS segundos,
               u.id_usuario, u.nombre,
               (SELECT COUNT(*) FROM likes_comentarios lc
                WHERE lc.id_comeL = c.id_come AND lc.estado = 1) AS likes,
               EXISTS (SELECT 1 FROM likes_comentarios lc
                       WHERE lc.id_comeL = c.id_come
                         AND lc.id_usuarioLC = p_id_usuario
                         AND lc.estado = 1) AS dio_like
        FROM comentarios c
        INNER JOIN usuario u ON u.id_usuario = c.id_usuarioC
        WHERE c.id_pubC = p_id_pub AND c.estado = 1 AND u.estado = 1
        ORDER BY c.fechaC ASC, c.id_come ASC;

    ELSEIF p_accion = 'LIKES_USR' THEN
        SELECT 0 AS codigo, id_pubL AS id_pub
        FROM likes
        WHERE id_usuarioL = p_id_usuario AND estado = 1;

    ELSE
        SELECT 2 AS codigo;
    END IF;
END$$

DELIMITER ;
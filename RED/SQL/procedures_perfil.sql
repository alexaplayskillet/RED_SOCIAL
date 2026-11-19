-- Archivo 03: procedimientos del perfil de usuario.
-- Requiere haber ejecutado antes los archivos 01 y 02.
-- Se puede ejecutar mas de una vez: reemplaza los procedimientos existentes.
-- Cada procedimiento agrupa varias acciones y recibe la accion en el primer parametro.

USE cosmix;

DROP PROCEDURE IF EXISTS gestionar_portada;
DROP PROCEDURE IF EXISTS gestionar_perfil;
DROP PROCEDURE IF EXISTS gestionar_imagen_perfil;
DROP PROCEDURE IF EXISTS gestionar_amigos;
DROP PROCEDURE IF EXISTS gestionar_galeria;

DELIMITER $$

CREATE PROCEDURE gestionar_perfil (
    IN p_accion     VARCHAR(10),
    IN p_id_usuario INT,
    IN p_nombre     VARCHAR(100),
    IN p_tag        VARCHAR(50),
    IN p_correo     VARCHAR(100),
    IN p_fecha_nac  DATE
)
BEGIN
    DECLARE EXIT HANDLER FOR 1062
    BEGIN
        SELECT 6 AS codigo, NULL AS nombre, NULL AS tag,
               NULL AS correo, NULL AS fecha_nac;
    END;

    IF NOT EXISTS (SELECT 1 FROM usuario
                   WHERE id_usuario = p_id_usuario AND estado = 1) THEN
        SELECT 1 AS codigo, NULL AS nombre, NULL AS tag,
               NULL AS correo, NULL AS fecha_nac;

    ELSEIF p_accion = 'OBTENER' THEN
        SELECT 0 AS codigo, nombre, tag, correo, fecha_nac
        FROM usuario
        WHERE id_usuario = p_id_usuario;

    ELSEIF p_accion = 'EDITAR' THEN
        IF p_nombre IS NULL OR TRIM(p_nombre) = ''
           OR p_tag IS NULL OR TRIM(p_tag) = ''
           OR p_correo IS NULL OR TRIM(p_correo) = ''
           OR p_fecha_nac IS NULL THEN
            SELECT 5 AS codigo, NULL AS nombre, NULL AS tag,
                   NULL AS correo, NULL AS fecha_nac;

        ELSEIF EXISTS (SELECT 1 FROM usuario
                       WHERE tag = p_tag AND id_usuario <> p_id_usuario) THEN
            SELECT 3 AS codigo, NULL AS nombre, NULL AS tag,
                   NULL AS correo, NULL AS fecha_nac;

        ELSEIF EXISTS (SELECT 1 FROM usuario
                       WHERE correo = p_correo AND id_usuario <> p_id_usuario) THEN
            SELECT 4 AS codigo, NULL AS nombre, NULL AS tag,
                   NULL AS correo, NULL AS fecha_nac;

        ELSE
            UPDATE usuario
            SET    nombre    = p_nombre,
                   tag       = p_tag,
                   correo    = p_correo,
                   fecha_nac = p_fecha_nac
            WHERE  id_usuario = p_id_usuario;

            SELECT 0 AS codigo, nombre, tag, correo, fecha_nac
            FROM usuario
            WHERE id_usuario = p_id_usuario;
        END IF;

    ELSE
        SELECT 2 AS codigo, NULL AS nombre, NULL AS tag,
               NULL AS correo, NULL AS fecha_nac;
    END IF;
END$$

CREATE PROCEDURE gestionar_imagen_perfil (
    IN p_accion     VARCHAR(10),
    IN p_id_usuario INT,
    IN p_tipo       VARCHAR(10),
    IN p_imagen     MEDIUMBLOB
)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM usuario
                   WHERE id_usuario = p_id_usuario AND estado = 1) THEN
        SELECT 1 AS codigo, NULL AS imagen;

    ELSEIF p_tipo NOT IN ('PORTADA', 'FOTO') THEN
        SELECT 4 AS codigo, NULL AS imagen;

    ELSEIF p_accion = 'GUARDAR' THEN
        IF p_imagen IS NULL OR LENGTH(p_imagen) = 0 THEN
            SELECT 3 AS codigo, NULL AS imagen;
        ELSE
            IF p_tipo = 'PORTADA' THEN
                UPDATE usuario SET portada = p_imagen
                WHERE id_usuario = p_id_usuario;
            ELSE
                UPDATE usuario SET fotografia = p_imagen
                WHERE id_usuario = p_id_usuario;
            END IF;
            SELECT 0 AS codigo, NULL AS imagen;
        END IF;

    ELSEIF p_accion = 'ELIMINAR' THEN
        IF p_tipo = 'PORTADA' THEN
            UPDATE usuario SET portada = NULL
            WHERE id_usuario = p_id_usuario;
        ELSE
            UPDATE usuario SET fotografia = NULL
            WHERE id_usuario = p_id_usuario;
        END IF;
        SELECT 0 AS codigo, NULL AS imagen;

    ELSEIF p_accion = 'OBTENER' THEN
        IF p_tipo = 'PORTADA' THEN
            SELECT 0 AS codigo, portada AS imagen
            FROM usuario WHERE id_usuario = p_id_usuario;
        ELSE
            SELECT 0 AS codigo, fotografia AS imagen
            FROM usuario WHERE id_usuario = p_id_usuario;
        END IF;

    ELSE
        SELECT 2 AS codigo, NULL AS imagen;
    END IF;
END$$

CREATE PROCEDURE gestionar_amigos(
    IN p_accion     VARCHAR(10),
    IN p_id_usuario INT,
    IN p_id_amigo   INT,
    IN p_busqueda   VARCHAR(100),
    IN p_limite     INT
)
BEGIN
    DECLARE v_limite INT DEFAULT 2147483647;
    DECLARE v_patron VARCHAR(110);
    DECLARE v_menor  INT;
    DECLARE v_mayor  INT;
    DECLARE v_son_amigos INT DEFAULT 0;

    IF p_limite IS NOT NULL AND p_limite > 0 THEN
        SET v_limite = p_limite;
    END IF;
    SET v_patron = CONCAT('%', IFNULL(TRIM(p_busqueda), ''), '%');
    SET v_menor  = LEAST(p_id_usuario, IFNULL(p_id_amigo, 0));
    SET v_mayor  = GREATEST(p_id_usuario, IFNULL(p_id_amigo, 0));

    IF p_accion = 'LISTAR' THEN
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
        FROM amistad a
        JOIN usuario u
          ON u.id_usuario = IF(a.id_usuario1 = p_id_usuario, a.id_usuario2, a.id_usuario1)
        WHERE a.estado = 1
          AND u.estado = 1
          AND (a.id_usuario1 = p_id_usuario OR a.id_usuario2 = p_id_usuario)
          AND (u.tag LIKE v_patron OR u.nombre LIKE v_patron)
        ORDER BY a.id_amistad DESC
        LIMIT v_limite;

    ELSEIF p_accion = 'CONTAR' THEN
        SELECT 0 AS codigo, COUNT(*) AS total
        FROM amistad a
        JOIN usuario u
          ON u.id_usuario = IF(a.id_usuario1 = p_id_usuario, a.id_usuario2, a.id_usuario1)
        WHERE a.estado = 1
          AND u.estado = 1
          AND (a.id_usuario1 = p_id_usuario OR a.id_usuario2 = p_id_usuario);

    ELSEIF p_accion = 'ELIMINAR' THEN
        SELECT COUNT(*) INTO v_son_amigos
        FROM amistad
        WHERE id_usuario1 = v_menor AND id_usuario2 = v_mayor AND estado = 1;

        IF v_son_amigos = 0 THEN
            SELECT 1 AS codigo;
        ELSE
            DELETE FROM amistad
            WHERE id_usuario1 = v_menor AND id_usuario2 = v_mayor;
            SELECT 0 AS codigo;
        END IF;

    ELSEIF p_accion = 'FOTO' THEN
        IF EXISTS (SELECT 1 FROM usuario WHERE id_usuario = p_id_amigo AND estado = 1) THEN
            SELECT 0 AS codigo, fotografia AS imagen
            FROM usuario WHERE id_usuario = p_id_amigo;
        ELSE
            SELECT 1 AS codigo, NULL AS imagen;
        END IF;

    ELSE
        SELECT 2 AS codigo;
    END IF;
END$$

CREATE PROCEDURE gestionar_galeria(
    IN p_accion     VARCHAR(10),
    IN p_id_usuario INT,
    IN p_id_pub     INT,
    IN p_texto      VARCHAR(1000),
    IN p_limite     INT
)
BEGIN
    DECLARE v_limite INT DEFAULT 2147483647;
    DECLARE v_texto  VARCHAR(1000);
    DECLARE v_existe INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 7 AS codigo;
    END;

    IF p_limite IS NOT NULL AND p_limite > 0 THEN
        SET v_limite = p_limite;
    END IF;

    SELECT COUNT(*) INTO v_existe
    FROM publicaciones
    WHERE id_pub = p_id_pub
      AND id_usuarioP = p_id_usuario
      AND multimedia IS NOT NULL;

    IF p_accion = 'LISTAR' THEN
        SELECT 0 AS codigo, id_pub, texto, fecha_pub
        FROM publicaciones
        WHERE id_usuarioP = p_id_usuario
          AND multimedia IS NOT NULL
          AND (   HEX(LEFT(multimedia, 3)) = 'FFD8FF'
               OR HEX(LEFT(multimedia, 4)) IN ('89504E47', '47494638')
               OR (HEX(LEFT(multimedia, 4)) = '52494646'
                   AND HEX(SUBSTRING(multimedia, 9, 4)) = '57454250'))
        ORDER BY fecha_pub DESC, id_pub DESC
        LIMIT v_limite;

    ELSEIF p_accion = 'OBTENER' THEN
        IF v_existe = 0 THEN
            SELECT 1 AS codigo, NULL AS imagen;
        ELSE
            SELECT 0 AS codigo, multimedia AS imagen
            FROM publicaciones
            WHERE id_pub = p_id_pub AND id_usuarioP = p_id_usuario;
        END IF;

    ELSEIF p_accion = 'EDITAR' THEN
        SET v_texto = NULLIF(TRIM(IFNULL(p_texto, '')), '');

        IF v_existe = 0 THEN
            SELECT 1 AS codigo;
        ELSEIF CHAR_LENGTH(IFNULL(v_texto, '')) > 255 THEN
            SELECT 3 AS codigo;
        ELSE
            UPDATE publicaciones
            SET texto = v_texto
            WHERE id_pub = p_id_pub AND id_usuarioP = p_id_usuario;
            SELECT 0 AS codigo;
        END IF;

    ELSEIF p_accion = 'ELIMINAR' THEN
        IF v_existe = 0 THEN
            SELECT 1 AS codigo;
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

    ELSE
        SELECT 2 AS codigo;
    END IF;
END$$

DELIMITER ;
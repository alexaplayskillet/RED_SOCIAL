DELIMITER //

CREATE PROCEDURE crear_coleccion(
    IN p_id_usuario INT,
    IN p_nombre VARCHAR(100),
    IN p_descripcion VARCHAR(255),
    OUT p_id_tablero INT
)
BEGIN
    IF TRIM(p_nombre) = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El nombre de la colección es obligatorio';
    END IF;

    INSERT INTO tableros (id_usuarioT, nombre, descripcion)
    VALUES (p_id_usuario, TRIM(p_nombre), p_descripcion);

    SET p_id_tablero = LAST_INSERT_ID();
END //

CREATE PROCEDURE guardar_publicacion(
    IN p_id_usuario INT,
    IN p_id_tablero INT,
    IN p_id_pub INT
)
BEGIN
    /* la colección debe ser del usuario y estar activa */
    IF NOT EXISTS (SELECT 1 FROM tableros
                   WHERE id_tablero = p_id_tablero
                     AND id_usuarioT = p_id_usuario AND estado = 1) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La colección no existe o no te pertenece';
    END IF;

    /* la publicación debe existir */
    IF NOT EXISTS (SELECT 1 FROM publicaciones WHERE id_pub = p_id_pub) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La publicación no existe';
    END IF;

    /* si ya estaba (o la habías quitado), la reactiva */
    INSERT INTO guardados (id_pubG, id_tableroG)
    VALUES (p_id_pub, p_id_tablero)
    ON DUPLICATE KEY UPDATE estado = 1, fechaG = CURRENT_TIMESTAMP;
END //

CREATE PROCEDURE quitar_guardado(
    IN p_id_usuario INT,
    IN p_id_tablero INT,
    IN p_id_pub INT
)
BEGIN
    UPDATE guardados g
    INNER JOIN tableros t ON t.id_tablero = g.id_tableroG
    SET g.estado = 0
    WHERE g.id_tableroG = p_id_tablero
      AND g.id_pubG = p_id_pub
      AND t.id_usuarioT = p_id_usuario;
END //

DELIMITER ;
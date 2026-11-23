DELIMITER //

CREATE FUNCTION Est_Likes(p_id_usuario INT) RETURNS INT
READS SQL DATA
BEGIN
    DECLARE v_total INT DEFAULT 0;

    SELECT COUNT(*) INTO v_total
    FROM likes l
    INNER JOIN publicaciones p ON l.id_pubL = p.id_pub
    WHERE p.id_usuarioP = p_id_usuario
      AND l.estado = 1;

    RETURN v_total;
END //

DELIMITER ;

DELIMITER //

CREATE FUNCTION Est_Comms(p_id_usuario INT) RETURNS INT
READS SQL DATA
BEGIN
    DECLARE v_total INT DEFAULT 0;

    SELECT COUNT(*) INTO v_total
    FROM comentarios c
    INNER JOIN publicaciones p ON c.id_pubC = p.id_pub
    WHERE p.id_usuarioP = p_id_usuario
      AND c.estado = 1;

    RETURN v_total;
END //

DELIMITER ;

DELIMITER //

CREATE FUNCTION Est_Pubs(p_id_usuario INT) RETURNS INT
READS SQL DATA
BEGIN
    DECLARE v_total INT DEFAULT 0;

    SELECT COUNT(*) INTO v_total
    FROM publicaciones 
    WHERE id_usuarioP = p_id_usuario
	 AND fecha_pub >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
      AND fecha_pub <  DATE_FORMAT(CURDATE(), '%Y-%m-01') + INTERVAL 1 MONTH;

    RETURN v_total;
END //

DELIMITER ;

DELIMITER //

CREATE FUNCTION Est_Amigos(p_id_usuario INT) RETURNS INT
READS SQL DATA
BEGIN
    DECLARE v_total INT DEFAULT 0;

    SELECT COUNT(*) INTO v_total
    FROM amistad 
    WHERE id_usuario1 = p_id_usuario
		OR id_usuario2 = p_id_usuario;
    RETURN v_total;
END //

DELIMITER ;


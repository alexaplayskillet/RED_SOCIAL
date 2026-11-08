USE cosmix;

DROP PROCEDURE IF EXISTS login_usuario;
DROP PROCEDURE IF EXISTS registrar_usuario;

DELIMITER $$

CREATE PROCEDURE login_usuario (
    IN  tag_usuario VARCHAR(50),
    OUT id          INT,
    OUT pass        VARCHAR(255)
)
BEGIN
    SET id   = NULL;
    SET pass = NULL;

    SELECT id_usuario, contra
    INTO   id, pass
    FROM   usuario
    WHERE  tag = tag_usuario
      AND  estado = 1;
END$$


CREATE PROCEDURE registrar_usuario (
    IN  nom      VARCHAR(100),
    IN  tagg     VARCHAR(50),
    IN  fechanac DATE,
    IN  correoR  VARCHAR(100),
    IN  pass     VARCHAR(255),
    OUT p_id     INT,
    OUT p_codigo TINYINT
)
BEGIN
    DECLARE EXIT HANDLER FOR 1062
    BEGIN
        SET p_id     = NULL;
        SET p_codigo = 3;
    END;

    SET p_id     = NULL;
    SET p_codigo = 0;

    IF EXISTS (SELECT 1 FROM usuario WHERE tag = tagg) THEN
        SET p_codigo = 1;

    ELSEIF EXISTS (SELECT 1 FROM usuario WHERE correo = correoR) THEN
        SET p_codigo = 2;

    ELSE
        INSERT INTO usuario (nombre, tag, fecha_nac, correo, contra)
        VALUES (nom, tagg, fechanac, correoR, pass);

        SET p_id = LAST_INSERT_ID();
    END IF;
END$$

DELIMITER ;
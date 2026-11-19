drop procedure login_usuario;
DELIMITER //
/*PROCEDIMIENTO DE INICIAR SESION*/
CREATE PROCEDURE login_usuario(
			IN tag_usuario VARCHAR(50),
            OUT id INT,
            OUT pass VARCHAR(255)
            
)
BEGIN
		SET id = null; 
        SET pass = null;
        
        SELECT id_usuario, contra INTO id, pass
        FROM usuario
        WHERE tag = tag_usuario AND estado = 1;
END//

DELIMITER ;

DELIMITER //
/*PROCEDIMIENTO DE CREAR USUARIO*/
CREATE PROCEDURE registrar_usuario(
	IN nom varchar(100),
    IN tagg varchar(50),
	IN fechanac date,
    IN correoR varchar(100), 
    IN pass varchar(255),
    out p_id int,
    out p_codigo tinyint
)
BEGIN
	/*declaramos variables que me regresen el error*/
    DECLARE EXIT HANDLER FOR 1062
    BEGIN
		SET p_id = null;
        SET p_codigo = 3;
	END;
	SET p_id = null;
	SET p_codigo = 0;
    
    /*revisamos si existe el tag*/
	IF EXISTS(SELECT 1 FROM usuario WHERE tag = tagg) THEN
		SET p_codigo = 1;
	ELSEIF 	EXISTS(SELECT 1 FROM usuario WHERE correo = correoR) THEN
		SET p_codigo = 2;
	ELSE 
		INSERT INTO usuario (nombre, tag, fecha_nac, correo, contra)
		VALUES (nom, tagg, fechanac, correoR, pass);
        SET p_id = last_insert_id();
	END IF;
END //

DELIMITER ;


SELECT * FROM usuario;

CALL login_usuario('tag_que_no_existe', @id, @hash);
SELECT @id, @hash;
CALL login_usuario('alexa', @id, @hash);
SELECT @id, @hash;


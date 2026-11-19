/*CREACION DE VISTAS*/

/*generar publicaciones para el usuario*/
CREATE VIEW v_cargaPublicaciones AS 
SELECT u.id_usuario, u.nombre, u.tag, u.fotografia, 
p.id_pub, p.texto, p.multimedia, p.fecha_pub, 
/*CONTAR LIKES, COMPARTIDOS Y DEMAS*/
		(SELECT COUNT(*) FROM likes l       WHERE l.id_pubL  = p.id_pub AND l.estado  = 1) AS likes,
       (SELECT COUNT(*) FROM comentarios c WHERE c.id_pubC  = p.id_pub AND c.estado  = 1) AS comentarios,
       (SELECT COUNT(*) FROM compartidos s WHERE s.id_pubcc = p.id_pub AND s.estado  = 1) AS compartidos,
       (SELECT COUNT(*) FROM guardados g   WHERE g.id_pubG  = p.id_pub AND g.estado  = 1) AS guardados

FROM usuario AS u
INNER JOIN publicaciones AS p
ON u.id_usuario = p.id_usuarioP
WHERE u.estado = 1;


/*ver los comentarios*/
CREATE VIEW v_cargaComentarios AS
SELECT u.id_usuario, u.nombre, u.fotografia, 
c.id_come, c.id_pubC, c.texto, c.fechaC

FROM usuario AS u
INNER JOIN comentarios AS c 
ON u.id_usuario = c.id_usuarioC
WHERE c.estado = 1 AND u.estado = 1


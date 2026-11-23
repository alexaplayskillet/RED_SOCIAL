UPDATE usuario SET fotografia = load_file("C:/Users/Lenovo/Pictures/Screenshots/Captura de pantalla 2025-02-13 131615.png") WHERE id_usuario = 1;

SELECT * FROM usuario;

INSERT INTO publicaciones(id_usuarioP, texto, multimedia) VALUES (1, 'Mi primera publicacion', 
load_file("C:/Users/Lenovo/Pictures/Screenshots/Captura de pantalla 2025-10-09 131050.png"));

SELECT * FROM publicaciones;

UPDATE publicaciones set multimedia = 
load_file("C:/Users/Lenovo/Pictures/Screenshots/Captura de pantalla 2025-10-09 131050.png") wHERE id_pub = 1;

INSERT INTO publicaciones(id_usuarioP, texto) VALUES (1, 'Mi segunda publicacion');

SELECT * FROM likes;

select * from guardados;

select * from tableros;
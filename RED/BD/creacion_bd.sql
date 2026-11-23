CREATE DATABASE COSMIX;
USE COSMIX;

CREATE TABLE usuario(
	id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre varchar(100) NOT NULL,
    tag varchar(50) unique NOT NULL,
    fecha_nac DATE NOT NULL,
    correo VARCHAR(100) not null unique,
    contra VARCHAR(255) not null,
    estado TINYINT default 1 not null,
    fecha_creac DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fotografia BLOB
);
ALTER table usuario ADD portada mediumblob;
CREATE TABLE amistad (
    id_amistad INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario1 INT NOT NULL,
    id_usuario2 INT NOT NULL,
    estado TINYINT NOT NULL DEFAULT 1,  
    
    /*crea las llaves foraneas*/
    CONSTRAINT fk_amistad_usuario1
        FOREIGN KEY (id_usuario1) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_amistad_usuario2
        FOREIGN KEY (id_usuario2) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    /*checa que la amistad sea unica*/
    CONSTRAINT uq_amistad UNIQUE (id_usuario1, id_usuario2),
   /*checa las amistades en orden ascendente*/
   CONSTRAINT chk_orden CHECK (id_usuario1 < id_usuario2)
);

CREATE TABLE solicitudes(
	id_soli INT PRIMARY KEY AUTO_INCREMENT,
    usuario_envia INT NOT NULL,
    usuario_recibe INT NOT NULL,
    estado TINYINT NOT NULL DEFAULT 1, /*donde 1 es enviada, 2 aceptada, 3 rechazada y 4 cancelada*/
    fecha_envio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta DATETIME NULL,
    
    CONSTRAINT fk_soli_ue
		FOREIGN KEY (usuario_envia) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE,
	CONSTRAINT fk_soli_ur
		FOREIGN KEY (usuario_recibe) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE,
	/*checa que el usuario no se envie a si mismo*/
    CONSTRAINT chk_soli_distintos CHECK (usuario_envia <> usuario_recibe),
    /*checa que el estado de la solicitud sea valido*/
    CONSTRAINT chk_soli_estado CHECK (estado IN (1, 2, 3, 4))
);

CREATE TABLE tableros(
	id_tablero INT PRIMARY KEY AUTO_INCREMENT,
    id_usuarioT INT NOT NULL,
    descripcion varchar(255),
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado TINYINT DEFAULT 1/*donde 1 es activo y 0 eliminado*/,
    
    CONSTRAINT fk_usuario
		FOREIGN KEY (id_usuarioT) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE chats(
	id_chat INT PRIMARY KEY AUTO_INCREMENT,
	id_usuario1 INT NOT NULL,
	id_usuario2 INT NOT NULL,
    
    /*crea las llaves foraneas*/
    CONSTRAINT fk_chat_usuario1
        FOREIGN KEY (id_usuario1) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_chat_usuario2
        FOREIGN KEY (id_usuario2) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    /*checa que el chat sea unica*/
    CONSTRAINT uq_chat UNIQUE (id_usuario1, id_usuario2),
   /*checa los chats en orden ascendente*/
   CONSTRAINT chk_orden_chat CHECK (id_usuario1 < id_usuario2),
	/*checa que no sean el mismo usuario*/
   CONSTRAINT chk_chat_distintos CHECK (id_usuario1 <> id_usuario2)
   
);

CREATE TABLE mensaje(
	id_mensaje INT PRIMARY KEY AUTO_INCREMENT,
    id_chatM INT NOT NULL,
    usuario_envia INT NOT NULL,
    contenido VARCHAR(255),
    multimedia BLOB,
    fecha_envio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_chat
		FOREIGN KEY (id_chatM) REFERENCES chats(id_chat)
        ON DELETE RESTRICT ON UPDATE CASCADE,
	CONSTRAINT fk_ue
		FOREIGN KEY (usuario_envia) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE publicaciones(
	id_pub INT PRIMARY KEY AUTO_INCREMENT,
    id_usuarioP INT NOT NULL,
    texto VARCHAR(255),
    multimedia BLOB,
    /*likes int,
    comentarios int,
    compartidos int,
    guardados int,*/
    fecha_pub DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_usuarioP
		FOREIGN KEY (id_usuarioP) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE likes(
	id_like INT PRIMARY KEY AUTO_INCREMENT,
    id_pubL INT NOT NULL,
    id_usuarioL INT NOT NULL,
    fechaL DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    estado TINYINT DEFAULT 1 /*1 = activo 0 = eliminado*/,
    
	CONSTRAINT fk_publiL
    FOREIGN KEY (id_pubL) REFERENCES publicaciones(id_pub)
    ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_usuarioL
    FOREIGN KEY (id_usuarioL) REFERENCES usuario(id_usuario)
    ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT uq_like UNIQUE (id_pubL, id_usuarioL)
);

CREATE TABLE comentarios(
	id_come INT PRIMARY KEY AUTO_INCREMENT,
    id_pubC INT NOT NULL,
    id_usuarioC INT NOT NULL,
    texto VARCHAR(255) NOT NULL,
    fechaC DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    estado TINYINT DEFAULT 1 /*1 = activo 0 = eliminado*/,
    
	CONSTRAINT fk_publiC
    FOREIGN KEY (id_pubC) REFERENCES publicaciones(id_pub)
    ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_usuarioC
    FOREIGN KEY (id_usuarioC) REFERENCES usuario(id_usuario)
    ON DELETE RESTRICT ON UPDATE CASCADE
    
    
);

CREATE TABLE compartidos(
	id_comp INT PRIMARY KEY AUTO_INCREMENT,
    id_pubcc INT NOT NULL,
    id_usuariocc INT NOT NULL,
    fechacc DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    estado TINYINT DEFAULT 1 /*1 = activo 0 = eliminado*/,
    
	CONSTRAINT fk_publicc
    FOREIGN KEY (id_pubcc) REFERENCES publicaciones(id_pub)
    ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_usuariocc
    FOREIGN KEY (id_usuariocc) REFERENCES usuario(id_usuario)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE guardados(
	id_guar INT PRIMARY KEY AUTO_INCREMENT,
    id_pubG INT NOT NULL,
    id_tableroG INT NOT NULL,
    fechaG DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    estado TINYINT DEFAULT 1 /*1 = activo 0 = eliminado*/,
    
	CONSTRAINT fk_publiG
    FOREIGN KEY (id_pubG) REFERENCES publicaciones(id_pub)
    ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_tableroG
    FOREIGN KEY (id_tableroG) REFERENCES tableros(id_tablero)
    ON DELETE RESTRICT ON UPDATE CASCADE
    
);


ALTER TABLE publicaciones MODIFY multimedia MEDIUMBLOB;

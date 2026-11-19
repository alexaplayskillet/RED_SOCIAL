CREATE DATABASE IF NOT EXISTS cosmix
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_0900_ai_ci;

USE cosmix;

CREATE TABLE IF NOT EXISTS usuario (
    id_usuario  INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    tag         VARCHAR(50)  NOT NULL UNIQUE,
    fecha_nac   DATE         NOT NULL,
    correo      VARCHAR(100) NOT NULL UNIQUE,
    contra      VARCHAR(255) NOT NULL,
    estado      TINYINT      NOT NULL DEFAULT 1,
    fecha_creac DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fotografia  MEDIUMBLOB   NULL,
    portada     MEDIUMBLOB   NULL
);


CREATE TABLE IF NOT EXISTS amistad (
    id_amistad  INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario1 INT NOT NULL,
    id_usuario2 INT NOT NULL,
    estado      TINYINT NOT NULL DEFAULT 1,

    CONSTRAINT fk_amistad_usuario1
        FOREIGN KEY (id_usuario1) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT fk_amistad_usuario2
        FOREIGN KEY (id_usuario2) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT uq_amistad UNIQUE (id_usuario1, id_usuario2),
    CONSTRAINT chk_amistad_orden CHECK (id_usuario1 < id_usuario2)
);


CREATE TABLE IF NOT EXISTS solicitudes (
    id_soli         INT PRIMARY KEY AUTO_INCREMENT,
    usuario_envia   INT NOT NULL,
    usuario_recibe  INT NOT NULL,
    estado          TINYINT NOT NULL DEFAULT 1,
    fecha_envio     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta DATETIME NULL,

    CONSTRAINT fk_soli_ue
        FOREIGN KEY (usuario_envia) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT fk_soli_ur
        FOREIGN KEY (usuario_recibe) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT chk_soli_distintos CHECK (usuario_envia <> usuario_recibe),
    CONSTRAINT chk_soli_estado CHECK (estado IN (1, 2, 3, 4))
);


CREATE TABLE IF NOT EXISTS tableros (
    id_tablero     INT PRIMARY KEY AUTO_INCREMENT,
    id_usuarioT    INT NOT NULL,
    descripcion    VARCHAR(255),
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado         TINYINT DEFAULT 1,

    CONSTRAINT fk_usuario
        FOREIGN KEY (id_usuarioT) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE
);


CREATE TABLE IF NOT EXISTS chats (
    id_chat     INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario1 INT NOT NULL,
    id_usuario2 INT NOT NULL,

    CONSTRAINT fk_chat_usuario1
        FOREIGN KEY (id_usuario1) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT fk_chat_usuario2
        FOREIGN KEY (id_usuario2) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT uq_chat UNIQUE (id_usuario1, id_usuario2),
    CONSTRAINT chk_chat_orden CHECK (id_usuario1 < id_usuario2),
    CONSTRAINT chk_chat_distintos CHECK (id_usuario1 <> id_usuario2)
);



CREATE TABLE IF NOT EXISTS mensaje (
    id_mensaje    INT PRIMARY KEY AUTO_INCREMENT,
    id_chatM      INT NOT NULL,
    usuario_envia INT NOT NULL,
    contenido     VARCHAR(255),
    multimedia    MEDIUMBLOB,
    fecha_envio   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_chat
        FOREIGN KEY (id_chatM) REFERENCES chats(id_chat)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_ue
        FOREIGN KEY (usuario_envia) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE
);


CREATE TABLE IF NOT EXISTS publicaciones (
    id_pub      INT PRIMARY KEY AUTO_INCREMENT,
    id_usuarioP INT NOT NULL,
    texto       VARCHAR(255),
    multimedia  MEDIUMBLOB,
    likes       INT,
    comentarios INT,
    compartidos INT,
    guardados   INT,
    fecha_pub   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_usuarioP
        FOREIGN KEY (id_usuarioP) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE
);


CREATE TABLE IF NOT EXISTS likes (
    id_like     INT PRIMARY KEY AUTO_INCREMENT,
    id_pubL     INT NOT NULL,
    id_usuarioL INT NOT NULL,
    fechaL      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado      TINYINT DEFAULT 1,

    CONSTRAINT fk_publiL
        FOREIGN KEY (id_pubL) REFERENCES publicaciones(id_pub)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_usuarioL
        FOREIGN KEY (id_usuarioL) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS comentarios (
    id_come     INT PRIMARY KEY AUTO_INCREMENT,
    id_pubC     INT NOT NULL,
    id_usuarioC INT NOT NULL,
    texto       VARCHAR(255) NOT NULL,
    fechaC      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado      TINYINT DEFAULT 1,

    CONSTRAINT fk_publiC
        FOREIGN KEY (id_pubC) REFERENCES publicaciones(id_pub)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_usuarioC
        FOREIGN KEY (id_usuarioC) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS compartidos (
    id_comp      INT PRIMARY KEY AUTO_INCREMENT,
    id_pubcc     INT NOT NULL,
    id_usuariocc INT NOT NULL,
    fechacc      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado       TINYINT DEFAULT 1,

    CONSTRAINT fk_publicc
        FOREIGN KEY (id_pubcc) REFERENCES publicaciones(id_pub)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_usuariocc
        FOREIGN KEY (id_usuariocc) REFERENCES usuario(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE
);


CREATE TABLE IF NOT EXISTS guardados (
    id_guar     INT PRIMARY KEY AUTO_INCREMENT,
    id_pubG     INT NOT NULL,
    id_tableroG INT NOT NULL,
    fechaG      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado      TINYINT DEFAULT 1,

    CONSTRAINT fk_publiG
        FOREIGN KEY (id_pubG) REFERENCES publicaciones(id_pub)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_tableroG
        FOREIGN KEY (id_tableroG) REFERENCES tableros(id_tablero)
        ON DELETE RESTRICT ON UPDATE CASCADE
);


-- Datos de prueba
UPDATE cosmix.usuario SET nombre = 'Ariadna Rodriguez' WHERE id_usuario = 1;
UPDATE cosmix.usuario SET nombre = 'Arantza De La Vega' WHERE id_usuario = 2;

INSERT IGNORE INTO amistad (id_usuario1, id_usuario2, estado)
SELECT LEAST(a.id_usuario, b.id_usuario), GREATEST(a.id_usuario, b.id_usuario), 1
FROM usuario a
JOIN usuario b ON b.id_usuario <> a.id_usuario
WHERE a.tag = 'arisum';

SELECT * FROM publicaciones

create database georginaCelanitp; 
use georginaCelanitp;

create table estados(
id TINYINT(4),
estado VARCHAR(30),
PRIMARY KEY(id));

create table roles(
id TINYINT(4),
rol VARCHAR (30),
PRIMARY KEY (id));

create table usuarios(
idUsuario INT(11) NOT NULL AUTO_INCREMENT,
nombre VARCHAR(30),
apellido VARCHAR (30),
usuario VARCHAR(30),
pass VARCHAR(30),
rol TINYINT(4),
PRIMARY KEY (idUsuario),
FOREIGN KEY (rol) REFERENCES roles(id),
UNIQUE (usuario));

create table mensajes( 
idMensaje INT(11) NOT NULL AUTO_INCREMENT, 
de INT(11),
para INT(11),
asunto VARCHAR(30),
mensaje VARCHAR(500),
fecha TIMESTAMP ,
origen INT (11),
estado TINYINT (4),
PRIMARY KEY(idMensaje),
FOREIGN KEY(de) REFERENCES usuarios(idUsuario),
FOREIGN KEY(para) REFERENCES usuarios(idUsuario),
FOREIGN KEY(estado) REFERENCES estados(id));



INSERT INTO roles (id, rol)
VALUES (1, "Administrador"),(2,"Usuario");

INSERT INTO usuarios (idUsuario, nombre, apellido, usuario, pass, rol)
VALUES (123, "Georgina", "Celani", "GGCC", "testing", 1), 
(223, "Bertrand", "Russell", "BBRR", "philosophy", 2);
 
INSERT INTO estados(id, estado)
VALUES (0, "No Leído"),(1,"Leído"),(2, "Eliminado");

SELECT * FROM usuarios;
SELECT idUsuario FROM usuarios;

SELECT usuario from usuarios;
SHOW index from usuarios;


select * from mensajes;
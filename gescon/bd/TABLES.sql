-- DROP DATABASE IF EXISTS gescon;
-- CREATE DATABASE gescon;
-- USE gescon;

CREATE TABLE Roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) UNIQUE NOT NULL
);

-- Roles por defecto
-- 1 := Autor
-- 2 := Revisor
-- 3 := Jefe de Comité
INSERT INTO Roles (nombre)
VALUES
('autor'),
('revisor'),
('jefe de comite');

CREATE TABLE Usuarios (
    rut VARCHAR(12) PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    id_rol INT NOT NULL DEFAULT 1,
    FOREIGN KEY (id_rol) REFERENCES Roles(id)
);

CREATE TABLE Topicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE Articulos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    password VARCHAR(100) NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_editado DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    fecha_limite DATETIME,
    resumen VARCHAR(150) NOT NULL,
    rut_contacto VARCHAR(12) NOT NULL,
    FOREIGN KEY (rut_contacto) REFERENCES Usuarios(rut) ON DELETE CASCADE
);

CREATE TABLE Articulos_Topicos (
    id_articulo INT NOT NULL,
    id_topico INT NOT NULL,
    PRIMARY KEY (id_articulo, id_topico),
    FOREIGN KEY (id_articulo) REFERENCES Articulos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_topico) REFERENCES Topicos(id) ON DELETE CASCADE
);

CREATE TABLE Usuarios_Especialidad (
    rut_usuario VARCHAR(12) NOT NULL,
    id_topico INT NOT NULL,
    PRIMARY KEY (rut_usuario, id_topico),
    FOREIGN KEY (rut_usuario) REFERENCES Usuarios(rut) ON DELETE CASCADE,
    FOREIGN KEY (id_topico) REFERENCES Topicos(id) ON DELETE CASCADE
);

CREATE TABLE Articulos_Autores (
    id_articulo INT NOT NULL,
    rut_autor VARCHAR(12) NOT NULL,
    PRIMARY KEY (id_articulo, rut_autor),
    FOREIGN KEY (id_articulo) REFERENCES Articulos(id) ON DELETE CASCADE,
    FOREIGN KEY (rut_autor) REFERENCES Usuarios(rut) ON DELETE CASCADE
);

CREATE TABLE Articulos_Revisores (
    id_articulo INT NOT NULL,
    rut_revisor VARCHAR(12) NOT NULL,
    PRIMARY KEY (id_articulo, rut_revisor),
    FOREIGN KEY (id_articulo) REFERENCES Articulos(id) ON DELETE CASCADE,
    FOREIGN KEY (rut_revisor) REFERENCES Usuarios(rut) ON DELETE CASCADE
);

CREATE TABLE Formulario (
    id_formulario INT AUTO_INCREMENT PRIMARY KEY,
    id_articulo INT NOT NULL,
    rut_revisor VARCHAR(12) NOT NULL,
    calidad INT NOT NULL,
    originalidad INT NOT NULL,
    valoracion INT NOT NULL,
    argumentos_valoracion TEXT NOT NULL,
    comentarios TEXT,
    FOREIGN KEY (id_articulo) REFERENCES Articulos(id) ON DELETE CASCADE,
    FOREIGN KEY (rut_revisor) REFERENCES Usuarios(rut) ON DELETE CASCADE,
    CONSTRAINT verificar_calidad CHECK (calidad BETWEEN 1 AND 7),
    CONSTRAINT verificar_originalidad CHECK (originalidad BETWEEN 1 AND 7),
    CONSTRAINT verificar_valoracion CHECK (valoracion BETWEEN 1 AND 7),
    UNIQUE (id_articulo,rut_revisor)
);
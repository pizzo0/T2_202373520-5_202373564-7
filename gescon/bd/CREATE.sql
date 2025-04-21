CREATE TABLE Usuarios (
    rut VARCHAR(12) PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL
);

CREATE TABLE Topicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL
);

CREATE TABLE Articulos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    password VARCHAR(100) NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    fecha_envio DATE NOT NULL,
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
    FOREIGN KEY (rut_revisor) REFERENCES Usuarios(rut) ON DELETE CASCADE
);
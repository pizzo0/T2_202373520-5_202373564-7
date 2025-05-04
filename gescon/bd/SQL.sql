DROP DATABASE IF EXISTS gescon;
CREATE DATABASE gescon;
USE gescon;

CREATE TABLE Roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) UNIQUE NOT NULL
);

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

DELIMITER //
CREATE TRIGGER verificar_data
BEFORE INSERT ON Usuarios
FOR EACH ROW
BEGIN
    SET NEW.rut = TRIM(NEW.rut);
    SET NEW.nombre = REGEXP_REPLACE(TRIM(NEW.nombre), '\\s+', ' ');
    SET NEW.email = TRIM(NEW.email);
    SET NEW.password = TRIM(NEW.password);

    -- esta parte es por si acaso xd

    IF NEW.rut IS NULL OR NEW.rut = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El RUT no puede estar vacio.';
    END IF;
    IF NEW.nombre IS NULL OR NEW.nombre = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El nombre no puede estar vacio.';
    END IF;
    IF NEW.email IS NULL OR NEW.email = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El email no puede estar vacio.';
    END IF;
    IF NEW.password IS NULL OR NEW.password = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La contraseña no puede estar vacia.';
    END IF;

    -- aqui empiezan las verificaciones de verdad

    IF RIGHT(NEW.rut, 1) = 'k' THEN
        SET NEW.rut = CONCAT(LEFT(NEW.rut, LENGTH(NEW.rut) - 1), 'K');
    END IF;

    IF NOT NEW.rut REGEXP '^[1-9][0-9]?\\.[0-9]{3}\\.[0-9]{3}-[0-9K]$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Formato del RUT invalido (XX.XXX.XXX-X).';
    END IF;

    IF NOT NEW.email REGEXP '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Email invalido.';
    END IF;

    IF NEW.password REGEXP '\\s' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La contraseña no debe contener espacios.';
    END IF;

    IF CHAR_LENGTH(NEW.password) < 8 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Contraseña muy corta. Debe tener 8 caracteres o mas.';
    END IF;

    IF NOT NEW.password REGEXP '[A-Z]' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Contraseña debe tener al menos una letra mayuscula.';
    END IF;
    
    IF NOT NEW.password REGEXP '[a-z]' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Contraseña debe tener al menos una letra minuscula.';
    END IF;
    
    IF NOT NEW.password REGEXP '[0-9]' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Contraseña debe tener al menos un numero.';
    END IF;
    
    IF NOT NEW.password REGEXP '[^a-zA-Z0-9]' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Contraseña debe tener al menos un caracter especial.';
    END IF;
END;//
DELIMITER ;

DELIMITER //
CREATE TRIGGER verificar_update_data BEFORE UPDATE ON Usuarios
FOR EACH ROW
BEGIN
    -- no se puede cambiar el rut xd

    IF NEW.rut != OLD.rut THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El RUT no se puede modificar!';
    END IF;
    
    SET NEW.nombre = REGEXP_REPLACE(TRIM(NEW.nombre), '\\s+', ' ');
    SET NEW.email = TRIM(NEW.email);
    SET NEW.password = TRIM(NEW.password);

    -- por si acaso de nuevo :p

    IF NEW.nombre IS NULL OR NEW.nombre = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El nombre no puede estar vacio.';
    END IF;
    IF NEW.email IS NULL OR NEW.email = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El email no puede estar vacio.';
    END IF;
    IF NEW.password IS NULL OR NEW.password = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La contraseña no puede estar vacia.';
    END IF;

    -- verificaciones de verdad

    IF RIGHT(NEW.rut, 1) = 'k' THEN
        SET NEW.rut = CONCAT(LEFT(NEW.rut, LENGTH(NEW.rut) - 1), 'K');
    END IF;

    IF NOT NEW.rut REGEXP '^[1-9][0-9]?\\.[0-9]{3}\\.[0-9]{3}-[0-9K]$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Formato del RUT invalido (XX.XXX.XXX-X).';
    END IF;

    IF NOT NEW.email REGEXP '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Email invalido.';
    END IF;

    IF NEW.password REGEXP '\\s' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La contraseña no debe contener espacios.';
    END IF;

    IF CHAR_LENGTH(NEW.password) < 8 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Contraseña muy corta. Debe tener 8 caracteres o mas.';
    END IF;

    IF NOT NEW.password REGEXP '[A-Z]' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Contraseña debe tener al menos una letra mayuscula.';
    END IF;
    
    IF NOT NEW.password REGEXP '[a-z]' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Contraseña debe tener al menos una letra minuscula.';
    END IF;
    
    IF NOT NEW.password REGEXP '[0-9]' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Contraseña debe tener al menos un numero.';
    END IF;
    
    IF NOT NEW.password REGEXP '[^a-zA-Z0-9]' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Contraseña debe tener al menos un caracter especial.';
    END IF;
END;//
DELIMITER ;

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

DELIMITER //
CREATE TRIGGER establecer_fecha_limite BEFORE INSERT ON Articulos
FOR EACH ROW
BEGIN
    IF NEW.fecha_envio IS NULL THEN
        SET NEW.fecha_envio = NOW();
    END IF;

    SET NEW.fecha_limite = NEW.fecha_envio + INTERVAL 7 DAY;
END;//
DELIMITER ;

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

DELIMITER //
CREATE PROCEDURE asignar_revisor (
	IN p_id_articulo INT,
    IN p_rut_revisor VARCHAR(12)
)
BEGIN
	-- verificamos que sea revisor
	IF NOT EXISTS (
	SELECT 1 FROM Usuarios
    WHERE rut = p_rut_revisor AND id_rol = 2
    ) THEN
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Usuario no es Revisor.';
	END IF;
    
    -- verificamos que no sea autor del articulo
    IF EXISTS (
		SELECT 1 FROM Articulos_Autores
        WHERE id_articulo = p_id_articulo AND rut_autor = p_rut_revisor
    ) THEN
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El Autor no puede ser Revisor de su propio articulo.';
    END IF;
    
    -- verificamos que no sea ya revisor
    IF EXISTS (
		SELECT 1 FROM Articulos_Revisores
		WHERE id_articulo = p_id_articulo
        AND rut_revisor = p_rut_revisor
    ) THEN
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El Revisor ya fue asignado al articulo.';
    END IF;
    
    INSERT INTO Articulos_Revisores (id_articulo, rut_revisor)
    VALUES (p_id_articulo, p_rut_revisor);
END;//
DELIMITER ;

DELIMITER //
CREATE PROCEDURE asignar_revisor_random(
	IN p_id_articulo INT
)
BEGIN
    DECLARE v_rut_revisor VARCHAR(12);
    
    SELECT Usuarios.rut INTO v_rut_revisor FROM Usuarios
    JOIN Usuarios_Especialidad especialidad ON Usuarios.rut = especialidad.rut_usuario
    JOIN Articulos_Topicos topico ON especialidad.id_topico = topico.id_topico
    AND topico.id_articulo = p_id_articulo
    JOIN Roles ON Usuarios.id_rol = Roles.id
    WHERE Roles.nombre = 'revisor'
    AND Usuarios.rut NOT IN (
		SELECT rut_autor FROM Articulos_Autores
        WHERE id_articulo = p_id_articulo
    )
    AND Usuarios.rut NOT IN (
		SELECT rut_revisor FROM Articulos_Revisores
        WHERE id_articulo = p_id_articulo
    )
    ORDER BY RAND()
    LIMIT 1;
END;//
DELIMITER ;

DELIMITER //
CREATE PROCEDURE insertar_articulo (
    IN p_password VARCHAR(255),
    IN p_titulo VARCHAR(150),
    IN p_resumen VARCHAR(150),
    IN p_rut_contacto VARCHAR(12),
    IN p_autores TEXT,
    IN p_topicos TEXT
)
BEGIN
    DECLARE v_id_articulo INT;
    DECLARE v_rut_autor VARCHAR(12);
    DECLARE v_id_topico INT;
    DECLARE pos INT;
    DECLARE v_aux INT;
	DECLARE v_aux2 TEXT DEFAULT '';
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SET p_titulo = TRIM(p_titulo);
    SET p_resumen = TRIM(p_resumen);
    
    -- esta parte por si acaso xdd

    IF p_titulo = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Titulo no puede estar vacio.';
    END IF;
    IF p_resumen = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Resumen no puede estar vacio.';
    END IF;

    -- verificaciones de verdad :p

    -- al menos 1 autor
    IF LENGTH(TRIM(p_autores)) = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Debe haber al menos un autor.';
    END IF;

    -- al menos 1 topico
    IF LENGTH(TRIM(p_topicos)) = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Debe haber al menos un topico.';
    END IF;

    -- uno de los autores debe ser contacto
    SET pos = FIND_IN_SET(p_rut_contacto,p_autores);
    IF pos = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Rut de contacto no esta en la lista de autores!';
    END IF;
    
    -- inserta articulos
    INSERT INTO Articulos (password, titulo, resumen, rut_contacto)
    VALUES (p_password, p_titulo, p_resumen, p_rut_contacto);
    SET v_id_articulo = LAST_INSERT_ID();

    -- inserta autores
    WHILE LENGTH(p_autores) > 0 DO
        SET pos = LOCATE(',', p_autores);
        IF pos = 0 THEN
            SET v_rut_autor = TRIM(p_autores);
            SET p_autores = '';
        ELSE
            SET v_rut_autor = TRIM(SUBSTRING(p_autores, 1, pos - 1));
            SET p_autores = SUBSTRING(p_autores, pos + 1);
        END IF;

        -- se verifica que el titulo no este repetido para alguno de los autores.
        IF EXISTS (
            SELECT 1 FROM Articulos_Autores Autores
            JOIN Articulos ON Articulos.id = Autores.id_articulo
            WHERE Autores.rut_autor = v_rut_autor
            AND Articulos.titulo = p_titulo
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El titulo no puede estar repetido para alguno de los autores';
        END IF;

        -- evitamos autores duplicados
        IF FIND_IN_SET(v_rut_autor, v_aux2) THEN
			SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'No pueden haber autores repetidos.';
		END IF;
        SET v_aux2 := CONCAT(v_aux2, IF(v_aux2 = '', '', ','), v_rut_autor);

        INSERT INTO Articulos_Autores (id_articulo, rut_autor)
        VALUES (v_id_articulo, v_rut_autor);
    END WHILE;

    -- inserta topicos
    WHILE LENGTH(p_topicos) > 0 DO
        SET pos = LOCATE(',', p_topicos);
        IF pos = 0 THEN
            SET v_id_topico = CAST(TRIM(p_topicos) AS UNSIGNED);
            SET p_topicos = '';
        ELSE
            SET v_id_topico = CAST(TRIM(SUBSTRING(p_topicos, 1, pos - 1)) AS UNSIGNED);
            SET p_topicos = SUBSTRING(p_topicos, pos + 1);
        END IF;

        INSERT INTO Articulos_Topicos (id_articulo, id_topico)
        VALUES (v_id_articulo, v_id_topico);
    END WHILE;
    
    -- asignamos 3 revisores aleatorios
    -- notar que puede ocurrir que sean < 3
	CALL asignar_revisor_random (v_id_articulo);
	CALL asignar_revisor_random (v_id_articulo);
	CALL asignar_revisor_random (v_id_articulo);

    COMMIT;
END;//
DELIMITER ;

DELIMITER //
CREATE PROCEDURE actualizar_articulo (
	IN p_id_articulo INT,
    IN p_titulo VARCHAR(150),
    IN p_resumen VARCHAR(150),
    IN p_rut_contacto VARCHAR(12),
    IN p_autores TEXT
)
BEGIN
	DECLARE v_aux TEXT DEFAULT '';
	DECLARE v_rut_autor VARCHAR(12);
    DECLARE pos INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		ROLLBACK;
        RESIGNAL;
	END;
    
    START TRANSACTION;
    
    
    IF NOT EXISTS (
    SELECT 1 FROM Articulos
    WHERE id = p_id_articulo
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El articulo no existe.';
    END IF;

    SET p_titulo = TRIM(p_titulo);
    SET p_resumen = TRIM(p_resumen);
	
    -- esta parte por si acaso x2 xdd
    
    IF p_titulo = '' THEN
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El titulo no puede estar vacio.';
    END IF;
    IF p_resumen = '' THEN
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El titulo no puede estar vacio.';
    END IF;
    
    -- verificaciones de verdad :p

    -- al menos 1 autor
    IF LENGTH(TRIM(p_autores)) = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Debe haber al menos un autor.';
    END IF;

    -- uno de los autores debe ser contacto
    SET pos = FIND_IN_SET(p_rut_contacto,p_autores);
    IF pos = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Rut de contacto no esta en la lista de autores!';
    END IF;
    
	UPDATE Articulos
    SET titulo = p_titulo,
        resumen = p_resumen,
        rut_contacto = p_rut_contacto
    WHERE id = p_id_articulo;
    
    DELETE FROM Articulos_Autores
    WHERE id_articulo = p_id_articulo;
    
    SET pos = 0;
    WHILE LENGTH(p_autores) > 0 DO
		SET pos = LOCATE(',', p_autores);
        IF pos = 0 THEN
			SET v_rut_autor = TRIM(p_autores);
            SET p_autores = '';
		ELSE
			SET v_rut_autor = TRIM(SUBSTRING(p_autores, 1, pos - 1));
            SET p_autores = SUBSTRING(p_autores, pos + 1);
		END IF;
        
        -- evitamos autores duplicados
        IF FIND_IN_SET(v_rut_autor, v_aux) THEN
			SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'No pueden haber autores repetidos.';
		END IF;
        SET v_aux := CONCAT(v_aux, IF(v_aux = '', '', ','), v_rut_autor);

        -- se verifica que el titulo no este repetido para alguno de los autores.
        IF EXISTS (
            SELECT 1 FROM Articulos_Autores Autores
            JOIN Articulos ON Articulos.id = Autores.id_articulo
            WHERE Autores.rut_autor = v_rut_autor
            AND Articulos.titulo = p_titulo
            AND Articulos.id <> p_id_articulo
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El titulo no puede estar repetido para alguno de los autores.';
        END IF;

        INSERT INTO Articulos_Autores (id_articulo, rut_autor)
        VALUES (v_id_articulo, v_rut_autor);
    END WHILE;
    
    COMMIT;
END;//
DELIMITER ;

DELIMITER //
CREATE PROCEDURE obtener_revisores_por_especialidad(especialidad_id INT)
BEGIN
    SELECT Usuarios.rut, Usuarios.nombre, Usuarios.email
    FROM Usuarios
    JOIN Usuarios_Especialidad ON Usuarios.rut = Usuarios_Especialidad.rut_usuario
    JOIN Roles ON Usuarios.id_rol = Roles.id
    WHERE Roles.id = 2
    AND Usuarios_Especialidad.id_topico = especialidad_id;
END;//
DELIMITER ;
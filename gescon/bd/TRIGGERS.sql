DELIMITER //
CREATE TRIGGER verificar_insert_usuario BEFORE INSERT ON Usuarios
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
CREATE TRIGGER verificar_update_usuario BEFORE UPDATE ON Usuarios
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

DELIMITER //
CREATE TRIGGER verificar_insert_formulario BEFORE INSERT ON Formulario
FOR EACH ROW
BEGIN
    IF NEW.calidad < 1 OR NEW.calidad > 7 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La calidad debe ser un valor entre 1 y 7.';
    END IF;
    
    IF NEW.originalidad < 1 OR NEW.originalidad > 7 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La originalidad debe ser un valor entre 1 y 7.';
    END IF;
    
    IF NEW.valoracion < 1 OR NEW.valoracion > 7 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La valoracion debe ser un valor entre 1 y 7.';
    END IF;
    
    IF NOT EXISTS (
        SELECT 1 FROM Articulos_Revisores
        WHERE id_articulo = NEW.id_articulo
        AND rut_revisor = NEW.rut_revisor
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Revisor no asignado al articulo.';
    END IF;
END;//
DELIMITER ;

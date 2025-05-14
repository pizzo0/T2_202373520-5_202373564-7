DELIMITER //
CREATE PROCEDURE asignar_revisor (
	IN p_id_articulo INT,
    IN p_rut_revisor VARCHAR(12)
)
BEGIN
    -- verificamos si ya hay 3 revisores asignados
    IF (SELECT COUNT(*) FROM Articulos_Revisores WHERE id_articulo = p_id_articulo) >= 3 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El Articulo ya tiene 3 revisores asignados.';
    END IF;

	-- verificamos que sea revisor
	IF NOT EXISTS (
	SELECT 1 FROM Usuarios
    WHERE rut = p_rut_revisor
    AND id_rol >= 2
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
	IN p_id_articulo INT,
    OUT p_nombre_asignado VARCHAR(12)
)
BEGIN
    DECLARE v_rut_revisor VARCHAR(12);
    DECLARE v_nombre_revisor VARCHAR(255);
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_rut_revisor = NULL;

    IF (SELECT COUNT(*) FROM Articulos_Revisores WHERE id_articulo = p_id_articulo) >= 3 THEN
        SET p_nombre_asignado = NULL;
    ELSE
        SELECT Usuarios.rut, Usuarios.nombre INTO v_rut_revisor,v_nombre_revisor FROM Usuarios
        JOIN Usuarios_Especialidad especialidad ON Usuarios.rut = especialidad.rut_usuario
        JOIN Articulos_Topicos topico ON especialidad.id_topico = topico.id_topico
            AND topico.id_articulo = p_id_articulo
        JOIN Roles ON Usuarios.id_rol = Roles.id
        WHERE Roles.id >= 2
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

        IF v_rut_revisor IS NOT NULL THEN
            CALL asignar_revisor(p_id_articulo, v_rut_revisor);
            SET p_nombre_asignado = v_nombre_revisor;
        ELSE
            SET p_nombre_asignado = NULL;
        END IF;
    END IF;
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
            SET MESSAGE_TEXT = 'Rut de contacto no esta en los autores!';
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

        -- se verifica que el autor exista
        IF NOT EXISTS (
        SELECT 1 FROM Usuarios
        WHERE rut = v_rut_autor
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Uno de los autores no existe.';
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
        SET v_aux2 = CONCAT(v_aux2, IF(v_aux2 = '', '', ','), v_rut_autor);

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
	CALL asignar_revisor_random (v_id_articulo, @xd);
	CALL asignar_revisor_random (v_id_articulo, @xd);
	CALL asignar_revisor_random (v_id_articulo, @xd);

    SELECT LAST_INSERT_ID() AS id_articulo;
    
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
    DECLARE hubo_cambios INT DEFAULT 0;

    DECLARE old_titulo VARCHAR(150);
    DECLARE old_resumen VARCHAR(150);
    DECLARE old_rut_contacto VARCHAR(12);
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		ROLLBACK;
        RESIGNAL;
	END;
    
    START TRANSACTION;

    -- no sigue si no existe el articulo

    IF NOT EXISTS (
    SELECT 1 FROM Articulos
    WHERE id = p_id_articulo
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El articulo no existe.';
    END IF;

    -- revisamos si hay cambios

    SELECT titulo, resumen, rut_contacto INTO old_titulo, old_resumen, old_rut_contacto FROM Articulos
    WHERE id = p_id_articulo;

    IF p_titulo = '' OR p_titulo IS NULL THEN
        SET p_titulo = old_titulo;
    ELSE
        SET hubo_cambios = 1;
    END IF;

    IF p_resumen = '' OR p_resumen IS NULL THEN
        SET p_resumen = old_resumen;
    ELSE
        SET hubo_cambios = 1;
    END IF;

    SET p_titulo = TRIM(p_titulo);
    SET p_resumen = TRIM(p_resumen);
	
    -- esta parte por si acaso x2 xdd
    
    IF p_titulo = '' THEN
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Titulo no puede estar vacio.';
    END IF;
    IF p_resumen = '' THEN
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Resumen no puede estar vacio.';
    END IF;
    
    -- verificaciones de verdad :p
    
    IF hubo_cambios = 1 THEN
		UPDATE Articulos
		SET titulo = p_titulo,
			resumen = p_resumen
		WHERE id = p_id_articulo;
	END IF;

    -- verificamos cambios en autores y autor de contacto

    SET hubo_cambios = 0;

    SET p_rut_contacto = TRIM(p_rut_contacto);
    SET p_autores = TRIM(p_autores);

    IF p_rut_contacto = '' OR p_rut_contacto IS NULL THEN
        SET p_rut_contacto = old_rut_contacto;
    ELSE
        SET hubo_cambios = 1;
    END IF;
    
    IF p_autores <> '' THEN
        -- uno de los autores debe ser contacto
        SET pos = FIND_IN_SET(p_rut_contacto,p_autores);
        IF pos = 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Rut de contacto no esta en los autores!';
        END IF;

        DELETE FROM Articulos_Autores
        WHERE id_articulo = p_id_articulo;

        SET hubo_cambios = 1;
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

            -- se verifica que el autor exista
            IF NOT EXISTS (
			SELECT 1 FROM Usuarios
			WHERE rut = v_rut_autor
			) THEN
				SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Uno de los autores no existe.';
            END IF;

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
            
            -- evitamos autores duplicados
            IF FIND_IN_SET(v_rut_autor, v_aux) THEN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'No pueden haber autores repetidos.';
            END IF;
            SET v_aux = CONCAT(v_aux, IF(v_aux = '', '', ','), v_rut_autor);

            INSERT INTO Articulos_Autores (id_articulo, rut_autor)
            VALUES (p_id_articulo, v_rut_autor);
        END WHILE;
    ELSE 
        -- si no se cambian los autores, se verifica que sea uno de los autores que ya estaban definidos de antes
        IF NOT EXISTS (
            SELECT 1 FROM Articulos_Autores
            WHERE id_articulo = p_id_articulo
            AND rut_autor = p_rut_contacto
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Rut de contacto no esta en los autores!';
        END IF;

    END IF;
    
    -- hacemos la actualizacion al final por si no se actualiza el titulo o resumen, de forma que si se cambian autores y/o autor de conctacto, se actualice el atributo fecha_editado.

    IF hubo_cambios = 1 THEN
		UPDATE Articulos
		SET rut_contacto = p_rut_contacto
		WHERE id = p_id_articulo;
	END IF;
    
    COMMIT;
END;//
DELIMITER ;


DELIMITER //
CREATE PROCEDURE filtrar_articulos_data (
    IN p_id_articulo INT,
    IN p_contacto VARCHAR(255),
    IN p_autor VARCHAR(255),
    IN p_revisor VARCHAR(255),
    IN p_necesita_revisores TINYINT,
    IN p_id_topico INT,
    IN p_titulo VARCHAR(150),
    IN fecha_inicio DATETIME,
    IN fecha_fin DATETIME,
    IN p_orden VARCHAR(50),
    IN limite INT,
    IN offset_val INT,
    IN p_revisado TINYINT
)
BEGIN
    DECLARE orden_sql TEXT;

    SET orden_sql = CASE p_orden
        WHEN 'fecha_envio_asc' THEN 'fecha_envio ASC'
        WHEN 'fecha_envio_desc' THEN 'fecha_envio DESC'
        WHEN 'titulo_asc' THEN 'titulo ASC'
        WHEN 'titulo_desc' THEN 'titulo DESC'
        WHEN 'contacto_asc' THEN 'JSON_UNQUOTE(JSON_EXTRACT(contacto, "$.nombre")) ASC'
        WHEN 'contacto_desc' THEN 'JSON_UNQUOTE(JSON_EXTRACT(contacto, "$.nombre")) DESC'
        ELSE 'fecha_envio DESC'
    END;

    SET @filtros = CONCAT(
        ' FROM Articulos_Data WHERE 1=1 ',

        IF (p_id_articulo IS NULL OR p_id_articulo = '', '',
            CONCAT(' AND id_articulo = ', p_id_articulo)),

        IF(p_contacto IS NULL OR p_contacto = '', '', 
            CONCAT(' AND JSON_UNQUOTE(JSON_EXTRACT(contacto, "$.nombre")) COLLATE utf8mb4_general_ci LIKE ''%', p_contacto, '%''')),

        IF(p_autor IS NULL OR p_autor = '', '', 
            CONCAT(' AND EXISTS (SELECT 1 FROM JSON_TABLE(autores, "$[*]" COLUMNS (nombre VARCHAR(255) PATH "$.nombre")) AS autor WHERE autor.nombre COLLATE utf8mb4_general_ci LIKE ''%', p_autor, '%'' )')),

        IF(p_revisor IS NULL OR p_revisor = '', '', 
            CONCAT(' AND EXISTS (SELECT 1 FROM JSON_TABLE(revisores, "$[*]" COLUMNS (nombre VARCHAR(255) PATH "$.nombre")) AS revisor WHERE revisor.nombre COLLATE utf8mb4_general_ci LIKE ''%', p_revisor, '%'' )')),

        IF(p_id_topico IS NULL OR p_id_topico = 0, '', 
            CONCAT(' AND EXISTS (SELECT 1 FROM JSON_TABLE(topicos, "$[*]" COLUMNS (id INT PATH "$.id")) AS topico WHERE topico.id = ', p_id_topico, ')')),

        IF(p_titulo IS NULL OR p_titulo = '', '', 
            CONCAT(' AND titulo COLLATE utf8mb4_general_ci LIKE ''%', p_titulo, '%''')),

        IF(p_revisado IS NULL, '',
            CONCAT(' AND revisado = ', p_revisado)),

        IF(p_necesita_revisores IS NULL, '',
            CONCAT(' AND necesita_revisores = ', p_necesita_revisores)),

        ' AND fecha_envio BETWEEN ''', fecha_inicio, ''' AND ''', fecha_fin, ''' '
    );

    SET @sql_count = CONCAT('SELECT COUNT(*) AS total', @filtros);

    SET @sql = CONCAT(
        'SELECT *', @filtros,
        ' ORDER BY ', orden_sql,
        ' LIMIT ', limite,
        ' OFFSET ', offset_val
    );

    PREPARE stmt_count FROM @sql_count;
    EXECUTE stmt_count;
    DEALLOCATE PREPARE stmt_count;

    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END;//
DELIMITER ;
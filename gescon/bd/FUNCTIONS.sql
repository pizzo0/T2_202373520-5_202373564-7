DELIMITER //
CREATE FUNCTION obtener_revisores(
    p_rut_revisor TEXT,
    p_topicos TEXT,
    p_id_articulo TEXT
) RETURNS JSON
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE res JSON;

    -- si ya hay 3 revisores, no damos nada
    IF p_id_articulo IS NOT NULL AND p_id_articulo <> '' THEN
        IF (
            SELECT COUNT(*)
            FROM Articulos_Revisores
            WHERE id_articulo = CAST(p_id_articulo AS UNSIGNED)
        ) >= 3 THEN
            RETURN NULL;
        END IF;
    END IF;

    -- obtenemos posibles revisores
    IF p_topicos IS NULL OR p_topicos = '' THEN
        SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'rut', Usuarios.rut,
                'nombre', Usuarios.nombre,
                'email', Usuarios.email,
                'topicos', (
                    SELECT JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'id_topico', Especialidad.id_topico,
                            'nombre', Topicos.nombre
                        )
                    )
                    FROM Usuarios_Especialidad Especialidad
                    JOIN Topicos ON Topicos.id = Especialidad.id_topico
                    WHERE Especialidad.rut_usuario = Usuarios.rut
                ),
                'id_articulos', (
                    SELECT JSON_ARRAYAGG(Revisores.id_articulo)
                    FROM Articulos_Revisores Revisores
                    WHERE Revisores.rut_revisor = Usuarios.rut
                ),
                'id_articulos_posibles', (
                    SELECT JSON_ARRAYAGG(id_unico)
                    FROM (
                        SELECT Articulos.id AS id_unico
                        FROM Articulos
                        JOIN Articulos_Topicos ON Articulos.id = Articulos_Topicos.id_articulo
                        WHERE Articulos_Topicos.id_topico IN (
                            SELECT id_topico
                            FROM Usuarios_Especialidad
                            WHERE rut_usuario = Usuarios.rut
                        )
                        AND Articulos.id NOT IN (
                            SELECT id_articulo
                            FROM Articulos_Revisores
                            WHERE rut_revisor = Usuarios.rut
                        )
                        AND Articulos.id NOT IN (
                            SELECT id_articulo
                            FROM Articulos_Autores
                            WHERE rut_autor = Usuarios.rut
                        )
                        GROUP BY Articulos.id
                    ) AS posibles
                ),
                'id_rol', Usuarios.id_rol
            )
        )
        INTO res FROM Usuarios
        WHERE Usuarios.id_rol >= 2
        AND (
            p_id_articulo IS NULL OR p_id_articulo = '' OR (
                Usuarios.rut NOT IN (
                    SELECT rut_autor
                    FROM Articulos_Autores
                    WHERE id_articulo = CAST(p_id_articulo AS UNSIGNED)
                )
                AND Usuarios.rut NOT IN (
                    SELECT rut_revisor
                    FROM Articulos_Revisores
                    WHERE id_articulo = CAST(p_id_articulo AS UNSIGNED)
                )
            )
        )
        AND (
            p_rut_revisor IS NULL OR p_rut_revisor = '' OR 
            Usuarios.rut = p_rut_revisor
        );

        RETURN res;
    ELSE
        SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'rut', Usuarios.rut,
                'nombre', Usuarios.nombre,
                'email', Usuarios.email,
                'id_articulos', (
                    SELECT JSON_ARRAYAGG(Revisores.id_articulo)
                    FROM Articulos_Revisores Revisores
                    WHERE Revisores.rut_revisor = Usuarios.rut
                )
            )
        )
        INTO res
        FROM (
            SELECT DISTINCT Usuarios.rut, Usuarios.nombre, Usuarios.email
            FROM Usuarios
            JOIN Usuarios_Especialidad ON Usuarios.rut = Usuarios_Especialidad.rut_usuario
            WHERE Usuarios.id_rol >= 2
            AND FIND_IN_SET(Usuarios_Especialidad.id_topico, p_topicos) > 0
            AND (
                p_id_articulo IS NULL OR p_id_articulo = '' OR (
                    Usuarios.rut NOT IN (
                        SELECT rut_autor
                        FROM Articulos_Autores
                        WHERE id_articulo = CAST(p_id_articulo AS UNSIGNED)
                    )
                    AND Usuarios.rut NOT IN (
                        SELECT rut_revisor
                        FROM Articulos_Revisores
                        WHERE id_articulo = CAST(p_id_articulo AS UNSIGNED)
                    )
                )
            )
            AND (
                p_rut_revisor IS NULL OR p_rut_revisor = '' OR 
                Usuarios.rut = p_rut_revisor
            )
        ) Usuarios;

        RETURN res;
    END IF;
END;//
DELIMITER ;
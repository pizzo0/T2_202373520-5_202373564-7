DELIMITER //
CREATE FUNCTION obtener_revisores(
    p_rut_revisor TEXT,
    p_topicos TEXT,
    p_id_articulo TEXT,
    p_id_articulo_asignado TEXT,
    p_nombre TEXT,
    p_correo TEXT
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

    -- obtenemos posibles revisores, con o sin filtro por topicos
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
                    LEFT JOIN Articulos_Revisores ON Articulos_Revisores.id_articulo = Articulos.id
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
                    HAVING COUNT(DISTINCT Articulos_Revisores.rut_revisor) < 3
                ) AS posibles
            ),
            'id_rol', Usuarios.id_rol,
            'id_articulos_info', (
                SELECT JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'id_articulo', Articulos.id,
                        'en_revision', CASE
                            WHEN Articulos.fecha_limite < NOW() THEN 1
                            ELSE 0
                        END
                    )
                )
                FROM Articulos
                JOIN Articulos_Revisores ON Articulos.id = Articulos_Revisores.id_articulo
                WHERE Articulos_Revisores.rut_revisor = Usuarios.rut
            )
        )
    )
    INTO res
    FROM Usuarios
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
    )
    AND (
        p_nombre IS NULL OR p_nombre = '' OR 
        Usuarios.nombre COLLATE utf8mb4_0900_ai_ci LIKE CONCAT('%', p_nombre, '%') COLLATE utf8mb4_0900_ai_ci
    )
    AND (
        p_correo IS NULL OR p_correo = '' OR 
        Usuarios.email COLLATE utf8mb4_0900_ai_ci LIKE CONCAT('%', p_correo, '%') COLLATE utf8mb4_0900_ai_ci
    )
    AND (
        p_id_articulo_asignado IS NULL OR p_id_articulo_asignado = '' OR EXISTS (
            SELECT 1
            FROM Articulos_Revisores AR
            WHERE AR.id_articulo = CAST(p_id_articulo_asignado AS UNSIGNED)
            AND AR.rut_revisor = Usuarios.rut
        )
    )
    AND (
        p_topicos IS NULL OR p_topicos = '' OR EXISTS (
            SELECT 1
            FROM Usuarios_Especialidad
            WHERE Usuarios_Especialidad.rut_usuario = Usuarios.rut
            AND FIND_IN_SET(Usuarios_Especialidad.id_topico, p_topicos) > 0
        )
    );

    RETURN res;
END;//
DELIMITER ;
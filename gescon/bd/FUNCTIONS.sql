DELIMITER //
CREATE FUNCTION obtener_revisores(
    p_topicos TEXT
) RETURNS JSON
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE res JSON;

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
                )
            )
        )
        INTO res FROM Usuarios
        WHERE Usuarios.id_rol = 2;

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
            WHERE Usuarios.id_rol = 2
            AND FIND_IN_SET(Usuarios_Especialidad.id_topico, p_topicos) > 0
        ) Usuarios;

        RETURN res;
    END IF;
END;//
DELIMITER ;
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
                'id_topicos', (
                    SELECT JSON_ARRAYAGG(Especialidad.id_topico)
                    FROM Usuarios_Especialidad Especialidad
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
                'email', Usuarios.email
            )
        )
        INTO res FROM Usuarios
        JOIN Usuarios_Especialidad Especialidad ON Usuarios.rut = Especialidad.rut_usuario
        JOIN Roles on Usuarios.id_rol = Roles.id
        WHERE Roles.id = 2
        AND FIND_IN_SET(Especialidad.id_topico, p_topicos) > 0;

        RETURN res;
    END IF;
END;//
DELIMITER ;
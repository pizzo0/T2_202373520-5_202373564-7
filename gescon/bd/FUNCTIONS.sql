DELIMITER $$

CREATE FUNCTION ObtenerRevisores()
RETURNS TEXT
DETERMINISTIC
BEGIN
    DECLARE resultado LONGTEXT;

    SELECT
        JSON_ARRAYAGG(
            JSON_OBJECT(
                'rut', u.rut,
                'nombre', u.nombre,
                'email', u.email,
                'id_topicos', (
                    SELECT JSON_ARRAYAGG(ue.id_topico)
                    FROM Usuarios_Especialidad ue
                    WHERE ue.rut_usuario = u.rut
                ),
                'id_articulos_es_revisor', (
                    SELECT JSON_ARRAYAGG(ar.id_articulo)
                    FROM Articulos_Revisores ar
                    WHERE ar.rut_revisor = u.rut
                )
            )
        )
    INTO resultado
    FROM Usuarios u
    WHERE u.id_rol = 2;

    RETURN resultado;
END$$

DELIMITER ;
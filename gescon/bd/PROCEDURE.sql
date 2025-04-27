DELIMITER //

CREATE PROCEDURE obtenerArticulosPorAutor(IN rutAutor VARCHAR(12))
BEGIN
    SELECT 
        Articulos.id AS articulo_id,
        Articulos.titulo,
        Articulos.resumen,
        Articulos.fecha_envio,
        COALESCE(GROUP_CONCAT(DISTINCT Topicos.nombre SEPARATOR ', '), '') AS topicos,
        COALESCE(GROUP_CONCAT(DISTINCT Revisores.nombre SEPARATOR ', '), '') AS revisores
    FROM Articulos
    JOIN Articulos_Autores ON Articulos.id = Articulos_Autores.id_articulo
    LEFT JOIN Articulos_Topicos ON Articulos.id = Articulos_Topicos.id_articulo
    LEFT JOIN Topicos ON Articulos_Topicos.id_topico = Topicos.id
    LEFT JOIN Articulos_Revisores ON Articulos.id = Articulos_Revisores.id_articulo
    LEFT JOIN Usuarios AS Revisores ON Articulos_Revisores.rut_revisor = Revisores.rut
    WHERE Articulos_Autores.rut_autor = rutAutor
    GROUP BY Articulos.id, Articulos.titulo, Articulos.resumen, Articulos.fecha_envio
    ORDER BY Articulos.fecha_envio DESC;
END //

DELIMITER ;

--------------------------------------

DELIMITER $$

CREATE PROCEDURE revisores_por_especialidad(especialidad_id INT)
BEGIN
    SELECT Usuarios.rut, Usuarios.nombre, Usuarios.email
    FROM Usuarios
    JOIN Usuarios_Especialidad ON Usuarios.rut = Usuarios_Especialidad.rut_usuario
    JOIN Roles ON Usuarios.id_rol = Roles.id
    WHERE Roles.id = 2
    AND Usuarios_Especialidad.id_topico = especialidad_id;
END $$

DELIMITER ;
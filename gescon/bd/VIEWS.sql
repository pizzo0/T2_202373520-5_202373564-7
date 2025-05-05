-- view para obtener la data necesaria de los articulos mas facil

CREATE OR REPLACE VIEW Articulos_Data AS 
SELECT 
    Articulos.id AS articulo_id,
    Articulos.titulo,
    Articulos.resumen,
    Articulos.fecha_envio,
    Articulos.fecha_editado,
    JSON_OBJECT(
        'rut', Usuarios_contacto.rut,
        'nombre', Usuarios_contacto.nombre,
        'email', Usuarios_contacto.email
    ) AS contacto,
    COALESCE(
        JSON_ARRAYAGG(
            JSON_OBJECT(
                'rut', Usuarios.rut,
                'nombre', Usuarios.nombre,
                'email', Usuarios.email
            )
        ),
        JSON_ARRAY()
    ) AS autores,
    COALESCE(
        JSON_ARRAYAGG(
            JSON_OBJECT(
                'rut', Revisores.rut,
                'nombre', Revisores.nombre,
                'email', Revisores.email
            )
        ),
        JSON_ARRAY()
    ) AS revisores,

    COALESCE(
        JSON_ARRAYAGG(
            JSON_OBJECT(
				'id', Topicos.id,
                'nombre', Topicos.nombre
            )
        ),
        JSON_ARRAY()
    ) AS topicos
FROM Articulos
LEFT JOIN Articulos_Autores ON Articulos.id = Articulos_Autores.id_articulo
LEFT JOIN Usuarios ON Articulos_Autores.rut_autor = Usuarios.rut
LEFT JOIN Articulos_Topicos ON Articulos.id = Articulos_Topicos.id_articulo
LEFT JOIN Topicos ON Articulos_Topicos.id_topico = Topicos.id
LEFT JOIN Articulos_Revisores ON Articulos.id = Articulos_Revisores.id_articulo
LEFT JOIN Usuarios AS Revisores ON Articulos_Revisores.rut_revisor = Revisores.rut
LEFT JOIN Usuarios AS Usuarios_contacto ON Articulos.rut_contacto = Usuarios_contacto.rut
GROUP BY Articulos.id, Articulos.titulo, Articulos.resumen, Articulos.fecha_envio, Articulos.fecha_editado, Usuarios_contacto.rut, Usuarios_contacto.nombre, Usuarios_contacto.email;
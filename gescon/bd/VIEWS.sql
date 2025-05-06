CREATE OR REPLACE VIEW Articulos_Data AS 
SELECT 
    Articulos.id AS id_articulo,
    Articulos.titulo,
    Articulos.resumen,
    Articulos.fecha_envio,
    Articulos.fecha_editado,

    JSON_OBJECT(
        'rut', Usuarios_contacto.rut,
        'nombre', Usuarios_contacto.nombre,
        'email', Usuarios_contacto.email
    ) AS contacto,

    (
        SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'rut', Usuarios.rut,
                'nombre', Usuarios.nombre,
                'email', Usuarios.email
            )
        )
        FROM Articulos_Autores
        JOIN Usuarios ON Articulos_Autores.rut_autor = Usuarios.rut
        WHERE Articulos_Autores.id_articulo = Articulos.id
    ) AS autores,

    (
        SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'rut', Usuarios.rut,
                'nombre', Usuarios.nombre,
                'email', Usuarios.email
            )
        )
        FROM Articulos_Revisores
        JOIN Usuarios ON Articulos_Revisores.rut_revisor = Usuarios.rut
        WHERE Articulos_Revisores.id_articulo = Articulos.id
    ) AS revisores,

    (
        SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'id', Topicos.id,
                'nombre', Topicos.nombre
            )
        )
        FROM Articulos_Topicos
        JOIN Topicos ON Articulos_Topicos.id_topico = Topicos.id
        WHERE Articulos_Topicos.id_articulo = Articulos.id
    ) AS topicos

FROM Articulos
LEFT JOIN Usuarios AS Usuarios_contacto ON Articulos.rut_contacto = Usuarios_contacto.rut;
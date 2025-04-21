CREATE OR REPLACE VIEW obtenerArticulos AS
SELECT 
    Articulos.id AS articulo_id,
    Articulos.titulo,
    Articulos.resumen,
    Articulos.fecha_envio,
    COALESCE(GROUP_CONCAT(DISTINCT Usuarios.nombre SEPARATOR ', '), '') AS autores,
    COALESCE(GROUP_CONCAT(DISTINCT Topicos.nombre SEPARATOR ', '), '') AS topicos,
    COALESCE(GROUP_CONCAT(DISTINCT Revisores.nombre SEPARATOR ', '), '') AS revisores
FROM Articulos
LEFT JOIN Articulos_Autores ON Articulos.id = Articulos_Autores.id_articulo
LEFT JOIN Usuarios ON Articulos_Autores.rut_autor = Usuarios.rut
LEFT JOIN Articulos_Topicos ON Articulos.id = Articulos_Topicos.id_articulo
LEFT JOIN Topicos ON Articulos_Topicos.id_topico = Topicos.id
LEFT JOIN Articulos_Revisores ON Articulos.id = Articulos_Revisores.id_articulo
LEFT JOIN Usuarios AS Revisores ON Articulos_Revisores.rut_revisor = Revisores.rut
GROUP BY Articulos.id, Articulos.titulo, Articulos.fecha_envio;
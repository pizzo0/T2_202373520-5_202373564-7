<?php


require "../config/config.php";
require "../config/func.php";

$titulo = isset($_GET['titulo']) ? $_GET['titulo'] :'';
$contacto = isset($_GET['contacto']) ? $_GET['contacto'] :'';
$autor = isset($_GET['autor']) ? $_GET['autor'] :'';
$fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] :'1900-01-01';
$fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] :'2100-12-31';
$topico = isset($_GET['topicos']) ? $_GET['topicos'] :'';
$revisor = isset($_GET['revisor']) ? $_GET['revisor'] :'';

$fecha_desde = empty($fecha_desde) ? '1900-01-01' : $fecha_desde;
$fecha_hasta = empty($fecha_hasta) ? '2100-12-31' : $fecha_hasta;

$ordenar_por = isset($_GET['ordenar_por']) ? $_GET['ordenar_por'] : 'fecha_envio_desc'; // Valor por defecto
$opciones_orden = [
    'fecha_envio_asc' => 'fecha_envio ASC',
    'fecha_envio_desc' => 'fecha_envio DESC',
    'autor_asc' => 'contacto_nombre ASC',
    'autor_desc' => 'contacto_nombre DESC',
    'titulo_asc' => 'titulo ASC',
    'titulo_desc' => 'titulo DESC',
];
$ordenar_por_query = isset($opciones_orden[$ordenar_por]) ? $opciones_orden[$ordenar_por] : 'fecha_envio DESC';

$database = getDatabase();


$stmt = $database->prepare("
    SELECT * FROM obtenerArticulos
    WHERE
        (contacto_nombre LIKE ? OR contacto_nombre LIKE ?)
        AND (autores LIKE ? OR autores LIKE ?)
        AND revisores LIKE ?
        AND topicos LIKE ?
        AND titulo LIKE ?
        AND fecha_envio BETWEEN ? AND ?
    ORDER BY $ordenar_por_query
");

$stmt->execute([
    "%$contacto%",
    "%$contacto%",
    "%$autor%",
    "%$autor%",
    "%$revisor%",
    "%$topico%",
    "%$titulo%",
    $fecha_desde,
    $fecha_hasta
]);

$res = $stmt->get_result();

$data = [];

while ($fila = $res->fetch_assoc()) {
    $data[] = $fila;
}

echo json_encode([
    'total' => count($data),
    'data' => $data
    ], JSON_UNESCAPED_UNICODE
);
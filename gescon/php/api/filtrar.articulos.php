<?php


require "../config/config.php";
require "../config/func.php";

$titulo = isset($_GET['titulo']) ? $_GET['titulo'] :'';
$autor = isset($_GET['autor']) ? $_GET['autor'] :'';
$fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] :'1900-01-01';
$fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] :'2100-12-31';
$topico = isset($_GET['topicos']) ? $_GET['topicos'] :'';
$revisor = isset($_GET['revisor']) ? $_GET['revisor'] :'';

$fecha_desde = empty($fecha_desde) ? '1900-01-01' : $fecha_desde;
$fecha_hasta = empty($fecha_hasta) ? '2100-12-31' : $fecha_hasta;

$database = getDatabase();

$stmt = $database->prepare("
    SELECT * FROM obtenerArticulos
    WHERE
        autores LIKE ?
        AND revisores LIKE ?
        AND topicos LIKE ?
        AND titulo LIKE ?
        AND fecha_envio BETWEEN ? AND ?;
");

$stmt->execute([
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
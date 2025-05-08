<?php

require "../config/config.php";
require "../config/func.php";


header("Content-Type: application/json");

$id_articulo = $_GET['id_articulo'] ?? '';
$titulo = $_GET['titulo'] ?? '';
$contacto = $_GET['contacto'] ?? '';
$autor = $_GET['autor'] ?? '';
$fecha_desde = !empty($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '1900-01-01';
$fecha_hasta = !empty($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '2100-12-31';
$topico = $_GET['topicos'] ?? '';
$revisor = $_GET['revisor'] ?? '';
$ordenar_por = $_GET['ordenar_por'] ?? 'fecha_envio_desc';
$offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 20;

$database = getDatabase();

$stmt = $database->prepare("CALL filtrar_articulos_data(? ,?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    'isssissssii',
    $id_articulo,
    $contacto,
    $autor,
    $revisor,
    $topicoInt,
    $titulo,
    $fecha_desde,
    $fecha_hasta,
    $ordenar_por,
    $limit,
    $offset
);

$topicoInt = is_numeric($topico) ? (int) $topico : 0;

$stmt->execute();
$res = $stmt->get_result();

$data = [];

while ($fila = $res->fetch_assoc()) {
    foreach (['contacto', 'autores', 'revisores', 'topicos','formularios'] as $campo) {
        if (isset($fila[$campo])) {
            $decoded = json_decode($fila[$campo], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $fila[$campo] = $decoded;
            }
        }
    }
    $data[] = $fila;
}


while ($stmt->more_results() && $stmt->next_result()) { }

$stmt->close();

$total = count($data);

echo json_encode([
    'total' => $total,
    'data' => $data
], JSON_UNESCAPED_UNICODE);

$database->close();

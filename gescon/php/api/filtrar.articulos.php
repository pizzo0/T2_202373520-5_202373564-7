<?php

require "../config/config.php";
require "../config/func.php";

header("Content-Type: application/json");
date_default_timezone_set('America/Santiago');

$id_articulo = isset($_GET['id_articulo']) && $_GET['id_articulo'] !== '' ? (int)$_GET['id_articulo'] : null;
$titulo = $_GET['titulo'] ?? '';
$contacto = $_GET['contacto'] ?? '';
$autor = $_GET['autor'] ?? '';
$fecha_desde = !empty($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '1900-01-01 00:00:00';
$fecha_hasta = !empty($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : date('Y-m-d H:i:s');
$topico = isset($_GET['topicos']) ? (int)$_GET['topicos'] : 0;
$revisor = $_GET['revisor'] ?? '';
$necesita_revisores = (isset($_GET['necesita-revisores']) && (int)$_GET['necesita-revisores'] === 1) ? 1 : 0;
$ordenar_por = $_GET['ordenar_por'] ?? 'fecha_envio_desc';
$limit = 20;
$offset = (isset($_GET['offset']) ? (int)$_GET['offset'] : 0) * $limit;
$revisado = (isset($_GET['revisado']) && (int)$_GET['revisado'] === 1) ? 1 : 0;

$database = getDatabase();

$stmt = $database->prepare("CALL filtrar_articulos_data(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    'isssiissssiii',
    $id_articulo,
    $contacto,
    $autor,
    $revisor,
    $necesita_revisores,
    $topico,
    $titulo,
    $fecha_desde,
    $fecha_hasta,
    $ordenar_por,
    $limit,
    $offset,
    $revisado
);

if (!$stmt->execute()) {
    echo json_encode(['error' => $stmt->error]);
    exit;
}

$res_total = $stmt->get_result();
$total_row = $res_total->fetch_assoc();
$total = $total_row['total'] ?? 0;

$stmt->next_result();
$res_data = $stmt->get_result();

$data = [];

while ($fila = $res_data->fetch_assoc()) {
    foreach (['contacto', 'autores', 'revisores', 'topicos', 'formularios'] as $campo) {
        if (isset($fila[$campo])) {
            $decoded = json_decode($fila[$campo], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $fila[$campo] = $decoded;
            }
        }
    }
    $data[] = $fila;
}

echo json_encode([
    'total' => $total,
    'data' => $data
], JSON_UNESCAPED_UNICODE);

$stmt->close();
$database->close();
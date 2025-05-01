<?php


require "../config/config.php";
require "../config/func.php";

session_start();
$user = getUsuarioData();

$database = getDatabase();

$stmt = $database->prepare("
    CALL obtenerArticulosPorAutor(?)
");

$stmt->bind_param("s",$user['rut']);
$stmt->execute();
$res = $stmt->get_result();

$data = [];

while ($fila = $res->fetch_assoc()) {
    $data[] = $fila;
}

echo json_encode(['total' => count($data),
    'data' => $data,
    ], JSON_UNESCAPED_UNICODE
);

$stmt->close();
$database->close();
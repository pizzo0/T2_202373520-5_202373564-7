<?php


require "../config/config.php";
require "../config/func.php";

header("Content-Type: application/json");

session_start();
$user = getUsuarioData();

$database = getDatabase();

$stmt = $database->prepare("
    CALL articulos_por_autor(?)
");

$stmt->bind_param("s",$user['rut']);
$stmt->execute();
$res = $stmt->get_result();

$data = [];

while ($fila = $res->fetch_assoc()) {
    $fila['contacto'] = is_null($fila['contacto']) ? null : json_decode($fila['contacto'],true);
    $fila['autores'] = is_null($fila['autores']) ? null : json_decode($fila['autores'],true);
    $fila['revisores'] = is_null($fila['revisores']) ? null : json_decode($fila['revisores'],true);
    $fila['topicos'] = is_null($fila['topicos']) ? null : json_decode($fila['topicos'],true);
    $data[] = $fila;
}

echo json_encode(['total' => count($data),
    'data' => $data,
    ], JSON_UNESCAPED_UNICODE
);

$stmt->close();
$database->close();
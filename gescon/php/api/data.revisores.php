<?php

require "../config/config.php";
require "../config/func.php";

header("Content-Type: application/json");

$database = getDatabase();
$stmt = $database->stmt_init();

$params = [
    'rut_revisor' => $_GET['rut_revisor'] ?? '',
    'topicos' => $_GET['topicos'] ?? '',
    'id_articulo' => $_GET['id_articulo'] ?? '',
    'id_articulo_asignado' => $_GET['id_articulo_asignado'] ?? '',
    'nombre' => $_GET['nombre_revisor'] ?? '',
    'correo' => $_GET['correo_revisor'] ?? '',
];

if ($stmt->prepare("
SELECT obtener_revisores(?, ?, ?, ?, ?, ?) AS revisores
")) {
    $stmt->bind_param(
        "ssssss",
        $params['rut_revisor'],
        $params['topicos'],
        $params['id_articulo'],
        $params['id_articulo_asignado'],
        $params['nombre'],
        $params['correo']
    );

    if ($stmt->execute()) {
        $res = $stmt->get_result();
        if ($res && $row = $res->fetch_assoc()) {
            $revisores = $row['revisores'] ? json_decode($row['revisores'], true) : [];

            echo json_encode([
                'total' => count($revisores),
                'data' => $revisores,
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['total' => 0, 'data' => []], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(['error' => 'Error al ejecutar la consulta.']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Error al preparar la consulta.']);
}

$database->close();
<?php

require "../config/config.php";
require "../config/func.php";

header("Content-Type: application/json");

$database = getDatabase();
$stmt = $database->stmt_init();

$stmt->prepare("
    SELECT obtener_revisores('') AS revisores
");
$stmt->execute();

$res = $stmt->get_result();

if ($res && $row = $res->fetch_assoc()) {
    $revisores = json_decode($row['revisores'], true);

    echo json_encode([
        'total' => count($revisores),
        'data' => $revisores,
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['error' => 'No se pudo obtener los datos.']);
}

$stmt->close();
$database->close();
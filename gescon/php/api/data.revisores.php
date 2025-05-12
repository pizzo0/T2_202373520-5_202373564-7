<?php

require "../config/config.php";
require "../config/func.php";

header("Content-Type: application/json");

$database = getDatabase();
$stmt = $database->stmt_init();

$rut_revisor = isset($_GET['rut_revisor']) ? $_GET['rut_revisor'] : '' ;
$topicos = isset($_GET['topicos']) ? $_GET['topicos'] : '' ;
$id_articulo = isset($_GET['id_articulo']) ? $_GET['id_articulo'] : '' ;

$sql = "SELECT obtener_revisores('$rut_revisor','$topicos','$id_articulo') AS revisores";
$stmt->prepare($sql);
$stmt->execute();

$res = $stmt->get_result();

if ($res && $row = $res->fetch_assoc()) {
    if ($row['revisores']) {
        $revisores = json_decode($row['revisores'], true);

        echo json_encode([
            'total' => count($revisores),
            'data' => $revisores,
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'total' => 0,
            'data' => [],
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(['error' => 'No se pudo obtener los datos.']);
}

$stmt->close();
$database->close();
<?php


require "../config/config.php";
require "../config/func.php";

header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['id_articulo'])) {
    echo json_encode(['ok' => false, 'error' => 'ID NO ESPECIFICADA.']);
    exit;
}

$id = intval($input['id_articulo']);

$database = getDatabase();

$stmt = $database->prepare("
DELETE FROM Articulos WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();

if ($database->affected_rows > 0) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'error' => "NO SE PUDO ELIMINAR EL ARTICULO."]);
}

$stmt->close();
$database->close();
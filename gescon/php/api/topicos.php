<?php


require "../config/config.php";
require "../config/func.php";

header("Content-Type: application/json");

$database = getDatabase();

$id_topico = isset($_GET['id_topico']) ? trim($_GET['id_topico']) : null;
$nombre_topico = isset($_GET['nombre_topico']) ? trim($_GET['nombre_topico']) : null;

$query = "
SELECT * FROM Topicos WHERE 1=1
";
$params = [];
$tipos = "";

// Agregamos condiciones solo si los parámetros no son nulos o vacíos
if ($id_topico !== null && $id_topico !== "") {
    $query .= " AND id = ?";
    $params[] = $id_topico;
    $tipos .= "i";  // entero
}
if ($nombre_topico !== null && $nombre_topico !== "") {
    $query .= " AND nombre COLLATE utf8mb4_0900_ai_ci LIKE ?";
    $params[] = "%" . $nombre_topico . "%";
    $tipos .= "s";  // string
}

$stmt = $database->prepare($query);

if ($params) {
    $stmt->bind_param($tipos, ...$params);
}

$stmt->execute();
$res = $stmt->get_result();

$topicos = [];
while ($row = $res->fetch_assoc()) {
    $topicos[] = $row;
}

echo json_encode($topicos);

$stmt->close();
$database->close();
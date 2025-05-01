<?php


require "../config/config.php";
require "../config/func.php";

if (!isset($_GET["email"]) || !filter_var($_GET["email"], FILTER_VALIDATE_EMAIL)) {
    http_response_code(404);
    echo json_encode(["error" => "Email invalido"]);
    exit;
}

$email = $_GET["email"];

$database = getDatabase();

$stmt = $database->prepare("
SELECT rut, nombre, email FROM Usuarios
WHERE email = ?
");
$stmt->bind_param("s",$email);
$stmt->execute();

$data = $stmt->get_result()->fetch_assoc();

if ($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Usuario no existe"]);
}

$stmt->close();
$database->close();
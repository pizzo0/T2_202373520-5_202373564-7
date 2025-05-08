<?php


require "../config/config.php";
require "../config/func.php";


session_start();

header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);

if (isset($_GET['id_formulario'])) {
    $input['id_formulario'] = $_GET['id_formulario'];
    $input['email_revisor'] = $_GET['email_revisor'];
}

if (!isset($input['id_formulario'], $input['email_revisor'])) {
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos.']);
    exit;
}

$id_formulario = $input['id_formulario'];
$email_revisor = $input['email_revisor'];

$user = getUsuarioData();

if ($user['email'] != $email_revisor) {
    echo json_encode(['ok' => false, 'error' => 'No tienes permiso para eliminar este formulario.']);
    exit;
}

$database = getDatabase();

$stmt = $database->prepare("DELETE FROM Formulario WHERE id_formulario = ?");
$stmt->bind_param("i", $id_formulario);
$stmt->execute();

if ($database->affected_rows > 0) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'error' => 'No se pudo eliminar el formulario.']);
}

$stmt->close();
$database->close();

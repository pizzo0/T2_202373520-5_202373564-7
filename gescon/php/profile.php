<?php


$user = getUsuarioData();
if ($user === null) {
    $_SESSION["notificacion"] = [
        "tipo" => "alerta",
        "mensaje" => "Necesitas iniciar sesion para continuar"
    ];
    header("Location: /?page=login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $database = getDatabase();
    $user = $_SESSION['userid'];

    if (isset($_POST['nombre'])) {
        $nombre = trim($_POST['nombre']);

        if (empty($nombre)) {
            $_SESSION["notificacion"] = [
                "tipo" => "error",
                "mensaje" => "Tu nombre no puede estar vacío."
            ];
        } else {
            $stmt = $database->prepare("
            UPDATE Usuarios
            SET nombre = ?
            WHERE rut = ?
            ");
            $stmt->bind_param("ss", $nombre, $user);
            $stmt->execute();

            $_SESSION["notificacion"] = [
                "tipo" => "ok",
                "mensaje" => "Nombre modificado con éxito."
            ];
        }
    } elseif (isset($_POST['correo'])) {
        $correo = trim($_POST['correo']);

        if (empty($correo)) {
            $_SESSION["notificacion"] = [
                "tipo" => "error",
                "mensaje" => "Tu correo no puede estar vacío."
            ];
        } else {
            $stmt = $database->prepare("
            UPDATE Usuarios
            SET email = ?
            WHERE rut = ?
            ");
            $stmt->bind_param("ss", $correo, $user);
            $stmt->execute();

            $_SESSION["notificacion"] = [
                "tipo" => "ok",
                "mensaje" => "Correo modificado con éxito."
            ];
        }
    }
}
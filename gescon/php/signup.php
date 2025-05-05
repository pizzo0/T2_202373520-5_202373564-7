<?php


global $error;
$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (registrarUsuario($_POST)) {
        $_SESSION['mensaje_login'] = "Ya puedes iniciar sesión.";
        $_SESSION["notificacion"] = [
            "tipo" => "ok",
            "mensaje" => "Cuenta creada con exito."
        ];
        header("Location: /login");
        exit;
    } else {
        $_SESSION["notificacion"] = [
            "tipo" => "error",
            "mensaje" => $error
        ];
    }
}
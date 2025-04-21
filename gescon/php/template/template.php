<?php


session_start();
$user = getUsuarioData();

$page = getPagina();
if ($page != "index" && $page != "login" && $page != "buscar") {
    if ($user === null) {
        $_SESSION["notificacion"] = [
            "tipo" => "alerta",
            "mensaje" => "Necesitas iniciar sesion para continuar"
        ];
        header("Location: /?page=login");
        exit();
    }
}

getPhp();
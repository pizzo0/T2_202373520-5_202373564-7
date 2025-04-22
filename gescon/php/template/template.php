<?php


session_start();
$user = getUsuarioData();

$page = getPagina();
if ($page != "index" && $page != "login" && $page != "buscar" && $page != 'signup') {
    if ($user === null) {
        $_SESSION["notificacion"] = [
            "tipo" => "alerta",
            "mensaje" => "Necesitas iniciar sesion para continuar"
        ];
        header("Location: /?page=login");
        exit();
    }
}

if (($page == "login" || $page == "signup") && $user != null ) {
    $_SESSION["notificacion"] = [
        "tipo" => "error",
        "mensaje" => "No puedes acceder aqui."
    ];
    header("Location: /");
    exit();
}

getPhp();
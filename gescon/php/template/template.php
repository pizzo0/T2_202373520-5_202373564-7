<?php


session_start();
$user = getUsuarioData();

$page = getPagina();
if ($page != "index" && $page != "login" && $page != "buscar" && $page != 'signup' && $page != 'articulo') {
    if ($user === null) {
        $_SESSION["notificacion"] = [
            "tipo" => "alerta",
            "mensaje" => "Necesitas iniciar sesion para continuar"
        ];
        header("Location: /login");
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
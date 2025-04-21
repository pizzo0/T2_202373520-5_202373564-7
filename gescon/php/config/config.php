<?php


function config($key = "") {
    $res = [
        "name" => "Gescon",
        "site_url" => "",
        "pretty_uri" => false,
        "nav" => [
            "" => "Inicio",
            "login" => "Iniciar sesion",
            "buscar" => "Buscar",
            "publicar" => "Publicar",
            "gestion" => "Gestion",
            "asignar" => "Asignación",
            "signup" => "Registrar",
            "profile" => "Perfil",
        ],
        "template_path" => $_SERVER['DOCUMENT_ROOT'] . "/php/template",
        "content_path" => "./php/view.php",
        "php_path" => "./php",
        "styles_path" => "/css",
        "js_path" => "/js",
        "api_path" => $_SERVER['DOCUMENT_ROOT'] . "/php/api"
    ];

    return isset($res[$key]) ? $res[$key] : null;
}
<?php


function config($key = "") {
    $res = [
        "name" => "Gescon",
        "site_url" => "",
        "pretty_uri" => true,
        "nav" => [
            "" => "Inicio",
            "login" => "Iniciar sesion",
            "buscar" => "Buscar",
            "publicar" => "Publicar",
            "signup" => "Registrar",
            "perfil" => "Perfil",
        ],
        "template_path" => "./php/template",
        "content_path" => "./php/view.php",
        "php_path" => "./php",
        "styles_path" => "/css",
        "js_path" => "/js",
        "api_path" => "./php/api",
        "assets_path" => "./assets"
    ];

    return isset($res[$key]) ? $res[$key] : null;
}
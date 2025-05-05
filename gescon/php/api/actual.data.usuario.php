<?php

require "../config/config.php";
require "../config/func.php";

header("Content-Type: application/json");

session_start();
$user = getUsuarioData();

if ($user) {
    echo json_encode($user, JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Usuario no logeado"]);
}

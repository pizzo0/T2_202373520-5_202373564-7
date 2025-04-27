<?php


require "../config/config.php";
require "../config/func.php";

$database = getDatabase();
$stmt = $database->stmt_init();

$stmt->prepare("
        CALL revisores_por_especialidad(2)
");
$stmt->execute();

$res = $stmt->get_result()->fetch_assoc();

print_r($res);
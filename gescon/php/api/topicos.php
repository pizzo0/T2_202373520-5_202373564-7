<?php


require "../config/config.php";
require "../config/func.php";

$database = getDatabase();
$res = $database->query("
SELECT * FROM Topicos
");

if ($res->num_rows > 0) {
    $topicos = [];
    while ($row = $res->fetch_assoc()) {
        $topicos[] = $row;
    }
} else {
    $topicos = [];
}

echo json_encode( $topicos);

$database->close();
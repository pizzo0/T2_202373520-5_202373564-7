<?php


require "../config/config.php";
require "../config/func.php";

header("Content-Type: application/json");

session_start();
$user = getUsuarioData();
$database = getDatabase();

$stmt = $database->prepare("
    SELECT *
    FROM Articulos_Data,
    JSON_TABLE(
        autores, '$[*]'
        COLUMNS (
            rut VARCHAR(12) PATH '$.rut'
        )
    ) AS autor
    WHERE autor.rut = ?
    ORDER BY fecha_envio DESC
");

$stmt->bind_param("s",$user['rut']);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($articulo = $res->fetch_assoc()) {
    foreach(['contacto','autores','revisores','topicos','formularios'] as $aux) {
        $articulo[$aux] = $articulo[$aux] ? json_decode($articulo[$aux],true) : null;
    }
    $data[] = $articulo;
}


echo json_encode(['total' => count($data),
    'data' => $data,
    ], JSON_UNESCAPED_UNICODE
);

$stmt->close();
$database->close();
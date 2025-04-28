<?php

require "../config/config.php";
require "../config/func.php";

$database = getDatabase();
$stmt = $database->stmt_init();

$stmt->prepare("
    SELECT ObtenerRevisores() AS revisores
");
$stmt->execute();

$res = $stmt->get_result();

if ($res && $row = $res->fetch_assoc()) {
    $revisores = json_decode($row['revisores'], true);

    // Recorremos cada revisor para añadir nombres de especialidades
    foreach ($revisores as &$revisor) {
        if (isset($revisor['id_topicos']) && is_array($revisor['id_topicos'])) {
            $nombres_topicos = [];

            foreach ($revisor['id_topicos'] as $id_topico) {
                $nombres_topicos[] = getTopicoNombre($id_topico);
            }

            // Añadimos los nombres como un nuevo campo
            $revisor['nombres_topicos'] = $nombres_topicos;
        } else {
            // Por si no tiene id_topicos, dejamos nombres_topicos vacío
            $revisor['nombres_topicos'] = [];
        }
    }
    unset($revisor); // Siempre es buena práctica romper la referencia después del foreach

    echo json_encode([
        'total' => count($revisores),
        'data' => $revisores,
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['error' => 'No se pudo obtener los datos.']);
}
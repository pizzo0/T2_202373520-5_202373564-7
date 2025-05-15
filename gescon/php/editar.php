<?php

if (!isset($_GET['id_articulo'])) {
    header("location: /");
    exit;
}

if (isset($_POST['pass'])) {
    return;
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {

    $database = getDatabase();
    $stmt = $database->stmt_init();

    $database->begin_transaction();
    try {
        $id_articulo = (int)$_GET["id_articulo"];

        if (!isset($_POST["contacto"])) {
            throw new Exception("Falta el campo 'contacto' en el formulario.");
        }

        $email_contacto = $_POST["email"][$_POST["contacto"]];
        $contacto_data = getUsuarioDataEmail($email_contacto);
        if (!$contacto_data) {
            throw new Exception("Usuario de contacto no encontrado.");
        }

        $rut_contacto = $contacto_data["rut"];
        $titulo = $_POST["titulo"];
        $resumen = $_POST["resumen"];

        // Obtener todos los RUTs de los autores desde los correos
        $email_autores = $_POST["email"];
        $ruts_autores = [];
        foreach ($email_autores as $email) {
            $usuario = getUsuarioDataEmail($email);
            if (!$usuario) {
                throw new Exception("Autor no encontrado: $email");
            }
            $ruts_autores[] = $usuario["rut"];
        }
        $autores_str = implode(",", $ruts_autores);

        // Llamada al procedimiento almacenado
        $sql = "CALL actualizar_articulo(?, ?, ?, ?, ?)";
        $stmt->prepare($sql);
        $stmt->bind_param("issss", $id_articulo, $titulo, $resumen, $rut_contacto, $autores_str);
        $stmt->execute();

        $database->commit();

        $_SESSION["notificacion"] = [
            "tipo" => "ok",
            "mensaje" => "Artículo editado con éxito :)"
        ];

        header("location: /articulo/$id_articulo");
        exit;

    } catch (\Throwable $th) {
        $database->rollback();
        $_SESSION["notificacion"] = [
            "tipo" => "error",
            "mensaje" => "Ocurrió un error al editar el artículo :("
        ];
    }
}
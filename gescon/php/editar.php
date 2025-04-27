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
        if (!isset($_GET["id_articulo"])) {
            throw new Exception("Falta el ID del artículo.");
        }

        $id_articulo = (int)$_GET["id_articulo"];

        $sql = "
        UPDATE Articulos
        SET titulo = ?, resumen = ?, rut_contacto = ?, fecha_editado = NOW()
        WHERE id = ?
        ";
        $stmt->prepare($sql);

        if (!isset($_POST["contacto"])) {
            throw new Exception("Falta el campo 'contacto' en el formulario.");
        }

        $email_contacto = $_POST["email"][$_POST["contacto"]];
        $rut_contacto_data = getUsuarioDataEmail($email_contacto);
        if (!$rut_contacto_data) {
            throw new Exception("Usuario de contacto no encontrado.");
        }
        $rut_contacto = $rut_contacto_data["rut"];
        $titulo = $_POST["titulo"];
        $resumen = $_POST["resumen"];

        $stmt->bind_param("sssi", $titulo, $resumen, $rut_contacto,$id_articulo);
        $stmt->execute();

        // actualizamos los tópicos
        $sql = "
        DELETE FROM Articulos_Topicos
        WHERE id_articulo = ?
        ";
        $stmt->prepare($sql);
        $stmt->bind_param("i", $id_articulo);
        $stmt->execute();

        $topicos = array_filter(explode(",", $_POST["topicos"]));
        $sql = "
        INSERT INTO Articulos_Topicos (id_articulo, id_topico)
        VALUES (?,?)
        ";
        $stmt->prepare($sql);

        foreach ($topicos as $id_topico) {
            $id_topico = (int)$id_topico;
            $stmt->bind_param("ii", $id_articulo, $id_topico);
            $stmt->execute();
        }

        // actualizamos los autores
        $sql = "
        DELETE FROM Articulos_Autores
        WHERE id_articulo = ?
        ";
        $stmt->prepare($sql);
        $stmt->bind_param("i", $id_articulo);
        $stmt->execute();

        $sql = "
        INSERT INTO Articulos_Autores (id_articulo, rut_autor)
        VALUES (?,?)
        ";
        $stmt->prepare($sql);

        $email_autores = $_POST["email"];
        foreach ($email_autores as $email) {
            $usuario = getUsuarioDataEmail($email);
            if (!$usuario) {
                throw new Exception("Autor no encontrado: $email");
            }
            $rut_autor = $usuario["rut"];
            $stmt->bind_param("is", $id_articulo, $rut_autor);
            $stmt->execute();
        }

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

        echo $th;
    }
}

?>
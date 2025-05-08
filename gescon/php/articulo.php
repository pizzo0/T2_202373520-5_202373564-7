<?php


if (isset($_POST['calidad'])) {
    $user = getUsuarioData();

    $database = getDatabase();
    
    try {
        $stmt = $database->prepare("
        INSERT INTO Formulario (id_articulo,rut_revisor,calidad,originalidad,valoracion,argumentos_valoracion,comentarios)
        VALUES (?,?,?,?,?,?,?)
        ");

        $id_articulo = $_GET['id_articulo'];
        $rut_revisor = $user['rut'];
        $calidad = $_POST['calidad'];
        $originalidad = $_POST['originalidad'];
        $valoracion = $_POST['valoracion'];
        $argumentos = $_POST['argumentos'];
        $comentarios = $_POST['comentarios'];


        $stmt->bind_param("isiiiss",$id_articulo,$rut_revisor,$calidad,$originalidad,$valoracion,$argumentos,$comentarios);
        $stmt->execute();

        $_SESSION["notificacion"] = [
            "tipo" => "ok",
            "mensaje" => "Formulario enviado con exito :)"
        ];
    } catch (\Throwable $th) {

        $_SESSION["notificacion"] = [
            "tipo" => "error",
            "mensaje" => "Ocurrió un error al enviar el formulario :("
        ];
    }
}
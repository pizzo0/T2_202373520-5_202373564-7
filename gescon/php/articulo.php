<?php


if (isset($_POST['id_formulario'])) {
    $user = getUsuarioData();

    $database = getDatabase();
    try {        
        $stmt = $database->prepare("
            UPDATE Formulario 
            SET calidad = ?, originalidad = ?, valoracion = ?, argumentos_valoracion = ?, comentarios = ?
            WHERE id_formulario = ?
        ");

        $rut_revisor = $_POST['rut_revisor'];

        if ($user['rut'] != $rut_revisor) {
            $_SESSION["notificacion"] = [
                "tipo" => "error",
                "mensaje" => "No es tu formulario"
            ];
            throw new Exception("No es tu formulario.");
        }

        $id_formulario = $_POST['id_formulario'];
        $calidad = $_POST['calidad'];
        $originalidad = $_POST['originalidad'];
        $valoracion = $_POST['valoracion'];
        $argumentos = $_POST['argumentos'];
        $comentarios = $_POST['comentarios'];


        $stmt->bind_param("iiissi", $calidad, $originalidad, $valoracion, $argumentos, $comentarios, $id_formulario);
        $stmt->execute();

        $_SESSION["notificacion"] = [
            "tipo" => "ok",
            "mensaje" => "Formulario enviado con exito :)"
        ];
    } catch (Exception $e) {
        $_SESSION["notificacion"] = [
            "tipo" => "error",
            "mensaje" => "Ocurrió un error al enviar el formulario :(<br>" . $e->getMessage()
        ];
    }
} else if (isset($_POST['calidad'])) {
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
    } catch (Exception $e) {
        $_SESSION["notificacion"] = [
            "tipo" => "error",
            "mensaje" => "Ocurrió un error al enviar el formulario :(<br>" . $e->getMessage()
        ];
    }
}
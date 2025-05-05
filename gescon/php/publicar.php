<?php


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $database = getDatabase();
    $stmt = $database->stmt_init();
    
    $database->begin_transaction();
    try {
        $titulo = $_POST['titulo'];
        $resumen = $_POST['resumen'];
        $autores_email = $_POST['email'];
        $topicos = $_POST['topicos'];
        $password = generarPassword();

        $i_contacto = $_POST['contacto'];
        $rut_contacto = '';
        $rut_autores = '';
        foreach ($autores_email as $i => $email) {
            if ($i == $i_contacto) {
                $rut_contacto = getUsuarioDataEmail($email)['rut'];
            }
            $rut_autores .= getUsuarioDataEmail($email)['rut'] . ',';
        }
        $rut_autores = substr($rut_autores,0,-1);

        $stmt->prepare("
            CALL insertar_articulo(?,?,?,?,?,?)
        ");
        $stmt->bind_param("ssssss",$password ,$titulo, $resumen, $rut_contacto, $rut_autores, $topicos);

        if ($stmt->execute()) {
            $res = $stmt->get_result()->fetch_assoc();
            $id_articulo = $res['id_articulo'];
            
            $_SESSION["notificacion"] = [
                "tipo" => "ok",
                "mensaje" => "Articulo publicado con exito :)"
            ];
    
            header("location: /articulo/$id_articulo");
            exit;
        } else {
            $_SESSION["notificacion"] = [
                "tipo" => "error",
                "mensaje" => $stmt->error
            ];
        }

        $database->commit();

    } catch (\Throwable $th) {
        $database->rollback();
        $_SESSION["notificacion"] = [
            "tipo" => "error",
            "mensaje" => $stmt->error
        ];
    }
    $stmt->close();
    $database->close();
}
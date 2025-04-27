<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $database = getDatabase();
    $stmt = $database->stmt_init();
    
    $database->begin_transaction();
    try {
        $sql = "
        INSERT INTO Articulos (password,titulo,resumen,rut_contacto)
        VALUES (?,?,?,?)
        ";
        $stmt->prepare($sql);

        if (!isset($_POST["contacto"])) {
            throw new Exception("Falta el campo 'contacto' en el formulario.");
        }

        $pass = generarPassword();
        $email_contacto = $_POST["email"][$_POST["contacto"]];
        
        $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
        $titulo = $_POST["titulo"];
        $resumen = $_POST["resumen"];
        $rut_contacto = getUsuarioDataEmail($email_contacto)["rut"];

        $stmt->bind_param("ssss",$pass_hash,$titulo,$resumen,$rut_contacto);

        $stmt->execute();

        $id_articulo = $stmt->insert_id;

        $topicos = explode(",", $_POST["topicos"]);
        $sql = "
        INSERT INTO Articulos_Topicos (id_articulo,id_topico)
        VALUES (?,?)
        ";
        $stmt->prepare($sql);
        
        foreach ($topicos as $id_topico) {
            $stmt->bind_param("ss", $id_articulo, $id_topico);
            $stmt->execute();
        }

        $sql = "
        INSERT INTO Articulos_Autores (id_articulo,rut_autor)
        VALUES (?,?)
        ";
        $stmt->prepare($sql);

        $email_autores = $_POST["email"];
        foreach ($email_autores as $email) {
            $rut_autor = getUsuarioDataEmail($email)["rut"];
            $stmt->bind_param("ss", $id_articulo, $rut_autor);
            $stmt->execute();
        }

        // $rut_revisores = [];
        // while (count($rut_revisores) < 3) {
            
        // }

        $database->commit();

        $_SESSION["notificacion"] = [
            "tipo" => "ok",
            "mensaje" => "Articulo publicado con exito :)"
        ];

        header("location: /articulo/$id_articulo");
        exit;

    } catch (\Throwable $th) {
        $database->rollback();
        $_SESSION["notificacion"] = [
            "tipo" => "error",
            "mensaje" => "Ocurrio un error al publicar el articulo :("
        ];
    }
}
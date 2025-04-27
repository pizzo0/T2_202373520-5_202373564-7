<?php


global $error;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $database = getDatabase();
    $sql = sprintf("
        SELECT * FROM Usuarios
        WHERE email = '%s'
        ",
        $database->real_escape_string($_POST["correo"])
    );
    
    $res = $database->query($sql);
    $user = $res->fetch_assoc();

    if ($user && password_verify($_POST['pass'], $user["password"])) {
        $_SESSION["userid"] = $user["rut"];

        $_SESSION["notificacion"] = [
            "tipo" => "ok",
            "mensaje" => "Iniciaste sesion exitosamente."
        ];
        
        header("Location: /");
        exit;
    }
    
    if (!isset($error)) {
        $_SESSION["notificacion"] = [
            "tipo" => "error",
            "mensaje" => "Hubo un error al iniciar sesion."
        ];
        $error = "Correo o contraseña incorrectos.";
    }
}
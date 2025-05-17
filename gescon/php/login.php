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

    // no usaremos hash xd
    // if ($user && password_verify($_POST['pass'], $user["password"])) {
    if ($user && $user['password'] == $_POST['pass']) {
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
            "mensaje" => "Hubo un error al iniciar sesion.<br>Correo o contraseña incorrectos."
        ];
        $error = "Correo o contraseña incorrectos.";
    }
}
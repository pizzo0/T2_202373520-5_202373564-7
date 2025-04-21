<?php

global $error;
$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_POST["rut"])) {
        $error = "Rut vacío.";
    } elseif (empty($_POST["nombre"])) {
        $error = "Nombre vacío.";
    } elseif (!filter_var($_POST["correo"], FILTER_VALIDATE_EMAIL)) {
        $error = "Correo no válido.";
    } elseif ($_POST["pass"] !== $_POST["pass_confirm"]) {
        $error = "Las contraseñas deben coincidir.";
    } else {
        $pass_hash = password_hash($_POST["pass"], PASSWORD_DEFAULT);
        $database = getDatabase();

        $sql = "
        INSERT INTO Usuarios (rut,nombre,email,password)
        VALUES (?,?,?,?)
        ";
        $stmt = $database->stmt_init();

        if ($stmt->prepare($sql)) {
            $stmt->bind_param("ssss", $_POST["rut"], $_POST["nombre"], $_POST["correo"], $pass_hash);

            try {
                $stmt->execute();
                $_SESSION['mensaje_login'] = "Ya puedes iniciar sesión.";
                $_SESSION["notificacion"] = [
                    "tipo" => "ok",
                    "mensaje" => "Cuenta creada con exito."
                ];

                header("Location: ?page=login");
                exit;
            } catch (mysqli_sql_exception $e) {
                if (str_contains($e->getMessage(), 'PRIMARY')) {
                    $error = "El Rut ingresado ya está registrado.";
                } elseif (str_contains($e->getMessage(), 'email')) {
                    $error = "El correo ingresado ya está registrado.";
                } else {
                    $error = "Error al registrar: " . $e->getMessage();
                }
            }
        } else {
            $error = "Error en la preparación de la consulta.";
        }
    }
}
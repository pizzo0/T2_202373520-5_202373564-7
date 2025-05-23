<?php


$user = getUsuarioData();
if ($user === null) {
    $_SESSION["notificacion"] = [
        "tipo" => "alerta",
        "mensaje" => "Necesitas iniciar sesion para continuar"
    ];
    header("Location: /login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $database = getDatabase();

    $rut = $user['rut'];

    try {
        if (isset($_POST['nombre'])) {
            $nombre = trim($_POST['nombre']);
    
            if (empty($nombre)) {
                $_SESSION["notificacion"] = [
                    "tipo" => "error",
                    "mensaje" => "Tu nombre no puede estar vacío."
                ];
                return;
            } elseif ($user['nombre'] === $_POST['nombre']) {
                // nadaxd
            } else {
                $stmt = $database->prepare("
                UPDATE Usuarios
                SET nombre = ?
                WHERE rut = ?
                ");
                $stmt->bind_param("ss", $nombre, $rut);
                $stmt->execute();
        
                $_SESSION["notificacion"] = [
                    "tipo" => "ok",
                    "mensaje" => "Nombre guardado con éxito."
                ];
            }
        }
        
        if (isset($_POST['correo'])) {
            $correo = trim($_POST['correo']);
    
            if (empty($correo)) {
                $_SESSION["notificacion"] = [
                    "tipo" => "error",
                    "mensaje" => "Tu correo no puede estar vacío."
                ];
                return;
            } elseif ($user['email'] === $_POST['correo']) {
                // nadaxd
            } else {
                $stmt = $database->prepare("
                UPDATE Usuarios
                SET email = ?
                WHERE rut = ?
                ");
                $stmt->bind_param("ss", $correo, $rut);
                $stmt->execute();
        
                if ($user['nombre'] != $_POST['nombre']) {
                    $_SESSION["notificacion"] = [
                        "tipo" => "ok",
                        "mensaje" => "Cambios guardados con éxito."
                    ];
                } else {
                    $_SESSION["notificacion"] = [
                        "tipo" => "ok",
                        "mensaje" => "Correo guardado con éxito."
                    ];
                }
            }
        }
        
        if (isset($_POST['pass'])) {
            $sql = sprintf("
                SELECT * FROM Usuarios
                WHERE rut = '%s'
                ",
                $database->real_escape_string($rut)
            );
            
            $res = $database->query($sql);
            $user = $res->fetch_assoc();
            
            $old_pass = $_POST['old_pass'];
            $new_pass = $_POST['pass'];
            $confirm_new_pass = $_POST['pass_confirm'];

            if (!($old_pass === $user['password'])) {
                $_SESSION["notificacion"] = [
                    "tipo" => "error",
                    "mensaje" => "Tu contraseña anterior es incorrecta."
                ];
                return;
            }
            
            if ($new_pass !== $confirm_new_pass) {
                $_SESSION["notificacion"] = [
                    "tipo" => "error",
                    "mensaje" => "Las contraseñas deben coincidir."
                ];
                return;
            }
    
            if ($new_pass === $user['password']) {
                $_SESSION["notificacion"] = [
                    "tipo" => "alerta",
                    "mensaje" => "Contraseña nueva igual a la anterior."
                ];
                return;
            }
    
            try {
                $stmt = $database->prepare("
                UPDATE Usuarios
                SET password = ?
                WHERE rut = ?
                ");
    
                $stmt->bind_param("ss", $new_pass, $rut);
                $stmt->execute();
    
                $_SESSION["notificacion"] = [
                    "tipo" => "ok",
                    "mensaje" => "Contraseña guardada con exito."
                ];
            } catch (\Throwable $th) {
                $_SESSION["notificacion"] = [
                    "tipo" => "error",
                    "mensaje" => "Ocurrio un error al intentar modificar tu contraseña, intentalo de nuevo..."
                ];
            }
    
        }
    
        if (isset($_POST['confirmar-eliminar'])) {
            if ($user['id_rol'] == 3) {
                throw new Exception("Como jefe de comite, no puedes eliminar tu cuenta.");
            }

            $confirmar_eliminar = $_POST['confirmar-eliminar'];
            try {
                $stmt = $database->prepare("
                    SELECT password FROM Usuarios
                    WHERE rut = ?
                ");

                $stmt->bind_param("s", $user['rut']);
                $stmt->execute();
                
                $pass_user = $stmt->get_result()->fetch_assoc()['password'];

                if ($pass_user === $confirmar_eliminar) {
                    eliminarUsuario($user['rut']);
                    
                    $_SESSION["notificacion"] = [
                        "tipo" => "ok",
                        "mensaje" => "Cuenta eliminada con exito."
                    ];
                    
                    header("Location: /");
                    exit();
                } else {
                    $_SESSION["notificacion"] = [
                        "tipo" => "error",
                        "mensaje" => "Contraseña incorrecta."
                    ];
                }
            } catch (Exception $e) {
                $_SESSION["notificacion"] = [
                    "tipo" => "error",
                    "mensaje" => "Ocurrio un error inesperado. Intentalo de nuevo..."
                ];
            }
        }
    } catch (Exception $e) {
        $error = isset($stmt->error) ? $stmt->error : $e->getMessage();
        $_SESSION["notificacion"] = [
            "tipo" => "error",
            "mensaje" => $error
        ];
        
    }
}
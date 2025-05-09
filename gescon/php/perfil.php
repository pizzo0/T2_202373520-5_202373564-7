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
    
            if (!password_verify($old_pass, $user['password'])) {
                $_SESSION["notificacion"] = [
                    "tipo" => "error",
                    "mensaje" => "Tu contraseña anterior es incorrecta."
                ];
                return;
            }
    
            if ($new_pass !== $confirm_new_pass) {
                $_SESSION["notificacion"] = [
                    "tipo" => "alerta",
                    "mensaje" => "Las contraseñas deben coincidir."
                ];
                return;
            }
    
            if (password_verify($new_pass, $user['password'])) {
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
    
                $pass_hash = password_hash($new_pass, PASSWORD_DEFAULT);
    
                $stmt->bind_param("ss", $pass_hash, $rut);
                $stmt->execute();
    
                $_SESSION["notificacion"] = [
                    "tipo" => "ok",
                    "mensaje" => "Contraseña guardada con éxito."
                ];
            } catch (\Throwable $th) {
                $_SESSION["notificacion"] = [
                    "tipo" => "error",
                    "mensaje" => "Ocurrio un error al intentar modificar tu contraseña, intentalo de nuevo.."
                ];
            }
    
        }
    
        if (isset($_POST['eliminar'])) {
            if ($_POST['eliminar'] == 1) {
                eliminarUsuario($rut);
        
                $_SESSION["notificacion"] = [
                    "tipo" => "ok",
                    "mensaje" => "Tu cuenta fue eliminada con exito."
                ];
        
                header("Location: /");
                exit();
            }
        }
    } catch (\Throwable $th) {
        $_SESSION["notificacion"] = [
            "tipo" => "error",
            "mensaje" => $stmt->error
        ];
        
    }
}
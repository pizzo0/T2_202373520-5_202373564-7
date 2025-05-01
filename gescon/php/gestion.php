<?php


$user = getUsuarioData();
$database = getDatabase();

if ($user['id_rol'] === 3 && $_SERVER["REQUEST_METHOD"] === "POST") {

    if (registrarUsuario($_POST)) {
        $rut_revisor = $_POST['rut'];

        $res = $database->query("
        UPDATE Usuarios
        SET id_rol = 2
        WHERE rut = '$rut_revisor'
        ");

        $nombre = $_POST['nombre'];
        
        $_SESSION["notificacion"] = [
            "tipo" => "ok",
            "mensaje" => "Revisor ($nombre) creado con exito."
        ];

    } elseif (isset($_POST['topico'])) {
            $topico = $_POST['topico'];

            try {
                if(empty($topico)) {
                    $_SESSION["notificacion"] = [
                        "tipo" => "alert",
                        "mensaje" => "El nombre del topico no puede estar vacio."
                    ];
                    return;
                }

                $res = $database->query("
                    INSERT INTO Topicos (nombre)
                    VALUE ('$topico')
                ");

                $_SESSION["notificacion"] = [
                    "tipo" => "ok",
                    "mensaje" => "Topico ($topico) creado con exito."
                ];
            } catch(Exception $e) {
                $_SESSION["notificacion"] = [
                    "tipo" => "error",
                    "mensaje" => "Ocurrio un error al crear el topico ($topico). Puede que este ya exista."
                ];
            }

    } elseif (isset($_POST['tipo'])) {
        if ($_POST['tipo'] === "eliminar") {
            eliminarUsuario($_POST['rut_revisor']);
            
            $_SESSION["notificacion"] = [
                "tipo" => "ok",
                "mensaje" => "Revisor eliminado con exito."
            ];
        }
    } elseif (isset($_POST['rut_modificar'])) {
        try {
            $rut_modificar = $_POST['rut_modificar'];
            $nombre_modificar = $_POST['nombre_modificar'];
            $correo_modificar = $_POST['correo_modificar'];

            $stmt = $database->stmt_init();
            $database->begin_transaction();
            
            $stmt = $database->prepare("
            UPDATE Usuarios
            SET nombre = ?, email = ?
            WHERE rut = ?
            ");
            $stmt->bind_param("sss", $nombre_modificar, $correo_modificar, $rut_modificar);
            $stmt->execute();
    
            $stmt = $database->prepare("
            DELETE FROM Usuarios_Especialidad
            WHERE rut_usuario = ?
            ");
            $stmt->bind_param("s",$rut_modificar);
            $stmt->execute();
    
    
            $topicos = explode(",", $_POST["topicos"]);
            $stmt = $database->prepare("
            INSERT INTO Usuarios_Especialidad (rut_usuario,id_topico)
            VALUES (?,?)
            ");
            foreach($topicos as $especialidad) {
                $especialidad = (int)$especialidad;
                $stmt->bind_param("si",$rut_modificar,$especialidad);
                $stmt->execute();
            }

            $database->commit();

            $_SESSION["notificacion"] = [
                "tipo" => "ok",
                "mensaje" => "Revisor modificado con exito."
            ];
        } catch (\Throwable $th) {
            $database->rollback();
            $_SESSION["notificacion"] = [
                "tipo" => "error",
                "mensaje" => "Ocurrio un error al modificar al revisor... $th"
            ];
        }
    }
}

$database->close();
<?php


$user = getUsuarioData();
$database = getDatabase();

if ($user['id_rol'] === 3 && $_SERVER["REQUEST_METHOD"] === "POST") {
    global $error;
    if (isset($_POST['pass'])) {
        $res = registrarUsuario($_POST);
        if ($res) {
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
            unset($_POST);
        } else {
            $_SESSION["notificacion"] = [
                "tipo" => "error",
                "mensaje" => $error
            ];
        }

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
        } catch (Exception $e) {
            $database->rollback();
            $_SESSION["notificacion"] = [
                "tipo" => "error",
                "mensaje" => "Ocurrio un error al modificar al revisor... $e"
            ];
        }
    } elseif (isset($_POST["revisores"])) {
        try {
            $id_articulo = $_POST['id_articulo'];
            $revisores = explode(',',$_POST['revisores']);
            $revisores_previos = explode(',',$_POST['revisores-previos']);

            $stmt = $database->prepare("
            DELETE FROM Articulos_Revisores
            WHERE id_articulo = ?
            AND rut_revisor = ?
            ");
            foreach ($revisores_previos as $revisor) if (!in_array($revisor,$revisores) && !empty($revisor)) {
                $stmt->bind_param("is",$id_articulo,$revisor);
                $stmt->execute();
            }

            $stmt = $database->prepare("
            CALL asignar_revisor (?,?);
            ");
            foreach ($revisores as $revisor) if (!in_array($revisor,$revisores_previos) && !empty($revisor)) {
                $stmt->bind_param("is",$id_articulo,$revisor);
                $stmt->execute();
            }
            $database->commit();
            
            $_SESSION["notificacion"] = [
                "tipo" => "ok",
                "mensaje" => "Revisor(es) asignado(s)."
            ];
        } catch (Exception $e) {
            $database->rollback();
            $_SESSION["notificacion"] = [
                "tipo" => "error",
                "mensaje" => "Error al modificar revisores.<br>" . $e->getMessage()
            ];
        }
    } elseif (isset($_POST["articulos"])) {
        try {
            $articulos = explode(",",$_POST["articulos"]);
            $articulos_previos = explode(",",$_POST["articulos-previos"]);
            $rut_revisor = $_POST["rut_revisor"];

            $stmt = $database->prepare("
                DELETE FROM Articulos_Revisores
                WHERE rut_revisor = ?
                AND id_articulo = ?
            ");

            foreach ($articulos_previos as $articulo) if (!in_array($articulo,$articulos) && !empty($articulo)) {
                $stmt->bind_param("si",$rut_revisor,$articulo);
                $stmt->execute();
            }
            
            foreach ($articulos as $articulo) if (!in_array($articulo,$articulos_previos) && !empty($articulo)) {
                $stmt = $database->prepare("
                CALL asignar_revisor (?,?)
                ");

                $stmt->bind_param("is", $articulo, $rut_revisor);
                $stmt->execute();
            }

            $database->commit();

            $_SESSION["notificacion"] = [
                "tipo" => "ok",
                "mensaje" => "Articulo(s) asignado(s)."
            ];
        } catch (Exception $e) {
            $database->rollback();
            $_SESSION["notificacion"] = [
                "tipo" => "error",
                "mensaje" => "Error al modificar revisores.<br>" . $e->getMessage()
            ];
        }
    } else if (isset($_POST['eliminar'])) {
        $rut = $_POST['eliminar'];
        try {
            eliminarUsuario($rut);
            $_SESSION["notificacion"] = [
                "tipo" => "ok",
                "mensaje" => "Miembro de comite eliminado con exito."
            ];
        } catch (Exception $e) {
            $_SESSION["notificacion"] = [
                "tipo" => "error",
                "mensaje" => "No se pudo eliminar el miembro de comite:<br>" . $e->getMessage()
            ];
        }
    } else if (isset($_POST['id_articulo_revisor_aleatorio'])) {
        $id_articulo = $_POST['id_articulo_revisor_aleatorio'];
        $revisores_asignados = [];

        try {
            $database->begin_transaction();

            while (true) {
                $stmt = $database->prepare("CALL asignar_revisor_random(?, @rut_revisor)");
                $stmt->bind_param("i", $id_articulo);
                $stmt->execute();
                $stmt->close();

                $result = $database->query("SELECT @rut_revisor AS rut_revisor");
                $revisor = $result->fetch_assoc()['rut_revisor'];

                if ($revisor === null) {
                    break;
                }

                $revisores_asignados[] = $revisor;
            }

            $database->commit();

            if (count($revisores_asignados) > 0) {
                $_SESSION["notificacion"] = [
                    "tipo" => "ok",
                    "mensaje" => "Se asignaron revisores aleatorios: [" . implode(", ", $revisores_asignados) . "]."
                ];
            } else {
                $_SESSION["notificacion"] = [
                    "tipo" => "alerta",
                    "mensaje" => "No se encontraron revisores para asignar."
                ];
            }

        } catch (Exception $e) {
            $database->rollback();
            $_SESSION["notificacion"] = [
                "tipo" => "error",
                "mensaje" => "Ocurrió un error al intentar asignar revisores: " . $e->getMessage()
            ];
        }
    }
}

$database->close();
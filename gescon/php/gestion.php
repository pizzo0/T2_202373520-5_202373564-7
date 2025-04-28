<?php


$user = getUsuarioData();

if ($user['id_rol'] === 3 && $_SERVER["REQUEST_METHOD"] === "POST") {

    if (registrarUsuario($_POST)) {
        $rut_revisor = $_POST['rut'];

        $database = getDatabase();
        $res = $database->query("
        UPDATE Usuarios
        SET id_rol = 2
        WHERE rut = '$rut_revisor'
        ");
        
        $_SESSION["notificacion"] = [
            "tipo" => "ok",
            "mensaje" => "Revisor creado con exito."
        ];
    } elseif ($_POST['tipo'] === "eliminar") {
        eliminarUsuario($_POST['rut_revisor']);
        
        $_SESSION["notificacion"] = [
            "tipo" => "ok",
            "mensaje" => "Revisor eliminado con exito."
        ];
    }
}
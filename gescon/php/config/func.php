<?php

function getDatabase() {
    $database = new mysqli(
        hostname:"db",
        username:"user0",
        password:"123456",
        database:"gescon",
        port:3306
    );
    
    if ($database->connect_error) {
        die("Error: ". $database->connect_error);
    }
    
    // $result = $database->query("SELECT * FROM Usuarios");
    // if ($result->num_rows > 0) {
    //     while($fila = $result->fetch_assoc()) {
    //         echo "RUT: " . $fila["rut"] . " - Nombre: " . $fila["nombre"] . "<br>";
    //     }
    // } else {
    //     echo "No hay resultados.";
    // }
    
    // $database->close();
    
    return $database;
}

function generarPassword() {
    return 1;
}

// obtiene la informacion del usuario actual de la base de datos. si no esta logeado no retorna la informacion
function getUsuarioData() {
    if (isset($_SESSION["userid"])) {
        $database = getDatabase();
        $stmt = $database->prepare("
            SELECT rut,nombre,email FROM Usuarios
            WHERE rut = ?
        ");
        $stmt->bind_param("s", $_SESSION["userid"]);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();

        return $user;
    }

    return null;
}

function getUsuarioDataEmail($email) {
    $database = getDatabase();
    
    $stmt = $database->prepare("
    SELECT rut, nombre, email FROM Usuarios
    WHERE email = ?
    ");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_assoc();
}

function getTopicos() {
    $database = getDatabase();
    $res = $database->query("
    SELECT * FROM Topicos
    ");

    if ($res->num_rows > 0) {
        $topicos = [];
        while ($row = $res->fetch_assoc()) {
            $topicos[] = $row;
        }
    } else {
        $topicos = [];
    }
    return $topicos;
}

function getStyle($css_file) {
    echo "'" . config("styles_path") . "/" . $css_file . ".css'";
}

function getJs($js_file) {
    echo "'" . config("js_path") . "/" . $js_file . ".js'";
}

// obtener el nombre de la pagina
function getNombre() {
    echo config("name");
}

// obtener la url de la pagina
function getUrl() {
    echo config("site_url");
}

function getPagina() {
    return isset($_GET["page"]) ? htmlspecialchars($_GET["page"]) : "index";
}

// obtener redireccion a algun sitio de la pagina
function getRedireccion($uri, $nombre) {
    $query = str_replace("page=", "", $_SERVER["QUERY_STRING"] ?? "");
    $class = $query == $uri ? " active" : "";
    $url = config("site_url") . "/" . (config("pretty_uri") || $uri == "" ? "" : "?page=") . $uri;

    return "<a href='" . $url . "' title='" . $nombre . "' class='item" . $class . "'>" . $nombre . "</a>";
}

// obtener barra de navegacion
function getNav($sep = " | ") {
    $navMain = "";
    $navAuth = "";
    $items = config("nav");

    $user = getUsuarioData();

    foreach ($items as $uri => $nombre) {
        // Parte de autenticación
        if ($uri === "login" || $uri === "signup") {
            if (!$user) {
                $navAuth .= getRedireccion($uri, $nombre) . $sep;
            }
            continue;
        } elseif ($uri === "profile") {
            if ($user) {
                $nombre = explode(" ",$user['nombre'])[0];
                $navAuth .= "<a href='/profile'>" . htmlspecialchars($nombre) . "</a>" . $sep;
            }
            continue;
        }

        // Ítems principales
        $navMain .= getRedireccion($uri, $nombre) . $sep;
    }

    // Imprimimos dos bloques separados
    echo '<div class="sub-nav">' . trim($navMain, $sep) . '</div>';
    echo '<div class="sub-nav">' . trim($navAuth, $sep) . '</div>';
}

// obtener el titulo de la pagina
function getTitulo() {
    
    $pagina = getPagina();
    echo ucwords(str_replace("_"," ",$pagina));
}

// carga el contenido de la pagina en un .view.php. Si no existe contenido, muestra 404.view.php.
function getContenido() {
    $pagina = strtolower(getPagina());
    $path = config("content_path") . "/" . $pagina . ".view.php";

    if (! file_exists($path)) {
        $path = config("content_path") . "/404.view.php";
    }

    require ($path);
}

// carga el php de la pagina. Si no hay php, no se carga nada.
function getPhp() {
    $pagina = strtolower(getPagina());
    $path = config("php_path") . "/" . $pagina . ".php";

    if (! file_exists($path)) {
        return;
    }

    require ($path);
}

function init() {

    require config("template_path") . "/template.php";
    require config("template_path") . "/template.view.php";
}
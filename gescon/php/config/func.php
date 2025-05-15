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
    
    return $database;
}

function generarPassword() {
    // para no complicarse la vida solo retorna 1 como contraseña para la prueba de la pagina
    return 1;
}

function getRolNombre($id_rol) {
    $database = getDatabase();
    $stmt = $database->prepare("
        SELECT nombre FROM Roles
        WHERE id = ?
    ");
    $stmt->bind_param("s", $id_rol);
    $stmt->execute();
    $rol = $stmt->get_result()->fetch_assoc()['nombre'];

    return ucfirst($rol);
}

// obtiene la informacion del usuario actual de la base de datos. si no esta logeado no retorna la informacion
function getUsuarioData() {
    if (isset($_SESSION["userid"])) {
        $database = getDatabase();
        $stmt = $database->prepare("
            SELECT rut,nombre,email,id_rol FROM Usuarios
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
    SELECT rut, nombre, email, id_rol FROM Usuarios
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

function getTopicoNombre($id_topico) {
    $database = getDatabase();
    $res = $database->query("
    SELECT * FROM Topicos
    WHERE id = $id_topico
    ");

    if ($res) {
        $res = $res->fetch_assoc();
        return $res;
    }
}

// obtiene todos los estilos dentro de la carpeta donde estan los css
function getStyles() {
    $styles_dir = config("styles_path");
    $aux_path = rtrim($styles_dir, '/');
    $path = $_SERVER['DOCUMENT_ROOT'] . $aux_path;

    if(is_dir($path)) {
        foreach(scandir($path) as $file) {
            if(pathinfo($file, PATHINFO_EXTENSION) === 'css') {
                echo '<link rel="stylesheet" href="' . $aux_path . '/' . $file . '">' . PHP_EOL;
            }
        }
    }
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

    $svg_name = (($uri == "login" || $uri == "signup") ? "" : ($uri ? $uri : 'home'));
    $svg = ($svg_name != '' ? ("<span class='nav-option-svg'>" . getAsset("/svg/" . $svg_name . ".svg") . "</span>") : "");

    return "<a class='nav-option " . (getPageSection() == $uri ? "nav-option-curr" : "") . "' href='" . $url . "' title='" . $nombre . "' class='item" . $class . "'>" . 
        $svg . "<span class='nav-option-name'>" . $nombre . "</span>" .
    "</a>";
}

// obtener pagina actual
function getPageSection() {
    $uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($uri, PHP_URL_PATH);
    $segments = explode('/', trim($path, '/'));

    return $segments[0] ?? '';
}

// obtener barra de navegacion
function getNav($sep = " | ") {
    $navMain = "";
    $navAuth = "";
    $items = config("nav");

    $user = getUsuarioData();

    foreach ($items as $uri => $nombre) {
        if (in_array($uri, ["gestion", "publicar"])) {
            if (!$user) {
                continue;
            } else if ($uri === "gestion" && $user["id_rol"] != 3) {
                continue;
            }
        } else if (in_array($uri, ["login", "signup"])) {
            if (!$user) {
                $navAuth .= getRedireccion($uri, $nombre) . $sep;
            }
            continue;
        } elseif ($uri === "perfil") {
            if ($user) {
                $nombre = explode(" ",$user['nombre'])[0];
                $navAuth .= "<a class='nav-option " . (getPageSection() == $uri ? "nav-option-curr" : "") . "' href='/" . (config("pretty_uri") || $uri == "" ? "" : "?page=") . $uri ."'>" . 
                    "<span class='nav-option-svg'>" . getAsset("/svg/user.svg") . "</span>" .
                    "<span class='nav-option-name'>" . $nombre . "</span>" .
                "</a>" . $sep;
            }
            continue;
        } else if ($uri === "") {
            $navAuth .= getRedireccion($uri, $nombre) . $sep;
            continue;
        }

        $navMain .= getRedireccion($uri, $nombre) . $sep;
    }

    $navMain .= "<a id='nav-close' class='nav-option menu-option'>" . 
                    "<span class='nav-option-svg'>" . getAsset("/svg/nav.svg") . "</span>" .
                    "<span class='nav-option-name'>Menu</span>" .
                "</a>" . $sep;

    echo '<div class="sub-nav">' . trim($navAuth, $sep) . '</div>';
    echo '<div class="sub-nav">' . trim($navMain, $sep) . '</div>';
}

// obtener el titulo de la pagina
function getTitulo() {
    
    $pagina = str_replace("_"," ",getPagina());
    $pagina = $pagina === 'index' ? 'inicio' : $pagina;
    echo ucwords($pagina);
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

function getAsset($path) {
    $path_asset = config("assets_path") . $path;
    $file = "";
    if (file_exists($path_asset)) $file = file_get_contents($path_asset);
    return $file;
}

function registrarUsuario($postData) {
    global $error;

    // Validar que la confirmación de contraseña coincida
    if ($postData["pass"] !== $postData["pass_confirm"]) {
        $error = "Las contraseñas deben coincidir.";
        return false;
    }

    // no haremos hash para la tarea xd
    // $pass_hash = password_hash($postData["pass"], PASSWORD_DEFAULT);
    $pass_hash = $postData["pass"];
    $database = getDatabase();

    $sql = "
    INSERT INTO Usuarios (rut, nombre, email, password)
    VALUES (?, ?, ?, ?)
    ";

    $stmt = $database->stmt_init();

    if ($stmt->prepare($sql)) {
        $stmt->bind_param("ssss", $postData["rut"], $postData["nombre"], $postData["correo"], $pass_hash);

        try {
            $stmt->execute();
            return true;
        } catch (mysqli_sql_exception $e) {
            if (str_contains($e->getMessage(), 'PRIMARY')) {
                $error = "El RUT ingresado ya está registrado.";
            } elseif (str_contains($e->getMessage(), 'email')) {
                $error = "El correo ingresado ya está registrado.";
            } else {
                $error = $e->getMessage();
            }
            return false;
        }
    } else {
        $error = $stmt->error;
        return false;
    }
}


function eliminarUsuario($rut) {
    $user = getUsuarioData();
    if ($user['rut'] === $rut || $user['id_rol'] === 3) {
        $database = getDatabase();
        $database->query("
        DELETE FROM Usuarios
        WHERE rut = '$rut'
        ");
    }
}

function obtenerTiempo($fecha) {
    $fechaActual = new DateTime();
    $fecha = new DateTime($fecha);
    $dif = $fechaActual->getTimestamp() - $fecha->getTimestamp();

    if ($dif < 60) {
        return $dif . "s";
    } elseif ($dif < 3600) {
        return floor($dif/60) . "m";
    } elseif ($dif < 86400) {
        return floor($dif/3600) . "h";
    } else {
        return $fecha->format('d-m-Y H:i');
    }
}

function obtenerFechaDia($fecha) {
    $fmt_dia = new IntlDateFormatter(
        'es_CL',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE,
        'America/Santiago'
    );
    return $fmt_dia->format($fecha);
}
function obtenerFechaHora($fecha) {
    $fmt_hora = new IntlDateFormatter(
        'es_CL',
        IntlDateFormatter::NONE,
        IntlDateFormatter::SHORT,
        'America/Santiago'
    );
    return $fmt_hora->format($fecha);
}
function init() {
    
    date_default_timezone_set('America/Santiago');
    require config("template_path") . "/template.php";
    require config("template_path") . "/template.view.php";
}
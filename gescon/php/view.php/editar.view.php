<?php


// ARREGLAR ESTO CON LA NUEVA ESTRUCTURA DEL SQL

$tiempo_expiracion = 1 * 60;

$user = getUsuarioData();
$topicos = getTopicos();

$svg_articulo = getAsset("/svg/svg_articulo.svg");

$database = getDatabase();
$database->begin_transaction();

$id_articulo = $_GET['id_articulo'];

$stmt = $database->prepare('
SELECT * FROM articulos_data
WHERE articulo_id = ?
');
$stmt->bind_param('s', $id_articulo);
$stmt->execute();

$articulo = $stmt->get_result()->fetch_assoc();

$pedir_clave = true;
$error_clave = '';

if (!empty($articulo)) {
    if (isset($_SESSION['articulos_verificados'][$id_articulo])) {
        $verificado_info = $_SESSION['articulos_verificados'][$id_articulo];

        if ($verificado_info['verificado'] && (time() - $verificado_info['timestamp']) <= $tiempo_expiracion) {

            $tiempo_restante = $tiempo_expiracion - (time() - $verificado_info['timestamp']);
            $minutos_restantes = floor($tiempo_restante / 60);
            $segundos_restantes = $tiempo_restante % 60;

            $_SESSION["notificacion"] = [
                "tipo" => "alerta",
                "mensaje" => "En " . ($minutos_restantes > 0 ? ($minutos_restantes . " minutos y ") : '') . $segundos_restantes . " segundos deberas verificarte de nuevo.",
            ];

            $pedir_clave = false;
        } else {
            unset($_SESSION['articulos_verificados'][$id_articulo]);
            $pedir_clave = true;
        }
    }

    $es_autor = strpos($articulo['autores'], $user['email']) !== false;
    $autores = explode(', ', $articulo['autores']);

    if ($pedir_clave) {
        $stmt = $database->prepare('
        SELECT password FROM Articulos
        WHERE id = ?
        ');
        
        $stmt->bind_param('s', $id_articulo);
        $stmt->execute();
        $res = $stmt->get_result();

        $pass_articulo = $res->fetch_assoc()['password'];

        if (isset($_POST['pass'])) {
            $pass = $_POST['pass'];

            if (password_verify($pass, $pass_articulo)) {
                $_SESSION['articulos_verificados'][$id_articulo] = [
                    'verificado' => true,
                    'timestamp' => time(),
                ];

                $_SESSION["notificacion"] = [
                    "tipo" => "ok",
                    "mensaje" => "Contraseña correcta. Puedes editar el articulo :)"
                ];

                $pedir_clave = false;
            } else {
                $error_clave = "Clave incorrecta, inténtalo de nuevo.";
            }
        }
    }
}
?>

<?php if (empty($articulo) || $es_autor === false) : ?>
    <?php include '404.view.php'; ?>
<?php elseif ($pedir_clave) : ?>
    <div class="menu big-border-radius">
        <h1>VERIFICACIÓN</h1>
        <p>Para editar este artículo necesitas ingresar la clave.</p>
        <form method="post">
            <div class="">
                <label for="pass">Contraseña</label>
                <input type="password" name="pass" placeholder="Contraseña del artículo" required>
            </div>
            <?php if (!empty($error_clave)) : ?>
                <p class="error" style="display:block;"><?= htmlspecialchars($error_clave) ?></p>
            <?php endif; ?>
            <div class="btns-container">
                <button type="submit">Verificar</button>
                <a class="btn-rojo btn" href='/articulo/<?= $id_articulo ?>'>Ir al articulo</a>
            </div>
        </form>
    </div>
<?php else : ?>
    <div class="menu_publicar big-border-radius">
        <div>
            <h1><span><?= $svg_articulo ?></span> Editar Articulo</h1>
            <p>Aqui puedes editar un articulo que te pertenece o que eres autor de él.</p>
        </div>
        <form method="post" id="form" class="form_publicar formulario">
            <div class="titulo_publicar">
                <label for="titulo">Titulo</label>
                <input type="text" id="titulo" name="titulo" maxlength="255" value="<?= htmlspecialchars($articulo['titulo']) ?>" required>
            </div>
            <div class="resumen_publicar">
                <label for="resumen">Resumen</label>
                <textarea id="resumen" name="resumen" class="input" maxlength="150" required><?= htmlspecialchars($articulo['resumen']) ?></textarea>
            </div>
            <div class="autores_publicar">
                <div>
                    <h1>Autores</h1>
                    <p>Ingresa los autores del artículo. Uno de los autores debes ser tú, por lo que no puedes eliminarte.</p>
                </div>
                <table id="tabla-autores">
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th></th>
                        <th></th>
                    </tr>

                    <?php
                        foreach ($autores as $index => $email) {
                            $usuario = getUsuarioDataEmail($email);
                            $nombre_autor = htmlspecialchars($usuario['nombre']);
                            $email_autor = htmlspecialchars($usuario['email']);
                            $is_contacto = ($email == $articulo['contacto']) ? 'checked' : '';
                    ?>
                        <tr class="autor-info">
                            <td><input type="text" name="nombre[]" value="<?= $nombre_autor ?>" readonly></td>
                            <td><input type="email" name="email[]" value="<?= $email_autor ?>" readonly></td>
                            <td><input type="radio" name="contacto" value="<?= $index ?>" <?= $is_contacto ?>></td>
                            <td><button class="remover" type="button">X</button></td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
                <div id="agregar-autor-form">
                    <button type="button" onclick="buscarYAgregarAutor()">+ Agregar Autor</button>
                    <input type="email" id="nuevo-email" placeholder="correo@ejemplo.com">
                </div>
            </div>

            <div class="topicos_publicar">
                <div class="dropdown" id="dropdown-container">
                    <button type="button" class="dropdown-button" id="dropdown-button">+ Agregar Tópicos</button>
                    <div class="dropdown-menu" id="dropdown-menu">
                        <!-- opciones -->
                    </div>
                </div>
                <div id="topicos-container">
                    <!-- mostrar los tópicos seleccionados -->
                    <?php
                    $topicosSeleccionados = explode(', ', $articulo['topicos']);
                    $idsTopicosSeleccionados = [];

                    $database = getDatabase();

                    foreach ($topicosSeleccionados as $nombreTopico) {
                        $stmt = $database->prepare('SELECT id FROM Topicos WHERE nombre = ?');
                        $stmt->bind_param('s', $nombreTopico);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        
                        if ($row) {
                            $id = $row['id'];
                            $idsTopicosSeleccionados[] = $id;
                            echo "<span class='selected-topic' data-id='$id'>$nombreTopico</span><br>";
                        }
                    }
                    ?>
                </div>
                <input type="hidden" name="topicos" id="hidden-topics" value="<?= htmlspecialchars(implode(',', $idsTopicosSeleccionados)) ?>">
            </div>

            <div class="btn_publicar">
                <button type="submit">Guardar cambios</button>
                <button class="btn-rojo">Eliminar</button>
                <!-- implementar eliminar articulo -->
            </div>
        </form>
        <div>
            <button style="width:100%;" onClick="window.location.href='/articulo/<?= $id_articulo ?>'">Dejar de editar</button>
        </div>
    </div>

    <script src=<?php getJs("inputTopicos"); ?>></script>
    <script src=<?php getJs("inputAutores"); ?>></script>
<?php endif ?>
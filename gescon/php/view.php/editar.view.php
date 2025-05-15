<?php


$tiempo_expiracion = 5 * 60;

$user = getUsuarioData();
$topicos = getTopicos();

$svg_articulo = getAsset("/svg/svg_articulo.svg");

$database = getDatabase();
$database->begin_transaction();

$id_articulo = $_GET['id_articulo'];

$stmt = $database->prepare('
SELECT * FROM articulos_data
WHERE id_articulo = ?
');
$stmt->bind_param('s', $id_articulo);
$stmt->execute();

$articulo = $stmt->get_result()->fetch_assoc();

$pedir_clave = true;
$error_clave = '';

$es_autor = false;

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

    $contacto = json_decode($articulo['contacto'],true);
    $autores = json_decode($articulo['autores'],true);
    foreach ($autores as $autor) {
        $autor_rut = $autor['rut'];
        if ($autor_rut === $user['rut']) {
            $es_autor = true;
        }
    }

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

            if ($pass === $pass_articulo) {
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
$fecha_limite = strtotime($articulo['fecha_limite'] ?? '');

if ($es_autor) {
    $fecha_actual = new DateTime();

    if ($fecha_actual->getTimestamp() > $fecha_limite) {
        $_SESSION['notificacion'] = [
            'tipo' => 'error',
            'mensaje' => 'La fecha limite para modificar tu articulo ya paso :('
        ];
        header("location: /articulo/$id_articulo");
        exit;
    }
}

$fecha_limite_texto = '';
if ($pedir_clave && $es_autor) {
    $fecha_limite_texto = "Tienes hasta el " . obtenerFechaDia($fecha_limite) . " a las " . obtenerFechaHora($fecha_limite) . " para modificar el articulo";
}

?>
<?php if (empty($articulo) || $es_autor === false) : ?>
    <?php include '404.view.php'; ?>
<?php elseif ($pedir_clave) : ?>
    <div class="menu big-border-radius">
        <h1>VERIFICACIÓN</h1>
        <form method="post">
            <p>Ingresa la contraseña enviada al autor de contacto para hacer tus modificaciones.</p>
            <p><?= $fecha_limite_texto ?></p>
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
                        foreach ($autores as $i => $autor) {
                            $nombre_autor = htmlspecialchars($autor['nombre']);
                            $email_autor = htmlspecialchars($autor['email']);
                            $is_contacto = ($autor['rut'] === $contacto['rut']) ? 'checked' : '';
                    ?>
                        <tr class="autor-info">
                            <td><input type="text" name="nombre[]" value="<?= $nombre_autor ?>" readonly></td>
                            <td><input type="email" name="email[]" value="<?= $email_autor ?>" readonly></td>
                            <td><input type="radio" name="contacto" value="<?= $i ?>" <?= $is_contacto ?>></td>
                            <td><button class="remover" <?php 
                            if (!($autor['rut'] === $user['rut'])) {
                                echo 'onclick="eliminarAutor(this)"';
                            }
                            ?> type="button">X</button></td>
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
                <div id="topicos-container-preview">
                    <!-- mostrar los tópicos seleccionados -->
                    <?php
                    $topicosSeleccionados = json_decode($articulo['topicos'],true);
                    $idsTopicosSeleccionados = [];

                    $database = getDatabase();

                    foreach ($topicosSeleccionados as $topico) {
                        $topicoNombre = $topico['nombre'];
                        if ($topico) {
                            $id = $topico['id'];
                            $idsTopicosSeleccionados[] = $id;
                            echo "<span class='etiqueta3' data-id='$id'>$topicoNombre</span><br>";
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="btns-container">
                <button type="submit">Guardar cambios</button>
                <button type="button" class="btn-rojo" id="eliminar-articulo" data-articulo="<?= $id_articulo ?>">Eliminar</button>
            </div>
        </form>
        <div>
            <button style="width:100%;" onClick="window.location.href='/articulo/<?= $id_articulo ?>'">Dejar de editar</button>
        </div>
    </div>

    <script src=<?php getJs("inputAutores"); ?>></script>
    <script src=<?php getJs("eliminarArticulo"); ?>></script>
<?php endif ?>
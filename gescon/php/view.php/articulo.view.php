<?php


$user = getUsuarioData();
$esAutor = false;
$esRevisor = false;

if (isset($_GET['id_articulo'])) {
    $id_articulo = $_GET['id_articulo'];

    $database = getDatabase();
    $stmt = $database->prepare('
        SELECT * FROM articulos_data
        WHERE id_articulo = ?
    ');
    $stmt->bind_param('s', $id_articulo);
    $stmt->execute();

    $articulo = $stmt->get_result()->fetch_assoc();
    if (!empty($articulo) && !empty($user)) {
        foreach (json_decode($articulo['autores'],true) as $autor_data) {
            $autor_rut = $autor_data['rut'];
            if (!$esAutor) {
                $esAutor = $autor_rut === $user['rut'];
            }
        }
    }
    
    $aux = [];
    foreach (json_decode($articulo['autores'],true) as $autor) {
        $aux[] = $autor['nombre'] . ' (' . $autor['email'] . ')';
    }
    $autores = implode(',<br>',$aux);

    $revisores = $articulo['revisores'];
    if (is_null($revisores)) {
        $revisores = 'No hay revisores aun.';
    } else {
        $revisores = json_decode($revisores,true);
        $aux2 = [];
        foreach ($revisores as $revisor) {
            $aux2[] = $revisor['nombre'] . ' (' . $revisor['email'] . ')';
            if (!$esRevisor) {
                $esRevisor = $revisor['rut'] === $user['rut'];
            }
        }
        $revisores = implode(',<br>', $aux2);
    }
    $contacto = json_decode($articulo['contacto'],true);
}
?>
<?php if (empty($articulo)) : ?>
    <?php include '404.view.php'; ?>
<?php else : ?>
    <div class="articulo-container">
        <div class="vista-articulo">
            <h1 class="vista-articulo-titulo"><?= $articulo['titulo'] ?></h1>
            <h2 class="vista-articulo-resumen"><?= $articulo['resumen'] ?></h2>
            <div class="vista-articulo-topicos">
                <?php
                    if (isset($articulo['topicos']) && !empty($articulo['topicos'])) {
                        foreach (json_decode($articulo['topicos'],true) as $topico) {
                            $topico = $topico['nombre'];
                            echo "<span class='etiqueta'>$topico</span><br>";
                        }
                    } else {
                        echo 'No hay revisiones.';
                    }
                ?>
            </div>
            <p class="vista-articulo-subtexto">Contacto:</p>
            <p><?= $contacto['nombre'] . ' (' . $contacto['email'] . ')' ?></p>
            <p class="vista-articulo-subtexto">Autor(es):</p>
            <p><?= $autores ?></p>
            <p class="vista-articulo-subtexto">Revisor(es):</p>
            <p><?= $revisores ?></p>
            <div class="vista-articulo-fecha">
                <p>Publicado: <?= obtenerTiempo($articulo['fecha_envio']) ?></p>
                <?php if (!empty($articulo['fecha_editado'])) :?>
                    <p>Editado: <?= obtenerTiempo($articulo['fecha_editado']) ?></p>
                <?php endif ?>
            </div>
        </div>
        <div class="articulo-acciones">
            <?php if ($esAutor) : ?>
                <button type="button" onClick="window.location.href='/editar/<?=$id_articulo?>'">Editar articulo</button>
            <?php endif ?>
            <?php if ($esRevisor) : ?>
                <button type="button" id="modalBtn" data-target="crear-form">Crear formulario</button>
                <div class="modal" id="crear-form">
                    <div class="modal-content">
                        <form class="crear-formulario formulario" method="POST">
                            <h2>Formulario</h2>
                            <div>
                                <span class="label">Calidad:</span>
                                <div class="input-box">
                                    <?php for ($i = 1; $i <= 7; $i++): ?>
                                        <label class="reset-label input-radio-label" for="calidad_<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                            <input type="radio" id="calidad_<?php echo $i; ?>" name="calidad" value="<?php echo $i; ?>" <?php if ($i == 7) echo 'checked' ?> >
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div>
                            <span class="label">Originalidad:</span>
                                <div class="input-box">
                                <?php for ($i = 1; $i <= 7; $i++): ?>
                                        <label class="reset-label input-radio-label" for="originalidad_<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                            <input type="radio" id="originalidad_<?php echo $i; ?>" name="originalidad" value="<?php echo $i; ?>" <?php if ($i == 7) echo 'checked' ?> >
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div>
                            <span class="label">Valoración:</span>
                                <div class="input-box">
                                <?php for ($i = 1; $i <= 7; $i++): ?>
                                        <label class="reset-label input-radio-label" for="valoracion_<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                            <input type="radio" id="valoracion_<?php echo $i; ?>" name="valoracion" value="<?php echo $i; ?>" <?php if ($i == 7) echo 'checked' ?> >
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div>
                                <label for="argumentos">Argumentos de valoración:</label>
                                <textarea id="argumentos" name="argumentos" class="input" style="min-height:100px;" required></textarea>
                            </div>
                            <div>
                                <label for="comentarios">Comentarios:</label>
                                <textarea id="comentarios" name="comentarios" class="input" style="min-height:100px;"></textarea>
                            </div>
                            <div class="btns-container">
                                <button type="submit">Enviar</button>
                                <button class="btn-rojo" type="button" id="modalBtn" data-target="crear-form">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-overlay" data-overlay-target="crear-form"></div>
            <?php endif ?>
            <?php if ($esAutor || $esRevisor) : ?>
                <div class="revisiones-container">
                    <h2>Revisiones</h2>
                    <div class="formularios-container">
                        <!-- aqui van los formularios -->
                    </div>
                </div>
                <div class="modal" id="consultar-form"></div>
                <div class="modal-overlay" data-overlay-target="consultar-form"></div>
                <script src=<?php getJs("articuloFormularios");?>></script>
            <?php endif ?>
        </div>
    </div>
    <script src=<?php getJs("modal");?>></script>
<?php endif ?>
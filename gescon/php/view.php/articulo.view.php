<?php


$user = getUsuarioData();
$esAutor = false;

if (isset($_GET['id_articulo'])) {
    $id_articulo = $_GET['id_articulo'];

    $database = getDatabase();
    $stmt = $database->prepare('
        SELECT * FROM obtenerArticulosEmail
        WHERE articulo_id = ?
    ');
    $stmt->bind_param('s', $id_articulo);
    $stmt->execute();

    $articulo = $stmt->get_result()->fetch_assoc();
    if (!empty($articulo) && !empty($user)) {
        $esAutor = strpos($articulo['autores'], $user['email']) !== false;
    }
}
?>
<?php if (empty($articulo)) : ?>
    <?php include '404.view.php'; ?>
<?php else : ?>
    <div class="vista-articulo">
        <h1 class="vista-articulo-titulo"><?= $articulo['titulo'] ?></h1>
        <h2 class="vista-articulo-resumen"><?= $articulo['resumen'] ?></h2>
        <div class="vista-articulo-topicos">
            <?php
                if (isset($articulo['topicos']) && !empty($articulo['topicos'])) {
                    $topicos = explode(', ', $articulo['topicos']);
                    foreach ($topicos as $topico) {
                        echo "<span class='etiqueta2'>$topico</span><br>";
                    }
                } else {
                    echo 'No hay tópicos disponibles.';
                }
            ?>
        </div>
        <p class="vista-articulo-subtexto">Contacto: <?= $articulo['contacto'] ?></p>
        <p class="vista-articulo-subtexto">Autor(es): <?= $articulo['autores'] ?></p>
        <p class="vista-articulo-subtexto">Revisor(es):
            <?php
                if (empty($articulo['revisores'])) {
                    echo 'No hay revisores aun.';
                } else {
                    echo $articulo['revisoers'];
                }
            ?>
        </p>
        <div class="vista-articulo-fecha">
            <p>Publicado: <?= $articulo['fecha_envio'] ?></p>
            <?php if (!empty($articulo['fecha_editado'])) :?>
                <p>Editado: <?= $articulo['fecha_editado'] ?></p>
            <?php endif ?>
        </div>
        <?php if ($esAutor) : ?>
            <button onClick="window.location.href='/editar/<?=$id_articulo?>'">Editar articulo</button>
        <?php endif ?>
    </div>
<?php endif ?>
<?php


$user = getUsuarioData();
$esAutor = false;

if (isset($_GET['id_articulo'])) {
    $id_articulo = $_GET['id_articulo'];

    $database = getDatabase();
    $stmt = $database->prepare('
        SELECT * FROM articulos_data
        WHERE articulo_id = ?
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
    foreach (json_decode($articulo['autores'],true) as $autor_data) {
        $aux[] = $autor_data['email'];
    }
    $revisores = $articulo['revisores'];
    if (is_null($revisores)) {
        $revisores = 'No hay revisores aun.';
    } else {
        $revisores = json_decode($revisores,true);
        $aux2 = [];
        foreach ($revisores as $revisor) {
            $aux2[] = $revisor['rut'];
        }
        $revisores = implode(', ', $aux2);
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
                    foreach (json_decode($articulo['topicos'],true) as $topico) {
                        $topico = $topico['nombre'];
                        echo "<span class='etiqueta'>$topico</span><br>";
                    }
                } else {
                    echo 'No hay topicos disponibles.';
                }
            ?>
        </div>
        <p class="vista-articulo-subtexto">Contacto: <?= json_decode($articulo['contacto'],true)['email'] ?></p>
        <p class="vista-articulo-subtexto">Autor(es): <?= implode(', ',$aux); ?></p>
        <p class="vista-articulo-subtexto">Revisor(es): <?= $revisores ?></p>
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
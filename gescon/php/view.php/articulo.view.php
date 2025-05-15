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

    if (!empty($articulo)) {
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
            $aux[] = $autor['nombre'] . ' (' . "<a>" . $autor['email'] . "</a>" . ')';
        }
        $autores = implode(',<br>',$aux);

        $revisores = $articulo['revisores'];
        if (is_null($revisores)) {
            $revisores = 'No hay revisores aun.';
        } else {
            $revisores = json_decode($revisores,true);
            $aux2 = [];
            foreach ($revisores as $revisor) {
                $aux2[] = $revisor['nombre'] . ' (' . "<a>" . $revisor['email'] . "</a>" . ')';
                if (!$esRevisor && $user) {
                    $esRevisor = $revisor['rut'] === $user['rut'];
                }
            }
            $revisores = implode(',<br>', $aux2);
        }
        $contacto = json_decode($articulo['contacto'],true);

        $calidad = $articulo['calidad'];
        $originalidad = $articulo['originalidad'];
        $valoracion = $articulo['valoracion'];

        $yaReviso = false;
        if ($esRevisor && $articulo['formularios']) {
            $formularios = json_decode($articulo['formularios'],true);
            foreach ($formularios as $formulario) {
                $rut_revisor_formulario = $formulario['revisor']['rut'];
                if ($rut_revisor_formulario === $user['rut']) {
                    $yaReviso = true;
                }
            }
        }

        $fecha_limite = strtotime($articulo['fecha_limite']);
        $fecha_actual = new DateTime();

        $sePuedeRevisar = false;
        $fecha_revision_texto = "Puedes evaluar el articulo a partir del " . obtenerFechaDia($fecha_limite) . " a las " . obtenerFechaHora($fecha_limite);
        if ($esRevisor && ($fecha_actual->getTimestamp() > $fecha_limite)) {
            $sePuedeRevisar = true;
        }

        $revisado_icono = getAsset("/svg/revisado.svg");
    }
}
?>
<?php if (empty($articulo)) : ?>
    <?php include '404.view.php'; ?>
<?php else : ?>
    <div class="articulo-container">
        <div class="vista-articulo">
            <h1 class="vista-articulo-titulo"><?= $articulo['revisado'] ? ("<span class='vista-articulo-revisado' title='Revisado'>" . $revisado_icono . "</span>") : '' ?><?= $articulo['titulo'] ?></h1>
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
            <div class="vista-articulo-autores-revisores">
                <p class="vista-articulo-subtexto">Contacto:</p>
                <p class="vista-articulo-subtexto"><?= $contacto['nombre']?> (<a><?= $contacto['email'] ?></a>)</p>
                <p class="vista-articulo-subtexto">Autor(es):</p>
                <p class="vista-articulo-subtexto"><?= $autores ?></p>
                <p class="vista-articulo-subtexto">Revisor(es):</p>
                <p class="vista-articulo-subtexto"><?= $revisores ?></p>
            </div>
            <?php if (!is_null($calidad) && !is_null($originalidad) && !is_null($valoracion)) : ?>
                <div class="vista-articulo-evaluacion">
                    <span class="etiqueta2">Calidad: <?= $calidad ?>/7.0</span>
                    <span class="etiqueta2">Originalidad: <?= $originalidad ?>/7.0</span>
                    <span class="etiqueta2">Valoración: <?= $valoracion ?>/7.0</span>
                </div>
            <?php endif ?>
            <div class="vista-articulo-fecha">
                <p>Publicado: <?= obtenerTiempo($articulo['fecha_envio']) ?></p>
                <?php if (!empty($articulo['fecha_editado'])) :?>
                    <p>Editado: <?= obtenerTiempo($articulo['fecha_editado']) ?></p>
                <?php endif ?>
            </div>
        </div>
    </div>

    <?php include "articulo.componente.view.php"; ?>
<?php endif ?>
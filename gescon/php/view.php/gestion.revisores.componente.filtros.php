<?php


$rut_revisor = isset($_GET['rut_revisor']) ? ("value='" . $_GET['rut_revisor'] . "'") : '';
$nombre_revisor = isset($_GET['nombre_revisor']) ? ("value='" . $_GET['nombre_revisor'] . "'") : '';
$correo_revisor = isset($_GET['correo_revisor']) ? ("value='" . $_GET['correo_revisor'] . "'") : '';
$id_articulo = isset($_GET['id_articulo']) ? ("value='" . $_GET['id_articulo'] . "'") : '';
$id_articulo_asignado = isset($_GET['id_articulo_asignado']) ? ("value='" . $_GET['id_articulo_asignado'] . "'") : '';
$topicos = isset($_GET['topicos']) ? ("value='" . $_GET['topicos'] . "'") : '';
?>

<div>
    <span>Informacion del revisor:</span>
    <br>
    <label for="rut_revisor">Rut:</label>
    <input type="text" name="rut_revisor" id="rut_revisor" <?= $rut_revisor ?>>
</div>
<div>
    <label for="nombre_revisor">Nombre:</label>
    <input type="text" name="nombre_revisor" id="nombre_revisor" <?= $nombre_revisor ?>>
</div>
<div>
    <label for="correo_revisor">Correo:</label>
    <input type="text" name="correo_revisor" id="correo_revisor" <?= $correo_revisor ?>>
</div>
<div>
    <label for="topicos">Topicos:</label>
    <input type="text" name="topicos" id="topicos" <?= $topicos ?>>
</div>
<div>
    <label for="id_articulo">Posible ID Articulo:</label>
    <input type="text" name="id_articulo" id="id_articulo" <?= $id_articulo ?>>
</div>
<div>
    <label for="id_articulo_asignado">ID Articulo asignado:</label>
    <input type="text" name="id_articulo_asignado" id="id_articulo_asignado" <?= $id_articulo_asignado ?>>
</div>
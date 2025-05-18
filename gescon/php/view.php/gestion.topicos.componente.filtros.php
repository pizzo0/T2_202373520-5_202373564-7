<?php


$id_topico = isset($_GET['id_topico']) ? ("value='" . $_GET['id_topico'] . "'") : '';
$nombre_topico = isset($_GET['nombre_topico']) ? ("value='" . $_GET['nombre_topico'] . "'") : '';
?>
<div>
    <span>Informacion del topico:</span>
    <br>
    <label for="id_topico">ID:</label>
    <input type="text" name="id_topico" id="id_topico" <?= $id_topico ?>>
</div>
<div>
    <label for="nombre_topico">Nombre:</label>
    <input type="text" name="nombre_topico" id="nombre_topico" <?= $nombre_topico ?>>
</div>
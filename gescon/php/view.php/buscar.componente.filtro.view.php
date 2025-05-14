<?php


$database = getDatabase();
$res = $database->query("
    SELECT * FROM Topicos
    ORDER BY nombre ASC
");

$id_articulo = isset($_GET['id_articulo']) ? ("value='" . $_GET['id_articulo'] . "'") : '';
$contacto = isset($_GET['contacto']) ? ("value='" . $_GET['contacto'] . "'") : '';
$autor = isset($_GET['autor']) ? ("value='" . $_GET['autor'] . "'") : '';
$revisor = isset($_GET['revisor']) ? ("value='" . $_GET['revisor'] . "'") : '';
$topico = isset($_GET['topicos']) ? $_GET['topicos'] : "";
$fecha_desde = isset($_GET['fecha_desde']) ? ("value='" . $_GET['fecha_desde'] . "'") : '';
$fecha_hasta = isset($_GET['fecha_hasta']) ? ("value='" . $_GET['fecha_hasta'] . "'") : '';
$revisado = isset($_GET['revisado']) ? ((int) $_GET['revisado'] === 1 ? 'checked' : 0) : 'checked';

$filtro_extra = isset($filtro_extra) ? $filtro_extra : false;
if ($filtro_extra) $necesita_revisores = isset($_GET['necesita-revisores']) ? ((int) $_GET['necesita-revisores'] === 1 ? 'checked' : 0) : '';

?>

<div class="filtro-container">
    <form method="GET" id="filtro-form">
        <span id="filtro-num-resultados"><!-- resultados --></span>
        <div>
            <span>Articulo en especifico:</span>
            <br>
            <label for="id_articulo">ID del Articulo:</label>
            <input type="text" name="id_articulo" id="id_articulo" <?= $id_articulo ?>>
        </div>
        <div>
            <span>Información del Articulo:</span>
            <br>
            <label for="contacto">Contacto:</label>
            <input type="text" name="contacto" id="contacto" <?= $contacto ?>>
        </div>
        <div>
            <label for="autor">Autor:</label>
            <input type="text" name="autor" id="autor" <?= $autor ?>>
        </div>
        <div>
            <label for="topicos">Topico:</label>
            <select class="select-input select-input-bigger" name="topicos" id="topicos">
                <option value="">Seleccionar topico</option>
                <?php
                    while ($row = $res->fetch_assoc()) {
                        $id = htmlspecialchars($row["id"]);
                        $nombre = htmlspecialchars($row["nombre"]);
                        $selec = $topico === $id ? "selected" : "";
                        echo "<option value='$id' $selec>$nombre</option>";
                    }
                ?>
            </select>
        </div>
        <div>
            <label for="revisor">Revisor:</label>
            <input type="text" name="revisor" id="revisor" <?= $revisor ?>>
        </div>
        <div>
            <span>Fecha de publicación:</span>
            <br>
            <label for="fecha_desde">Desde:</label>
            <input type="date" name="fecha_desde" id="fecha_desde" <?= $fecha_desde ?>>
            <label for="fecha_hasta">Hasta:</label>
            <input type="date" name="fecha_hasta" id="fecha_hasta" <?= $fecha_hasta ?>>
        </div>
        <span>Sobre el articulo:</span>
        <div class="checkbox-div">
            <label class="checkbox-label" for="revisado">
                <input type="checkbox" class="checkbox" id="revisado" name="revisado" <?= $revisado ?>>
                <span class="checkbox-slider"></span>
            </label>
            <label class="reset-label" for="revisado">Esta revisado</label>
        </div>
        <?php if ($filtro_extra) : ?>
            <div class="checkbox-div">
                <label class="checkbox-label" for="necesita-revisores">
                    <input type="checkbox" class="checkbox" id="necesita-revisores" name="necesita-revisores" <?= $necesita_revisores ?>>
                    <span class="checkbox-slider"></span>
                </label>
                <label class="reset-label" for="necesita-revisores">Necesita revisores</label>
            </div>
        <?php endif ?>
        <br>
        <div class="btns-container">
            <button type="submit" onclick="toggleFC()">Filtrar</button>
            <button type="button" onclick="toggleFC()" class="btn-rojo">Cancelar</button>
        </div>
    </form>
</div>
<div id="filtro-overlay"></div>
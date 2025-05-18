<?php


$database = getDatabase();
$res = $database->query("
    SELECT * FROM Topicos
    ORDER BY nombre ASC
");
$database->close();

$id_articulo = isset($_GET['id_articulo']) ? ("value='" . $_GET['id_articulo'] . "'") : '';
$contacto = isset($_GET['contacto']) ? ("value='" . $_GET['contacto'] . "'") : '';
$autor = isset($_GET['autor']) ? ("value='" . $_GET['autor'] . "'") : '';
$revisor = isset($_GET['revisor']) ? ("value='" . $_GET['revisor'] . "'") : '';
$topico = isset($_GET['topicos']) ? $_GET['topicos'] : "";
$fecha_desde = isset($_GET['fecha_desde']) ? ("value='" . $_GET['fecha_desde'] . "'") : '';
$fecha_hasta = isset($_GET['fecha_hasta']) ? ("value='" . $_GET['fecha_hasta'] . "'") : '';
$revisado = isset($_GET['revisado']) ? ((int) $_GET['revisado'] === 1 ? 'checked' : 0) : '';

$filtro_extra = isset($filtro_extra) ? $filtro_extra : false;
if ($filtro_extra) $necesita_revisores = isset($_GET['necesita-revisores']) ? ((int) $_GET['necesita-revisores'] === 1 ? 'checked' : 0) : '';
?>

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
    <div class="desde-hasta-inputs">
        <div>
            <label for="fecha_desde">Desde:</label>
            <input type="date" name="fecha_desde" id="fecha_desde" <?= $fecha_desde ?>>
        </div>
        <div>
            <label for="fecha_hasta">Hasta:</label>
            <input type="date" name="fecha_hasta" id="fecha_hasta" <?= $fecha_hasta ?>>
        </div>
    </div>
    <script>
        const inputDates = document.querySelectorAll('[type=date]');
        inputDates.forEach(i => {
            i.addEventListener('click', () => {
                if (i.showPicker) {
                i.showPicker();
                } else {
                i.focus();
                }
            });
        });
    </script>
</div>
<div class="input-container-gap">
    <span>Sobre el articulo:</span>
    <div class="switch-sub-container">
        <label class="checkbox-label" for="revisado">
            <input type="checkbox" class="checkbox" id="revisado" name="revisado" <?= $revisado ?>>
            <span class="checkbox-slider"></span>
        </label>
        <label class="reset-label" for="revisado">Esta revisado</label>
    </div>
    <?php if ($filtro_extra) : ?>
        <div class="switch-sub-container">
            <label class="checkbox-label" for="necesita-revisores">
                <input type="checkbox" class="checkbox" id="necesita-revisores" name="necesita-revisores" <?= $necesita_revisores ?>>
                <span class="checkbox-slider"></span>
            </label>
            <label class="reset-label" for="necesita-revisores">Necesita revisores</label>
        </div>
    <?php endif ?>
</div>